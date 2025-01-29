<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SavedArticle\SaveArticleRequest;
use App\Models\Article;
use Illuminate\Http\JsonResponse;

class SavedArticleController extends Controller
{
    /**
     * Get user's saved articles
     */
    public function index(): JsonResponse
    {
        $articles = auth()->user()
            ->savedArticles()
            ->with(['source', 'category'])
            ->paginate(15);

        return response()->json($articles);
    }

    /**
     * Save an article
     */
    public function store(SaveArticleRequest $request): JsonResponse
    {
        $article = Article::findOrFail($request->article_id);
        auth()->user()->saveArticle($article);

        return response()->json([
            'message' => 'Article saved successfully',
        ], 201);
    }

    /**
     * Remove a saved article
     */
    public function destroy(Article $article): JsonResponse
    {
        auth()->user()->unsaveArticle($article);

        return response()->json([
            'message' => 'Article removed from saved articles',
        ]);
    }

    /**
     * Check if an article is saved
     */
    public function check(Article $article): JsonResponse
    {
        return response()->json([
            'is_saved' => auth()->user()->hasSavedArticle($article),
        ]);
    }
}
