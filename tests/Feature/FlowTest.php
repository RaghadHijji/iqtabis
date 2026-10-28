<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Quote;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class FlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(CategorySeeder::class);
        User::factory()->create(['email' => 'test@example.com']);
    }

    public function test_web_flow(): void
    {
        // the site is Arabic by default; this test checks the English texts
        $this->withSession(['locale' => 'en']);
        $this->get('/')->assertOk()->assertSee('Log in and share your quote with us');
        $this->get('/login')->assertRedirect('/?auth=login');
        $this->get('/signup')->assertRedirect('/?auth=signup');
        $this->get('/?auth=login')->assertOk()->assertSee('data-open="login"', false);
        $this->get('/profile')->assertRedirect('/login');
        $this->post('/quotes', ['content' => 'abc'])->assertRedirect('/login');

        // signup validation
        $this->post('/signup', ['name' => 'R4', 'email' => 'bad', 'password' => '123'])
            ->assertSessionHasErrors(['name', 'email', 'password']);
        $this->post('/signup', ['name' => 'Raghad', 'email' => 'test@example.com', 'password' => '12345678', 'password_confirmation' => '12345678'])
            ->assertSessionHasErrors(['email']);

        $this->post('/signup', ['name' => 'رغد Hijji', 'email' => 'r@x.com', 'password' => '12345678', 'password_confirmation' => '12345678'])
            ->assertRedirect('/email/verify');
        $this->assertAuthenticated();
        $u = User::where('email', 'r@x.com')->first();
        $this->assertNotEquals('12345678', $u->password);
        $u->markEmailAsVerified();
        $this->actingAs($u->fresh());

        $cat = Category::where('name', 'Wisdom')->first();
        $this->post('/quotes', ['content' => 'ab', 'category_id' => $cat->id])->assertSessionHasErrors('content');
        $this->post('/quotes', ['content' => 'Knowledge is power', 'category_id' => $cat->id])->assertRedirect();
        $q = Quote::first();
        $this->assertEquals($u->id, $q->user_id);

        $this->get('/')->assertOk()->assertSee('Knowledge is power')->assertSee('رغد Hijji');
        $this->assertCount(1, $this->get('/?filter=Wisdom')->viewData('quotes'));
        $this->assertCount(0, $this->get('/?filter=Love')->viewData('quotes'));
        $this->assertCount(1, $this->get('/?q=power')->viewData('quotes'));
        $this->assertCount(0, $this->get('/?q=zzz')->viewData('quotes'));
        $this->assertEquals('Knowledge is power', $this->get('/')->viewData('quoteOfDay')->content);
        $this->get('/?view=mine')->assertSee('Knowledge is power');
        $this->get('/?sort=liked')->assertOk();
        $this->get('/?edit='.$q->id)->assertOk();

        $this->post("/quotes/{$q->id}/like")->assertRedirect();
        $this->assertEquals(1, $q->likes()->count());
        $this->post("/quotes/{$q->id}/favorite")->assertRedirect();
        $this->get('/?view=favs')->assertSee('Knowledge is power');
        $this->post("/quotes/{$q->id}/like");
        $this->assertEquals(0, $q->likes()->count());

        $this->put("/quotes/{$q->id}", ['content' => 'Knowledge is light', 'category_id' => $cat->id])->assertRedirect();
        $this->assertEquals('Knowledge is light', $q->fresh()->content);

        // another user can't edit/delete
        $other = User::where('email', 'test@example.com')->first();
        $this->actingAs($other)->put("/quotes/{$q->id}", ['content' => 'hacked', 'category_id' => $cat->id])->assertForbidden();
        $this->actingAs($other)->delete("/quotes/{$q->id}")->assertForbidden();
        $this->actingAs($other)->get('/')->assertOk()->assertDontSee('aria-label="Delete"', false);

        $this->actingAs($u)->delete("/quotes/{$q->id}")->assertRedirect();
        $this->assertEquals(0, Quote::count());

        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();

        $this->post('/login', ['email' => 'r@x.com', 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->post('/login', ['email' => 'r@x.com', 'password' => '12345678'])->assertRedirect('/');
        $this->assertAuthenticated();
    }

    public function test_arabic_pages(): void
    {
        $this->withSession(['locale' => 'ar', 'theme' => 'dark'])->get('/')->assertOk()->assertSee('dir="rtl"', false)->assertSee('class="dark"', false);
        $this->withSession(['locale' => 'ar'])->post('/signup', ['name' => '1', 'email' => 'x', 'password' => '1'])->assertSessionHasErrors();
        $errors = session('errors')->get('password')[0];
        echo "\nAR error: $errors\n";
        $this->assertStringContainsString('كلمة المرور', $errors);
        $this->withSession(['locale' => 'ar'])->get('/?auth=signup')->assertOk()->assertSee('إنشاء حساب');
    }

    public function test_api(): void
    {
        $this->getJson('/api/quotes')->assertOk();
        $this->postJson('/api/quotes', ['content' => 'abc'])->assertStatus(401);

        $this->postJson('/api/register', ['name' => 'Raghad', 'email' => 'a@a.com', 'password' => 'short'])->assertStatus(422);
        $token = $this->postJson('/api/register', ['name' => 'Raghad', 'email' => 'a@a.com', 'password' => '12345678'])
            ->assertStatus(201)->json('token');
        $this->assertNotEmpty($token);

        $this->postJson('/api/login', ['email' => 'a@a.com', 'password' => 'nope'])->assertStatus(401);
        $token = $this->postJson('/api/login', ['email' => 'a@a.com', 'password' => '12345678'])->assertOk()->json('token');

        $h = ['Authorization' => "Bearer $token"];
        $this->getJson('/api/user', $h)->assertOk()->assertJsonPath('email', 'a@a.com');
        // sharing waits for a confirmed email
        $this->postJson('/api/quotes', ['content' => 'API quote here', 'category_id' => 1], $h)->assertStatus(403);
        $this->postJson('/api/email/verification-notification', [], $h)->assertStatus(202);
        User::where('email', 'a@a.com')->first()->markEmailAsVerified();
        $this->app['auth']->forgetGuards();
        $this->postJson('/api/quotes', ['content' => 'x'], $h)->assertStatus(422);
        $id = $this->postJson('/api/quotes', ['content' => 'API quote here', 'category_id' => 1], $h)->assertStatus(201)->json('id') ?? $this->postJson('/api/quotes', [], $h)->json();
        $q = Quote::first();
        $this->getJson("/api/quotes/{$q->id}")->assertOk();
        $this->getJson('/api/quotes/999')->assertStatus(404);
        $this->postJson("/api/quotes/{$q->id}/like", [], $h)->assertOk()->assertJson(['liked' => true, 'likes_count' => 1]);
        $this->postJson("/api/quotes/{$q->id}/favorite", [], $h)->assertOk()->assertJson(['saved' => true]);
        $this->getJson('/api/favorites', $h)->assertOk();
        $this->putJson("/api/quotes/{$q->id}", ['content' => 'API quote edited', 'category_id' => 1], $h)->assertOk();

        $other = User::where('email', 'test@example.com')->first()->createToken('api')->plainTextToken;
        $this->app['auth']->forgetGuards();
        $this->deleteJson("/api/quotes/{$q->id}", [], ['Authorization' => "Bearer $other"])->assertStatus(403);
        $this->app['auth']->forgetGuards();
        $this->deleteJson("/api/quotes/{$q->id}", [], $h)->assertSuccessful();
        $this->postJson('/api/logout', [], $h)->assertOk();
        $this->assertEquals(2, PersonalAccessToken::count());
        echo "\nAPI sample: ".json_encode($this->getJson('/api/quotes')->json())."\n";
    }
}
