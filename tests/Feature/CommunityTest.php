<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Quote;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityTest extends TestCase
{
    use RefreshDatabase;

    private function quote(User $user, string $text): Quote
    {
        return Quote::forceCreate(['content' => $text, 'category_id' => Category::first()->id, 'user_id' => $user->id]);
    }

    public function test_categories_order_and_names(): void
    {
        $this->seed(CategorySeeder::class);
        $this->assertSame(['Religious', 'Motivation', 'Love', 'Sad', 'Humor', 'Wisdom', 'Poetry', 'Facts'],
            Category::orderBy('position')->pluck('name')->all());

        // Arabic is the default language now
        $this->get('/')->assertOk()->assertSee('lang="ar"', false)->assertSeeInOrder(['ديني', 'تحفيز', 'فكاهي', 'حقائق'])
            ->assertSee('سجّل دخولك وشاركنا اقتباسك')->assertSee('صُنع من رغد حجي');
    }

    public function test_moderation_blocks_politics_and_insults(): void
    {
        $this->seed(CategorySeeder::class);
        $user = User::factory()->create();
        $cat = Category::first()->id;
        $this->actingAs($user);

        foreach (['الحكومة لازم تتغير', 'أنت غبي جدًا', 'والسياسة شي ممل', 'You are an IDIOT', 'please shut up now', 'ٱلْحُكُومَة'] as $bad) {
            $this->from('/')->post('/quotes', ['content' => $bad, 'category_id' => $cat])->assertSessionHasErrors('content');
        }
        $this->assertSame(0, Quote::count());

        // normal quotes still work (words that only contain a bad word inside them are fine)
        foreach (['الحياة جميلة بالأمل', 'كلبس وكلبشة ليست كلمات', 'Stay hungry, stay foolish'] as $ok) {
            $this->from('/')->post('/quotes', ['content' => $ok, 'category_id' => $cat])->assertSessionHasNoErrors();
        }
        $this->assertSame(3, Quote::count());

        // the same rules for the API
        $token = $user->createToken('t')->plainTextToken;
        $this->withToken($token)->postJson('/api/quotes', ['content' => 'انتخابات بكرة', 'category_id' => $cat])->assertStatus(422);
    }

    public function test_likes_tab_and_reports(): void
    {
        $this->seed(CategorySeeder::class);
        $me = User::factory()->create();
        $others = User::factory()->count(3)->create();
        $mine = $this->quote($me, 'my own words here');
        $bad = $this->quote($others[0], 'a quote people will report');
        $good = $this->quote($others[0], 'a lovely quote to like');

        $this->actingAs($me)->post("/quotes/{$good->id}/like");
        $this->get('/?view=likes')->assertOk()->assertViewHas('quotes', fn ($q) => $q->pluck('id')->all() === [$good->id]);

        // can't report my own quote, reason must be valid
        $this->post("/quotes/{$mine->id}/report", ['reason' => 'spam'])->assertForbidden();
        $this->from('/')->post("/quotes/{$bad->id}/report", ['reason' => 'nope'])->assertSessionHasErrors('reason');

        // after I report it, I don't see it any more, others still do
        $this->from('/')->post("/quotes/{$bad->id}/report", ['reason' => 'offensive'])->assertRedirect('/');
        $this->get('/')->assertViewHas('quotes', fn ($q) => ! $q->contains('id', $bad->id));
        $this->actingAs($others[1])->get('/')->assertViewHas('quotes', fn ($q) => $q->contains('id', $bad->id));

        // after 3 reports it is hidden for everyone (also in the API)
        $this->actingAs($others[1])->post("/quotes/{$bad->id}/report", ['reason' => 'politics']);
        $this->actingAs($others[2])->post("/quotes/{$bad->id}/report", ['reason' => 'other']);
        $this->post('/logout');
        $this->get('/')->assertViewHas('quotes', fn ($q) => ! $q->contains('id', $bad->id));
        $this->assertNotContains($bad->id, collect($this->getJson('/api/quotes')->assertOk()->json())->pluck('id')->all());

        // reporting twice keeps one report
        $this->actingAs($me)->post("/quotes/{$bad->id}/report", ['reason' => 'spam']);
        $this->assertSame(3, $bad->reports()->count());

        // API report
        $token = $others[1]->createToken('t')->plainTextToken;
        $this->withToken($token)->postJson("/api/quotes/{$good->id}/report", ['reason' => 'spam'])->assertCreated();
    }
}
