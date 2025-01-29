<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Article\ListArticleRequest;
use App\Http\Requests\Article\SearchArticleRequest;
use App\Models\Article;
use Illuminate\Http\JsonResponse;

class ArticleController extends Controller
{
    /**
     * List articles with filters
     */
    public function index(ListArticleRequest $request): JsonResponse
    {
        $defaults = $request->defaults();
        $query = Article::query();

        // Apply filters
        if ($request->filled('source_id')) {
            $query->fromSource($request->source_id);
        }

        if ($request->filled('category_id')) {
            $query->inCategory($request->category_id);
        }

        if ($request->filled('author')) {
            $query->byAuthor($request->author);
        }

        if ($request->filled(['start_date', 'end_date'])) {
            $query->publishedBetween($request->start_date, $request->end_date);
        }

        // Apply sorting
        $sortBy = $request->input('sort_by', $defaults['sort_by']);
        $sortDirection = $request->input('sort_direction', $defaults['sort_direction']);
        $query->orderBy($sortBy, $sortDirection);

        // Paginate results
        $perPage = $request->input('per_page', $defaults['per_page']);
        $articles = $query->with(['source', 'category'])->paginate($perPage);

        return response()->json($articles);
    }

    /**
     * Search articles
     */
    public function search(SearchArticleRequest $request): JsonResponse
    {
        $defaults = $request->defaults();
        $query = Article::search($request->query);

        // Apply filters
        if ($request->filled('source_id')) {
            $query->fromSource($request->source_id);
        }

        if ($request->filled('category_id')) {
            $query->inCategory($request->category_id);
        }

        if ($request->filled('author')) {
            $query->byAuthor($request->author);
        }

        if ($request->filled(['start_date', 'end_date'])) {
            $query->publishedBetween($request->start_date, $request->end_date);
        }

        // Paginate results
        $perPage = $request->input('per_page', $defaults['per_page']);
        $articles = $query->with(['source', 'category'])->paginate($perPage);

        return response()->json($articles);
    }

    /**
     * Get personalized feed for authenticated user
     */
    public function feed(): JsonResponse
    {
        $articles = auth()->user()->getPersonalizedFeed()
            ->with(['source', 'category'])
            ->paginate(15);

        return response()->json($articles);
    }

    /**
     * Get article details
     */
    public function show(Article $article): JsonResponse
    {
        $article->load(['source', 'category']);
        
        return response()->json([
            'article' => $article,
            'is_saved' => auth()->check() ? auth()->user()->hasSavedArticle($article) : false,
        ]);
    }
}
