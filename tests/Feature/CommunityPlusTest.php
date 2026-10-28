<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Quote;
use App\Models\User;
use App\Notifications\QuoteActivity;
use Database\Seeders\CategorySeeder;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class CommunityPlusTest extends TestCase
{
    use RefreshDatabase;

    private function quoteBy(User $user, string $content = 'Words that stay with you'): Quote
    {
        return $user->quotes()->create(['content' => $content, 'category_id' => Category::first()->id]);
    }

    public function test_quote_has_its_own_page_and_quote_me_opens_a_random_one(): void
    {
        $this->seed(CategorySeeder::class);
        $quote = $this->quoteBy(User::factory()->create());

        $this->get(route('quotes.show', $quote))
            ->assertOk()
            ->assertSee('Words that stay with you')
            ->assertSee('<meta property="og:title" content="«Words that stay with you»">', false)
            ->assertSee('data-image', false);

        $this->get('/random')->assertRedirect(route('quotes.show', $quote));
        $this->get('/random?filter=Love')->assertRedirect(route('home'));

        // a hidden (reported) quote has no page
        $reporters = User::factory()->count(3)->create();
        $reporters->each(fn ($user) => $user->reportedQuotes()->attach($quote->id, ['reason' => 'spam']));
        $this->get(route('quotes.show', $quote))->assertNotFound();
    }

    public function test_users_comment_and_the_owner_is_notified(): void
    {
        $this->seed(CategorySeeder::class);
        $owner = User::factory()->create();
        $reader = User::factory()->create(['name' => 'Noura']);
        $quote = $this->quoteBy($owner);

        // guests can read but not write
        $this->post(route('comments.store', $quote), ['body' => 'Nice'])->assertRedirect('/login');

        $this->actingAs($reader)->post(route('comments.store', $quote), ['body' => 'x'])->assertSessionHasErrors('body');
        $this->actingAs($reader)->post(route('comments.store', $quote), ['body' => 'You are stupid'])->assertSessionHasErrors('body');
        $this->actingAs($reader)->post(route('comments.store', $quote), ['body' => 'This one is beautiful'])
            ->assertRedirect(route('quotes.show', $quote).'#comments');

        $this->get(route('quotes.show', $quote))->assertSee('This one is beautiful')->assertSee('Noura');
        $this->get('/')->assertSee(route('quotes.show', $quote).'#comments', false);

        // the owner gets a notification and opening it marks it as read
        $this->assertSame(1, $owner->unreadNotifications()->count());
        $notification = $owner->notifications()->first();
        $this->assertSame('comment', $notification->data['kind']);
        $this->actingAs($owner)->get('/')->assertSee('علّق على اقتباسك');
        $this->actingAs($owner)->get(route('notifications.show', $notification->id))
            ->assertRedirect(route('quotes.show', $quote).'#comments');
        $this->assertSame(0, $owner->unreadNotifications()->count());

        // the owner of the quote can delete comments on it, others cannot
        $comment = $quote->comments()->first();
        $stranger = User::factory()->create();
        $this->actingAs($stranger)->delete(route('comments.destroy', $comment))->assertForbidden();
        $this->actingAs($owner)->delete(route('comments.destroy', $comment))->assertRedirect();
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_a_like_notifies_the_owner_once_and_not_for_your_own_quote(): void
    {
        $this->seed(CategorySeeder::class);
        $owner = User::factory()->create();
        $fan = User::factory()->create();
        $quote = $this->quoteBy($owner);

        // like, unlike, like again: still one notification
        $this->actingAs($fan)->post(route('quotes.like', $quote));
        $this->actingAs($fan)->post(route('quotes.like', $quote));
        $this->actingAs($fan)->post(route('quotes.like', $quote));
        $this->assertSame(1, $owner->notifications()->count());
        $this->assertInstanceOf(QuoteActivity::class, new QuoteActivity('like', $quote, $fan));

        $this->actingAs($owner)->post(route('quotes.like', $quote));
        $this->assertSame(0, $owner->fresh()->notifications()->where('data->from_id', $owner->id)->count());

        $this->actingAs($owner)->post(route('notifications.read'));
        $this->assertSame(0, $owner->unreadNotifications()->count());
    }

    public function test_new_users_confirm_their_email_before_sharing(): void
    {
        $this->seed(CategorySeeder::class);
        Notification::fake();

        $this->post('/signup', ['name' => 'Lama', 'email' => 'lama@example.com', 'password' => '12345678', 'password_confirmation' => '12345678'])
            ->assertRedirect(route('verification.notice'));
        $user = User::where('email', 'lama@example.com')->first();
        Notification::assertSentTo($user, VerifyEmail::class);

        $this->get(route('verification.notice'))->assertOk()->assertSee('lama@example.com');
        $this->get('/')->assertSee('أكّد إيميلك');
        $this->post('/quotes', ['content' => 'My first quote', 'category_id' => Category::first()->id])
            ->assertRedirect(route('verification.notice'));

        $this->post(route('verification.send'))->assertSessionHas('status');

        $link = URL::temporarySignedRoute('verification.verify', now()->addHour(), ['id' => $user->id, 'hash' => sha1($user->email)]);
        $this->get($link)->assertOk()->assertSee('تم تأكيد بريدك');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());

        // the link also works in a browser where nobody is logged in, and a changed link is refused
        $other = User::factory()->unverified()->create();
        auth()->logout();
        $this->get(URL::temporarySignedRoute('verification.verify', now()->addHour(), ['id' => $other->id, 'hash' => sha1('wrong@example.com')]))->assertForbidden()->assertSee('انتهت صلاحية الرابط');
        $this->get(URL::temporarySignedRoute('verification.verify', now()->addHour(), ['id' => $other->id, 'hash' => sha1($other->email)]))->assertOk();
        $this->assertTrue($other->fresh()->hasVerifiedEmail());
        $this->assertAuthenticatedAs($other); // the confirmed link logs you in, no second log in needed
        auth()->logout();

        // an expired link offers a new one, sent to the account's own email
        $late = User::factory()->unverified()->create();
        $old = URL::temporarySignedRoute('verification.verify', now()->subMinute(), ['id' => $late->id, 'hash' => sha1($late->email)]);
        $this->get($old)->assertForbidden()->assertSee('أرسل الرابط مرة ثانية');
        $this->assertFalse($late->fresh()->hasVerifiedEmail());
        $this->post(route('verification.resend', ['id' => $late->id, 'hash' => sha1($late->email)]))->assertSessionHas('status');
        Notification::assertSentTo($late, VerifyEmail::class);
        $this->post(route('verification.resend', ['id' => $late->id, 'hash' => sha1('wrong@example.com')]));
        Notification::assertSentToTimes($late, VerifyEmail::class, 1);
        $this->actingAs($user->fresh());

        $this->post('/quotes', ['content' => 'My first quote', 'category_id' => Category::first()->id])->assertRedirect();
        $this->assertDatabaseHas('quotes', ['content' => 'My first quote']);
    }

    public function test_the_api_also_waits_for_a_confirmed_email(): void
    {
        $this->seed(CategorySeeder::class);
        Notification::fake();

        $token = $this->postJson('/api/register', ['name' => 'Huda', 'email' => 'huda@example.com', 'password' => '12345678'])
            ->assertCreated()->json('token');
        $user = User::where('email', 'huda@example.com')->first();
        Notification::assertSentTo($user, VerifyEmail::class);

        $quote = ['content' => 'Shared from the API', 'category_id' => Category::first()->id];
        $this->withToken($token)->postJson('/api/quotes', $quote)->assertForbidden();
        $this->withToken($token)->postJson('/api/email/verification-notification')->assertStatus(202);

        $user->markEmailAsVerified();
        $this->app['auth']->forgetGuards(); // the next request reads the user again
        $this->withToken($token)->postJson('/api/quotes', $quote)->assertCreated();
        $this->withToken($token)->postJson('/api/email/verification-notification')->assertOk();
    }

    public function test_only_admins_open_the_dashboard_and_handle_reports(): void
    {
        $this->seed(CategorySeeder::class);
        $owner = User::factory()->create();
        $quote = $this->quoteBy($owner, 'A reported quote');
        User::factory()->count(3)->create()->each(fn ($user) => $user->reportedQuotes()->attach($quote->id, ['reason' => 'spam']));

        $this->actingAs($owner)->get('/admin')->assertForbidden();

        $this->artisan('app:make-admin', ['email' => 'nobody@example.com'])->assertFailed();
        $admin = User::factory()->create(['email' => 'boss@example.com']);
        $this->artisan('app:make-admin', ['email' => 'boss@example.com'])->assertSuccessful();
        $admin->refresh();

        $this->actingAs($admin)->get('/admin')
            ->assertOk()
            ->assertSee('A reported quote')
            ->assertViewHas('stats', fn ($stats) => $stats['users'] === 5 && $stats['reports'] === 3);
        $this->actingAs($admin)->get('/')->assertSee(route('admin.index'), false);

        // "keep it" clears the reports so everyone sees it again
        $this->actingAs($admin)->post(route('admin.restore', $quote))->assertRedirect();
        $this->assertSame(0, $quote->reports()->count());
        $this->get(route('quotes.show', $quote))->assertOk();

        // admins can delete any quote
        $this->actingAs($admin)->delete(route('quotes.destroy', $quote))->assertRedirect();
        $this->assertModelMissing($quote);
    }
}
