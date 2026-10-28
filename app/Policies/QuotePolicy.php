<?php

namespace App\Policies;

use App\Models\Quote;
use App\Models\User;

class QuotePolicy
{
    /**
     * Only the owner can edit the quote (FR-06).
     */
    public function update(User $user, Quote $quote): bool
    {
        return $user->id === $quote->user_id;
    }

    /**
     * Only the owner can delete the quote (FR-07), or an admin when it breaks the rules (FR-25).
     */
    public function delete(User $user, Quote $quote): bool
    {
        return $user->id === $quote->user_id || $user->is_admin;
    }
}
