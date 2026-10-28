<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
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
