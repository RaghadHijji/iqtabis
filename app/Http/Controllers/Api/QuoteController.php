<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuoteRequest;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class QuoteController extends Controller
{
    /**
     * GET /api/quotes?category=Love&q=life&sort=liked -> 200 (anyone)
     */
    public function index(Request $request)
    {
        $quotes = Quote::with(['user:id,name', 'category:id,name'])
            ->withCount('likes')
            ->visibleTo(null)
            ->when($request->query('category'), fn ($query, $name) => $query->whereRelation('category', 'name', $name))
            ->when($request->query('q'), fn ($query, $search) => $query->where(fn ($q) => $q->where('content', 'like', "%{$search}%")->orWhere('author', 'like', "%{$search}%")))
            ->when($request->query('sort') === 'liked', fn ($query) => $query->orderByDesc('likes_count'))
            ->latest()
            ->get();

        return response()->json($quotes);
    }

    /**
     * GET /api/quotes/{id} -> 200, or 404
     */
    public function show(Quote $quote)
    {
        return response()->json($quote->load(['user:id,name', 'category:id,name'])->loadCount('likes'));
    }

    /**
     * POST /api/quotes -> 201 (needs a token), 422 if the data is wrong
     */
    public function store(StoreQuoteRequest $request)
    {
        $quote = $request->user()->quotes()->create($request->validated());

        return response()->json($quote, 201);
    }

    /**
     * PUT /api/quotes/{id} -> 200, or 403 if it's not your quote
     */
    public function update(StoreQuoteRequest $request, Quote $quote)
    {
        Gate::authorize('update', $quote);

        $quote->update($request->validated());

        return response()->json($quote);
    }

    /**
     * DELETE /api/quotes/{id} -> 200, or 403 if it's not your quote
     */
    public function destroy(Quote $quote)
    {
        Gate::authorize('delete', $quote);

        $quote->delete();

        return response()->json(['message' => 'Quote deleted.']);
    }

    /**
     * POST /api/quotes/{id}/like -> like / unlike
     */
    public function toggleLike(Request $request, Quote $quote)
    {
        $result = $request->user()->likedQuotes()->toggle($quote->id);

        return response()->json([
            'liked' => count($result['attached']) > 0,
            'likes_count' => $quote->likes()->count(),
        ]);
    }

    /**
     * POST /api/quotes/{id}/favorite -> save / unsave
     */
    public function toggleFavorite(Request $request, Quote $quote)
    {
        $result = $request->user()->favoriteQuotes()->toggle($quote->id);

        return response()->json(['saved' => count($result['attached']) > 0]);
    }

    /**
     * POST /api/quotes/{id}/report {"reason": "offensive|politics|spam|other"} -> 201, 403 for your own quote
     */
    public function report(Request $request, Quote $quote)
    {
        abort_if($quote->user_id === $request->user()->id, 403, 'You cannot report your own quote.');

        $data = $request->validate([
            'reason' => ['required', 'in:'.implode(',', array_keys(config('moderation.report_reasons')))],
        ]);

        $request->user()->reportedQuotes()->syncWithoutDetaching([$quote->id => ['reason' => $data['reason']]]);

        return response()->json(['reported' => true], 201);
    }

    /**
     * GET /api/favorites -> my saved quotes
     */
    public function favorites(Request $request)
    {
        return response()->json(
            $request->user()->favoriteQuotes()->with(['user:id,name', 'category:id,name'])->withCount('likes')->get()
        );
    }
}
