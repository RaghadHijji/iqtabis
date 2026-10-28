<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuoteRequest;
use App\Models\Category;
use App\Models\Quote;
use App\Models\User;
use App\Notifications\QuoteActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class QuoteController extends Controller
{
    /**
     * Home page: everyone's quotes with filter, search and views (FR-01).
     */
    public function index(Request $request)
    {
        $filter = $request->query('filter');
        $search = trim((string) $request->query('q', ''));
        $view = in_array($request->query('view'), ['favs', 'likes', 'mine']) ? $request->query('view') : null;
        $sort = $request->query('sort') === 'liked' ? 'liked' : null;
        $user = $request->user();

        // ?by=5 shows one poster's page: their quotes and numbers (FR-21)
        $poster = $request->filled('by') ? User::findOrFail($request->integer('by')) : null;

        // guests can't have saved quotes, likes or "my quotes"
        if (! $user) {
            $view = null;
        }

        $quotes = Quote::with(['user', 'category'])
            ->withCount(['likes', 'comments' => fn ($query) => $query->visibleTo($user)])
            ->visibleTo($user)
            ->when($filter, fn ($query) => $query->whereRelation('category', 'name', $filter))
            ->when($search !== '', fn ($query) => $query->where(fn ($q) => $q->where('content', 'like', "%{$search}%")->orWhere('author', 'like', "%{$search}%")))
            ->when($view === 'favs', fn ($query) => $query->whereRelation('favoritedBy', 'users.id', $user->id))
            ->when($view === 'likes', fn ($query) => $query->whereRelation('likes', 'users.id', $user->id))
            ->when($view === 'mine', fn ($query) => $query->where('user_id', $user->id))
            ->when($poster, fn ($query) => $query->where('user_id', $poster->id))
            ->when($sort === 'liked', fn ($query) => $query->orderByDesc('likes_count'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        // quote of the day: the same quote all day, a different one tomorrow
        $total = Quote::visibleTo($user)->count();
        $quoteOfDay = $total
            ? Quote::with(['user', 'category'])->withCount('likes')->visibleTo($user)->orderBy('id')
                ->skip(crc32(today()->toDateString()) % $total)->first()
            : null;

        // trending: the 3 most liked quotes, from the chosen category if there is one
        $trending = Quote::with(['user', 'category'])->withCount('likes')->visibleTo($user)
            ->when($filter, fn ($query) => $query->whereRelation('category', 'name', $filter))
            ->orderByDesc('likes_count')->latest()->take(3)->get()
            ->filter(fn ($quote) => $quote->likes_count > 0);

        // the names for the moving strip under the opening screen
        $authors = Quote::visibleTo($user)->whereNotNull('author')->where('author', '!=', '')
            ->distinct()->orderBy('author')->limit(16)->pluck('author');

        $posterStats = $poster ? [
            'quotes' => Quote::visibleTo($user)->where('user_id', $poster->id)->count(),
            'likes' => DB::table('likes')->whereIn('quote_id', $poster->quotes()->select('id'))->count(),
        ] : null;

        return view('welcome', [
            'page' => 'Home',
            'quotes' => $quotes,
            'categories' => Category::withCount(['quotes' => fn ($query) => $query->visibleTo($user)])->orderBy('position')->get(),
            'filter' => $filter,
            'search' => $search,
            'view' => $view,
            'sort' => $sort,
            'quoteOfDay' => $quoteOfDay,
            'trending' => $trending,
            'authors' => $authors,
            'likedIds' => $user ? $user->likedQuotes()->pluck('quotes.id')->all() : [],
            'favIds' => $user ? $user->favoriteQuotes()->pluck('quotes.id')->all() : [],
            'totalQuotes' => $total,
            'poster' => $poster,
            'posterStats' => $posterStats,
        ]);
    }

    /**
     * One quote on its own page, with its comments (FR-22).
     * It has its own link, so sharing it shows the quote in the preview.
     */
    public function show(Request $request, Quote $quote)
    {
        $user = $request->user();
        abort_unless(Quote::visibleTo($user)->whereKey($quote->id)->exists(), 404);

        $quote->load(['user', 'category', 'comments' => fn ($query) => $query->visibleTo($user)->with('user')])->loadCount('likes');

        // more quotes from the same category, the most liked first
        $similar = Quote::with(['user', 'category'])->withCount('likes')->visibleTo($user)
            ->where('category_id', $quote->category_id)->whereKeyNot($quote->id)
            ->orderByDesc('likes_count')->latest()->take(3)->get();

        return view('quotes.show', [
            'quote' => $quote,
            'similar' => $similar,
            'liked' => $user && $user->likedQuotes()->whereKey($quote->id)->exists(),
            'saved' => $user && $user->favoriteQuotes()->whereKey($quote->id)->exists(),
        ]);
    }

    /**
     * "Quote me": open a random quote, from one category if one is chosen.
     */
    public function random(Request $request)
    {
        $quote = Quote::visibleTo($request->user())
            ->when($request->query('filter'), fn ($query, $filter) => $query->whereRelation('category', 'name', $filter))
            ->inRandomOrder()
            ->first();

        if (! $quote) {
            return redirect()->route('home')->with('status', __('No quotes found.'));
        }

        return redirect()->route('quotes.show', $quote);
    }

    /**
     * Share a new quote (FR-05).
     */
    public function store(StoreQuoteRequest $request)
    {
        $request->user()->quotes()->create($request->validated());

        return back()->with('status', __('Your quote is shared.'));
    }

    /**
     * Edit a quote, only the owner can, in the first 10 minutes (FR-06).
     */
    public function update(StoreQuoteRequest $request, Quote $quote)
    {
        $check = Gate::inspect('update', $quote);

        // after 10 minutes the quote is locked: say so instead of an error page
        if ($check->denied()) {
            abort_unless($request->user()->id === $quote->user_id, 403);

            return redirect()->route('home', $request->only(['filter', 'q', 'view', 'sort', 'page']))
                ->with('status', $check->message());
        }

        $quote->update($request->validated());

        return redirect()->route('home', $request->only(['filter', 'q', 'view', 'sort', 'page']))
            ->with('status', __('Your quote was updated.'));
    }

    /**
     * Delete a quote, only the owner can (FR-07).
     */
    public function destroy(Quote $quote)
    {
        Gate::authorize('delete', $quote);

        $quote->delete();

        // the quote's own page is gone now, so go home from there
        $back = url()->previous() === route('quotes.show', $quote) ? redirect()->route('home') : back();

        return $back->with('status', __('Your quote was deleted.'));
    }

    /**
     * Like / unlike a quote.
     */
    public function toggleLike(Request $request, Quote $quote)
    {
        $changes = $request->user()->likedQuotes()->toggle($quote->id);

        // tell the owner, only when it was a new like (FR-23)
        if ($changes['attached']) {
            QuoteActivity::send('like', $quote, $request->user());
        }

        return back();
    }

    /**
     * Report a bad quote once, with a reason (FR-15).
     * After enough reports it is hidden for everyone; the reporter stops seeing it right away.
     */
    public function report(Request $request, Quote $quote)
    {
        abort_if($quote->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'reason' => ['required', 'in:'.implode(',', array_keys(config('moderation.report_reasons')))],
        ]);

        $request->user()->reportedQuotes()->syncWithoutDetaching([$quote->id => ['reason' => $data['reason']]]);

        return back()->with('status', __('Thanks, the quote was reported and hidden for you.'));
    }

    /**
     * Save / unsave a quote in my favorites.
     */
    public function toggleFavorite(Request $request, Quote $quote)
    {
        $request->user()->favoriteQuotes()->toggle($quote->id);

        return back();
    }
}
