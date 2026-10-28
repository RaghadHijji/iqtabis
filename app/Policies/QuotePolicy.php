<?php

namespace App\Policies;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class QuotePolicy
{
    /**
     * Only the owner can edit the quote (FR-06), and only in the first 10 minutes after posting it.
     */
    public function update(User $user, Quote $quote): Response
    {
        if ($user->id !== $quote->user_id) {
            return Response::deny();
        }

        return $quote->created_at->gt(now()->subMinutes(config('moderation.edit_minutes')))
            ? Response::allow()
            : Response::deny(__('Editing closes :minutes minutes after posting.', ['minutes' => config('moderation.edit_minutes')]));
    }

    /**
     * Only the owner can delete the quote (FR-07), or an admin when it breaks the rules (FR-25).
     */
    public function delete(User $user, Quote $quote): bool
    {
        return $user->id === $quote->user_id || $user->is_admin;
    }
}
