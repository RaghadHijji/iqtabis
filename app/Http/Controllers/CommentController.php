<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Quote;
use App\Notifications\QuoteActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    /**
     * Write a comment under a quote and tell the quote's owner (FR-22, FR-23).
     */
    public function store(StoreCommentRequest $request, Quote $quote)
    {
        $quote->comments()->create([...$request->validated(), 'user_id' => $request->user()->id]);

        QuoteActivity::send('comment', $quote, $request->user());

        return redirect(route('quotes.show', $quote).'#comments')->with('status', __('Your comment was added.'));
    }

    /**
     * Edit your own comment, in the first 10 minutes only. After that it is locked.
     */
    public function update(StoreCommentRequest $request, Comment $comment)
    {
        $check = Gate::inspect('update', $comment);

        if ($check->denied()) {
            abort_unless($request->user()->id === $comment->user_id, 403);

            return redirect(route('quotes.show', $comment->quote_id).'#comments')->with('status', $check->message());
        }

        $comment->update($request->validated());

        return redirect(route('quotes.show', $comment->quote_id).'#comment-'.$comment->id)->with('status', __('Your comment was updated.'));
    }

    /**
     * Delete a comment: its writer, the quote's owner or an admin can.
     */
    public function destroy(Comment $comment)
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        return back()->with('status', __('The comment was deleted.'));
    }

    /**
     * Report a bad comment. It is hidden for me right away, and for everyone after 3 reports.
     */
    public function report(Request $request, Comment $comment)
    {
        abort_if($comment->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'reason' => ['required', 'in:'.implode(',', array_keys(config('moderation.report_reasons')))],
        ]);

        $comment->reports()->syncWithoutDetaching([$request->user()->id => ['reason' => $data['reason']]]);

        return back()->with('status', __('Thanks, the comment was reported and hidden for you.'));
    }
}
