<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditWindowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
    }

    public function test_a_quote_can_be_edited_for_10_minutes_only(): void
    {
        $owner = User::factory()->create();
        $quote = $owner->quotes()->create(['category_id' => Category::first()->id, 'content' => 'first words']);

        $this->actingAs($owner)->put(route('quotes.update', $quote), ['content' => 'better words', 'category_id' => $quote->category_id])->assertRedirect();
        $this->assertSame('better words', $quote->fresh()->content);

        $this->travel(11)->minutes();

        // the web form says it's locked, the quote stays as it was
        $this->put(route('quotes.update', $quote), ['content' => 'late words', 'category_id' => $quote->category_id])
            ->assertRedirect()->assertSessionHas('status');
        $this->assertSame('better words', $quote->fresh()->content);
        $this->get('/')->assertDontSee('?edit='.$quote->id, false)
            ->assertSee('data-delete="'.route('quotes.destroy', $quote).'"', false);

        // the API refuses it too
        $this->putJson('/api/quotes/'.$quote->id, ['content' => 'late words', 'category_id' => $quote->category_id])->assertForbidden();

        // deleting is still allowed
        $this->delete(route('quotes.destroy', $quote))->assertRedirect();
        $this->assertModelMissing($quote);
    }

    public function test_a_comment_can_be_edited_by_its_writer_for_10_minutes(): void
    {
        $writer = User::factory()->create();
        $other = User::factory()->create();
        $quote = $other->quotes()->create(['category_id' => Category::first()->id, 'content' => 'a quote']);
        $comment = $quote->comments()->create(['user_id' => $writer->id, 'body' => 'nice one']);

        $this->actingAs($other)->put(route('comments.update', $comment), ['body' => 'hacked'])->assertForbidden();

        $this->actingAs($writer)->get(route('quotes.show', $quote))->assertSee('data-comment-form="'.$comment->id.'"', false);
        $this->put(route('comments.update', $comment), ['body' => 'very nice one'])->assertRedirect();
        $this->assertSame('very nice one', $comment->fresh()->body);

        $this->travel(11)->minutes();

        $this->get(route('quotes.show', $quote))->assertDontSee('data-comment-form="'.$comment->id.'"', false);
        $this->put(route('comments.update', $comment), ['body' => 'too late'])->assertSessionHas('status');
        $this->assertSame('very nice one', $comment->fresh()->body);
    }
}
