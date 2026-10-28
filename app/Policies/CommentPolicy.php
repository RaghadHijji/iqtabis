<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    /**
     * Only the writer can edit a comment, and only in the first 10 minutes after writing it.
     */
    public function update(User $user, Comment $comment): Response
    {
        if ($user->id !== $comment->user_id) {
            return Response::deny();
        }

        return $comment->created_at->gt(now()->subMinutes(config('moderation.edit_minutes')))
            ? Response::allow()
            : Response::deny(__('Editing closes :minutes minutes after posting.', ['minutes' => config('moderation.edit_minutes')]));
    }

    /**
     * The writer, the owner of the quote, or an admin can delete a comment (FR-22).
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id
            || $user->id === $comment->quote->user_id
            || $user->is_admin;
    }
}
