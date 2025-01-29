<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserPreference\UpdatePreferenceRequest;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Http\JsonResponse;

class UserPreferenceController extends Controller
{
    /**
     * Get user preferences
     */
    public function show(): JsonResponse
    {
        $preferences = auth()->user()->preferences;

        return response()->json([
            'preferences' => $preferences,
        ]);
    }

    /**
     * Update user preferences
     */
    public function update(UpdatePreferenceRequest $request): JsonResponse
    {
        $preferences = auth()->user()->preferences;
        $preferences->update($request->validated());

        return response()->json([
            'message' => 'Preferences updated successfully',
            'preferences' => $preferences->fresh(),
        ]);
    }

    /**
     * Get available sources and categories for preferences
     */
    public function options(): JsonResponse
    {
        return response()->json([
            'sources' => Source::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'type']),
            'categories' => Category::orderBy('name')
                ->get(['id', 'name', 'slug']),
        ]);
    }

    /**
     * Reset user preferences to default
     */
    public function reset(): JsonResponse
    {
        $preferences = auth()->user()->preferences;
        $preferences->update([
            'preferred_sources' => null,
            'preferred_categories' => null,
            'preferred_authors' => null,
        ]);

        return response()->json([
            'message' => 'Preferences reset successfully',
            'preferences' => $preferences->fresh(),
        ]);
    }
}
