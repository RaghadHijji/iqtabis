<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_is_locked_for_a_minute_after_five_wrong_tries(): void
    {
        $this->seed(CategorySeeder::class);
        $user = User::factory()->create(['password' => 'secret123']);

        foreach (range(1, 5) as $try) {
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong-pass'])
                ->assertSessionHasErrors('email');
        }

        // the 6th try is blocked, even with the right password, and shows the 429 page
        $this->post('/login', ['email' => $user->email, 'password' => 'secret123'])
            ->assertStatus(429)
            ->assertHeader('Retry-After')
            ->assertSee('التأني');
        $this->assertGuest();

        // after a minute it works again
        $this->travel(61)->seconds();
        $this->post('/login', ['email' => $user->email, 'password' => 'secret123'])->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    public function test_a_right_login_resets_the_wrong_tries(): void
    {
        $this->seed(CategorySeeder::class);
        $user = User::factory()->create(['password' => 'secret123']);

        foreach (range(1, 4) as $try) {
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong-pass']);
        }
        $this->post('/login', ['email' => $user->email, 'password' => 'secret123']);
        $this->post('/logout');

        foreach (range(1, 4) as $try) {
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong-pass'])
                ->assertSessionHasErrors('email');
        }
    }

    public function test_api_login_allows_five_tries_a_minute(): void
    {
        $user = User::factory()->create(['password' => 'secret123']);

        foreach (range(1, 5) as $try) {
            $this->postJson('/api/login', ['email' => $user->email, 'password' => 'wrong-pass'])->assertStatus(401);
        }

        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'secret123'])->assertStatus(429);
    }

    public function test_pages_have_a_link_preview(): void
    {
        $this->seed(CategorySeeder::class);

        $this->get('/')
            ->assertOk()
            ->assertSee('property="og:title"', false)
            ->assertSee('property="og:image" content="'.asset('images/og.jpg').'"', false)
            ->assertSee('name="twitter:card" content="summary_large_image"', false)
            ->assertSee('لكل لحظة اقتباس يشبهها');

        $this->assertFileExists(public_path('images/og.jpg'));
    }
}
