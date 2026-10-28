<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['body', 'user_id'])]
class Comment extends Model
{
    /**
     * The user who wrote the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The quote the comment is on.
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * Reports on this comment (comment_reports table).
     */
    public function reports(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'comment_reports')->withPivot('reason')->withTimestamps();
    }

    /**
     * Hide comments that got too many reports, and the ones I reported myself (same rule as quotes).
     */
    public function scopeVisibleTo(Builder $query, ?User $user): void
    {
        $query->has('reports', '<', config('moderation.hide_after_reports'))
            ->when($user, fn ($q) => $q->whereDoesntHave('reports', fn ($r) => $r->where('users.id', $user->id)));
    }
}
