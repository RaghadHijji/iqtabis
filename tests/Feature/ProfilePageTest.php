<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ProfilePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_poster_page_shows_only_their_quotes_and_numbers(): void
    {
        $this->seed(CategorySeeder::class);
        $category = Category::first();
        $sara = User::factory()->create(['name' => 'Sara']);
        $omar = User::factory()->create(['name' => 'Omar']);
        $saraQuote = $sara->quotes()->create(['content' => 'Sara wrote this one', 'category_id' => $category->id]);
        $omar->quotes()->create(['content' => 'Omar wrote that one', 'category_id' => $category->id]);
        $omar->likedQuotes()->attach($saraQuote->id);

        $this->get('/?by='.$sara->id)
            ->assertOk()
            ->assertSee('Sara wrote this one')
            ->assertDontSee('Omar wrote that one')
            ->assertViewHas('posterStats', ['quotes' => 1, 'likes' => 1])
            ->assertSee('اقتباسات Sara');

        // names on the cards link to the poster's page
        $this->get('/')->assertSee('?by='.$sara->id, false);

        $this->get('/?by=999')->assertNotFound();
    }

    public function test_user_can_reset_a_forgotten_password(): void
    {
        $this->seed(CategorySeeder::class);
        Notification::fake();
        $user = User::factory()->create(['email' => 'reem@example.com', 'password' => 'old-password']);

        $this->get('/forgot-password')->assertOk()->assertSee('نسيت كلمة المرور؟');
        $this->get('/?auth=login')->assertSee(route('password.request'), false);

        $this->post('/forgot-password', ['email' => 'reem@example.com'])->assertSessionHas('status');
        // an unknown email gets the same answer, so nobody can find out who has an account
        $this->post('/forgot-password', ['email' => 'nobody@example.com'])->assertSessionHas('status')->assertSessionHasNoErrors();

        $token = null;
        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use (&$token) {
            $token = $notification->token;

            return true;
        });

        $this->get('/reset-password/'.$token.'?email=reem@example.com')->assertOk()->assertSee('reem@example.com');

        // a wrong link does not work
        $this->post('/reset-password', ['token' => 'wrong', 'email' => 'reem@example.com', 'password' => 'new-password', 'password_confirmation' => 'new-password'])
            ->assertSessionHasErrors('email');

        $this->post('/reset-password', ['token' => $token, 'email' => 'reem@example.com', 'password' => 'new-password', 'password_confirmation' => 'new-password'])
            ->assertRedirect(route('home', ['auth' => 'login']));

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
        $this->post('/login', ['email' => 'reem@example.com', 'password' => 'new-password']);
        $this->assertAuthenticatedAs($user);
    }
}
