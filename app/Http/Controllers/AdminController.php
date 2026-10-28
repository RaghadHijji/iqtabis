<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Admin dashboard: site numbers, reported quotes and comments, and the newest users (FR-25).
     */
    public function index()
    {
        return view('admin.dashboard', [
            'page' => 'Admin',
            'stats' => [
                'users' => User::count(),
                'quotes' => Quote::count(),
                'comments' => Comment::count(),
                'likes' => DB::table('likes')->count(),
                'reports' => DB::table('reports')->count() + DB::table('comment_reports')->count(),
            ],
            'reported' => Quote::with(['user', 'category', 'reports'])
                ->withCount('reports')
                ->has('reports')
                ->orderByDesc('reports_count')
                ->get(),
            'reportedComments' => Comment::with(['user', 'quote', 'reports'])
                ->withCount('reports')
                ->has('reports')
                ->orderByDesc('reports_count')
                ->get(),
            'topQuotes' => Quote::with('user')->withCount('likes')->orderByDesc('likes_count')->take(5)->get(),
            'newUsers' => User::withCount('quotes')->latest()->take(8)->get(),
            'hideAfter' => config('moderation.hide_after_reports'),
        ]);
    }

    /**
     * The reports were wrong: clear them so the quote shows again for everyone.
     */
    public function restore(Quote $quote)
    {
        $quote->reports()->detach();

        return back()->with('status', __('The quote is back for everyone.'));
    }

    /**
     * The same for a comment: clear its reports so it shows again.
     */
    public function restoreComment(Comment $comment)
    {
        $comment->reports()->detach();

        return back()->with('status', __('The comment is back for everyone.'));
    }
}
