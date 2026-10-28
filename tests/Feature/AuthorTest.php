<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Quote;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_quotes_have_authors_and_show_on_home(): void
    {
        $this->seed();

        $this->assertSame(14, Quote::count());
        $this->assertSame(0, Quote::whereNull('author')->count());

        // seeding twice adds nothing new
        $this->seed();
        $this->assertSame(14, Quote::count());

        $this->get('/')->assertOk()->assertSee('المتنبي');

        // search finds quotes by who said it too
        $this->get('/?q=الشافعي')->assertOk()->assertSee('وعين الرضا');
        $this->getJson('/api/quotes?q=Einstein')->assertOk()->assertJsonCount(1);
    }

    public function test_user_can_share_a_quote_with_who_said_it(): void
    {
        $this->seed(CategorySeeder::class);
        $user = User::factory()->create();
        $category = Category::first();

        $this->actingAs($user)->post('/quotes', [
            'category_id' => $category->id,
            'content' => 'العلم نور',
            'author' => 'مثل عربي',
        ])->assertRedirect();

        $this->assertDatabaseHas('quotes', ['content' => 'العلم نور', 'author' => 'مثل عربي']);
        $this->get('/')->assertSee('— مثل عربي');

        // too long a name is refused
        $this->post('/quotes', [
            'category_id' => $category->id,
            'content' => 'اقتباس آخر',
            'author' => str_repeat('a', 81),
        ])->assertSessionHasErrors('author');

        // the API takes it too
        $token = $user->createToken('t')->plainTextToken;
        $this->app['auth']->forgetGuards();
        $this->withToken($token)->postJson('/api/quotes', [
            'category_id' => $category->id,
            'content' => 'Stay hungry, stay foolish.',
            'author' => 'Steve Jobs',
        ])->assertCreated()->assertJsonFragment(['author' => 'Steve Jobs']);
    }
}
