<?php

namespace App\Notifications;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

/**
 * Tells a user that someone liked or commented on their quote (FR-23).
 * Saved in the notifications table and shown under the bell in the navbar.
 */
class QuoteActivity extends Notification
{
    /**
     * @param  'like'|'comment'  $kind
     */
    public function __construct(public string $kind, public Quote $quote, public User $from) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array{kind: string, quote_id: int, quote: string, from_id: int, from: string}
     */
    public function toArray(object $notifiable): array
    {
        return [
            'kind' => $this->kind,
            'quote_id' => $this->quote->id,
            'quote' => Str::limit($this->quote->content, 50),
            'from_id' => $this->from->id,
            'from' => $this->from->name,
        ];
    }

    /**
     * Send it, but not to yourself, and only once per person for each like.
     */
    public static function send(string $kind, Quote $quote, User $from): void
    {
        $owner = $quote->user;

        if ($owner->id === $from->id) {
            return;
        }

        $alreadySent = $kind === 'like' && $owner->notifications()
            ->where('data->kind', 'like')
            ->where('data->quote_id', $quote->id)
            ->where('data->from_id', $from->id)
            ->exists();

        if (! $alreadySent) {
            $owner->notify(new self($kind, $quote, $from));
        }
    }
}
