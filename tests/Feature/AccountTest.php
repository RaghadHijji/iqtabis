<?php

namespace Tests\Feature;

use App\Models\Quote;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_settings(): void
    {
        $this->seed(CategorySeeder::class);
        $user = User::factory()->create(['name' => 'Raghad', 'email' => 'r@x.com', 'password' => '12345678']);
        User::factory()->create(['email' => 'taken@x.com']);

        // the pop-up is on the page for logged-in users only
        $this->get('/')->assertDontSee('id="account-modal"', false);
        $this->actingAs($user)->get('/')->assertSee('id="account-modal"', false)->assertSee('data-account-open', false);

        // email: wrong password, taken email, then ok
        $this->from('/')->put('/profile/email', ['email' => 'new@x.com', 'current_password' => 'wrong'])->assertSessionHasErrors('current_password');
        $this->from('/')->put('/profile/email', ['email' => 'taken@x.com', 'current_password' => '12345678'])->assertSessionHasErrors('email');
        $this->from('/')->put('/profile/email', ['email' => 'new@x.com', 'current_password' => '12345678'])->assertRedirect('/')->assertSessionHas('account_status');
        $this->assertSame('new@x.com', $user->fresh()->email);

        // password: confirmation must match
        $this->from('/')->put('/profile/password', ['current_password' => '12345678', 'password' => 'abcdefgh', 'password_confirmation' => 'nope'])->assertSessionHasErrors('password');
        $this->from('/')->put('/profile/password', ['current_password' => '12345678', 'password' => 'abcdefgh', 'password_confirmation' => 'abcdefgh'])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('abcdefgh', $user->fresh()->password));

        // the pop-up opens again with the message after saving
        $this->withSession(['account_status' => 'Password updated.', 'account_section' => 'account-password'])->get('/')->assertSee('data-open="1"', false);

        // delete: wrong password keeps the account, right password removes it and its quotes
        $q = Quote::forceCreate(['content' => 'my quote here', 'category_id' => 1, 'user_id' => $user->id]);
        $this->assertSame(1, Quote::where('user_id', $user->id)->count());
        $this->from('/')->delete('/profile', ['delete_password' => 'wrong'])->assertSessionHasErrors('delete_password');
        $this->assertNotNull($user->fresh());
        $this->from('/')->delete('/profile', ['delete_password' => 'abcdefgh'])->assertRedirect('/');
        $this->assertGuest();
        $this->assertNull(User::find($user->id));
        $this->assertSame(0, Quote::where('user_id', $user->id)->count());

        // Arabic messages
        $other = User::factory()->create(['password' => '12345678']);
        $this->actingAs($other)->withSession(['locale' => 'ar'])->from('/')
            ->put('/profile/password', ['current_password' => 'x', 'password' => '1', 'password_confirmation' => '2'])
            ->assertSessionHasErrors(['current_password' => 'كلمة المرور غير صحيحة.']);
    }
}
