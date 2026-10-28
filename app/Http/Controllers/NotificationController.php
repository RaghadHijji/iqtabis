<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Open a notification: mark it as read and go to its quote (FR-23).
     */
    public function show(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $anchor = $notification->data['kind'] === 'comment' ? '#comments' : '';

        return redirect(route('quotes.show', $notification->data['quote_id']).$anchor);
    }

    /**
     * Mark all my notifications as read.
     */
    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}
