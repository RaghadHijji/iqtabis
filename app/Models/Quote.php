<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['content', 'author', 'category_id'])]
class Quote extends Model
{
    /**
     * The user who shared the quote.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Users who liked this quote (likes table).
     */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }

    /**
     * Comments under this quote, oldest first (FR-22).
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->oldest();
    }

    /**
     * Reports on this quote (reports table, FR-15).
     */
    public function reports(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'reports')->withPivot('reason')->withTimestamps();
    }

    /**
     * Hide quotes that got too many reports, and the ones I reported myself.
     */
    public function scopeVisibleTo(Builder $query, ?User $user): void
    {
        $query->has('reports', '<', config('moderation.hide_after_reports'))
            ->when($user, fn ($q) => $q->whereDoesntHave('reports', fn ($r) => $r->where('users.id', $user->id)));
    }

    /**
     * Users who saved this quote in their favorites (favorites table).
     */
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }
}
