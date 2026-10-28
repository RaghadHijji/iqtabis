<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Quote;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PolishTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(CategorySeeder::class);
    }

    public function test_quotes_come_in_pages_of_twelve(): void
    {
        $user = User::factory()->create();
        foreach (range(1, 14) as $i) {
            Quote::forceCreate(['user_id' => $user->id, 'category_id' => Category::first()->id, 'content' => "quote number {$i}"]);
        }

        $this->get('/')->assertOk()->assertSee('?page=2', false)
            ->assertViewHas('quotes', fn ($quotes) => $quotes->count() === 12 && $quotes->total() === 14);
        $this->get('/?page=2')->assertOk()
            ->assertViewHas('quotes', fn ($quotes) => $quotes->count() === 2 && $quotes->currentPage() === 2);
    }

    public function test_delete_asks_first_and_actions_show_a_message(): void
    {
        $user = User::factory()->create(['name' => 'Raghad']);
        $quote = Quote::forceCreate(['user_id' => $user->id, 'category_id' => Category::first()->id, 'content' => 'mine to delete']);

        $this->actingAs($user)->withSession(['locale' => 'en']);
        $this->get('/')->assertSee('data-delete="'.route('quotes.destroy', $quote).'"', false)->assertSee('delete-dialog');

        $this->post('/quotes', ['category_id' => Category::first()->id, 'content' => 'a brand new quote'])
            ->assertSessionHas('status', 'Your quote is shared.');
        $this->delete(route('quotes.destroy', $quote))->assertSessionHas('status', 'Your quote was deleted.');
        $this->get('/')->assertDontSee('mine to delete');
    }

    public function test_each_error_has_its_own_page_and_quote(): void
    {
        Route::middleware('web')->get('/_test-error/{code}', fn (int $code) => abort($code));

        foreach ([401 => 'دق الباب', 403 => 'لا يعنيه', 419 => 'كالسيف', 429 => 'التأني', 500 => 'تسلم الجرة', 503 => 'مفتاح الفرج'] as $code => $words) {
            $this->get("/_test-error/{$code}")->assertStatus($code)->assertSee((string) $code)->assertSee($words);
        }
    }

    public function test_missing_page_uses_our_404(): void
    {
        $this->get('/no-such-page')->assertNotFound()->assertSee('الصفحة غير موجودة')->assertSee('favicon.png', false);
    }

    public function test_dates_also_show_in_hijri(): void
    {
        if (! class_exists(\IntlDateFormatter::class)) {
            $this->markTestSkipped('The intl extension is off.');
        }

        $this->travelTo('2026-10-24 12:00:00');
        $quote = User::factory()->create()->quotes()->create(['category_id' => Category::first()->id, 'content' => 'a dated quote']);

        $this->get('/')->assertOk()->assertSee('السبت 13 جمادى الأولى 1448 هـ')->assertSee('24 أكتوبر 2026');
        $this->get(route('quotes.show', $quote))->assertOk()->assertSee('2026-10-24 · 13 جمادى الأولى 1448 هـ');
    }

    public function test_inner_pages_have_a_back_link(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $quote = $admin->quotes()->create(['category_id' => Category::first()->id, 'content' => 'a quote with its page']);

        $this->get(route('quotes.show', $quote))->assertOk()->assertSee('data-back', false)->assertSee('رجوع');
        $this->get(route('password.request'))->assertOk()->assertSee('data-back', false);
        $this->actingAs($admin)->get(route('admin.index'))->assertOk()->assertSee('data-back', false);
    }

    public function test_privacy_and_terms_pages_are_linked_from_the_footer(): void
    {
        $this->get('/')->assertOk()->assertSee(route('privacy'), false)->assertSee(route('terms'), false);
        $this->get(route('privacy'))->assertOk()->assertSee('الخصوصية')->assertSee('مشفّرة');
        $this->get(route('terms'))->assertOk()->assertSee('الشروط')->assertSee('3 بلاغات');
    }

    public function test_deleting_the_account_also_deletes_its_notifications(): void
    {
        $owner = User::factory()->create(['password' => 'password']);
        $fan = User::factory()->create();
        $quote = $owner->quotes()->create(['category_id' => Category::first()->id, 'content' => 'liked by a fan']);
        $this->actingAs($fan)->post(route('quotes.like', $quote));
        $this->assertSame(1, $owner->notifications()->count());

        $this->actingAs($owner)->delete(route('account.destroy'), ['delete_password' => 'password']);

        $this->assertDatabaseCount('notifications', 0);
    }

    public function test_trending_follows_the_chosen_category(): void
    {
        $fan = User::factory()->create();
        $love = Category::where('name', 'Love')->first();
        $sad = Category::where('name', 'Sad')->first();
        $loved = $fan->quotes()->create(['category_id' => $love->id, 'content' => 'a liked love quote']);
        $sadOne = $fan->quotes()->create(['category_id' => $sad->id, 'content' => 'a liked sad quote']);
        $loved->likes()->attach($fan);
        $sadOne->likes()->attach($fan);

        $this->get('/?filter=Love')->assertOk()
            ->assertSee('id="trending"', false)
            ->assertSeeInOrder(['id="trending"', 'a liked love quote', 'id="quotes"'], false)
            ->assertDontSee('a liked sad quote');
    }

    public function test_signup_asks_for_the_password_twice(): void
    {
        $this->post('/signup', ['name' => 'Lama', 'email' => 'lama@example.com', 'password' => '12345678', 'password_confirmation' => '87654321'])
            ->assertSessionHasErrors('password');
        $this->assertGuest();
        $this->get('/')->assertSee('name="password_confirmation"', false);
    }

    public function test_remember_me_keeps_you_logged_in(): void
    {
        $user = User::factory()->create(['email' => 'r@x.com', 'password' => 'password']);
        $this->get('/')->assertSee('name="remember"', false);

        $response = $this->post('/login', ['email' => 'r@x.com', 'password' => 'password', 'remember' => '1']);

        $this->assertAuthenticatedAs($user);
        $response->assertCookie(auth()->guard()->getRecallerName());
    }

    public function test_a_quote_page_shows_similar_quotes_from_its_category(): void
    {
        $user = User::factory()->create();
        $love = Category::where('name', 'Love')->first();
        $quote = $user->quotes()->create(['category_id' => $love->id, 'content' => 'the main love quote']);
        $user->quotes()->create(['category_id' => $love->id, 'content' => 'another love quote']);
        $user->quotes()->create(['category_id' => Category::where('name', 'Sad')->first()->id, 'content' => 'a sad quote']);

        $this->get(route('quotes.show', $quote))->assertOk()
            ->assertSee('another love quote')
            ->assertDontSee('a sad quote');
    }

    public function test_reported_comments_are_hidden_and_reach_the_admin(): void
    {
        $writer = User::factory()->create();
        $quote = $writer->quotes()->create(['category_id' => Category::first()->id, 'content' => 'a quote with comments']);
        $comment = $quote->comments()->create(['user_id' => $writer->id, 'body' => 'a rude comment']);

        // you can't report your own comment
        $this->actingAs($writer)->post(route('comments.report', $comment), ['reason' => 'offensive'])->assertForbidden();

        // the reporter stops seeing it right away, the others still see it
        $reporter = User::factory()->create();
        $this->actingAs($reporter)->post(route('comments.report', $comment), ['reason' => 'offensive'])->assertRedirect();
        $this->actingAs($reporter)->get(route('quotes.show', $quote))->assertDontSee('a rude comment');
        $this->actingAs($writer)->get(route('quotes.show', $quote))->assertSee('a rude comment');

        // after 3 reports it is hidden for everyone, and the admin sees it
        foreach (User::factory(2)->create() as $user) {
            $this->actingAs($user)->post(route('comments.report', $comment), ['reason' => 'spam']);
        }
        $this->actingAs($writer)->get(route('quotes.show', $quote))->assertDontSee('a rude comment');

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get(route('admin.index'))->assertOk()->assertSee('a rude comment');

        // the comment count on the home page leaves hidden comments out
        $this->actingAs($writer)->get('/')->assertViewHas('quotes', fn ($quotes) => $quotes->firstWhere('id', $quote->id)->comments_count === 0);

        // "keep it" brings it back
        $this->actingAs($admin)->post(route('admin.comments.restore', $comment))->assertRedirect();
        $this->actingAs($writer)->get(route('quotes.show', $quote))->assertSee('a rude comment');
    }

    public function test_pages_send_security_headers(): void
    {
        $this->get('/')->assertOk()
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }
}
