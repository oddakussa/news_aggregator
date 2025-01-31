<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * List all categories
     */
    public function index(): JsonResponse
    {
        $categories = Category::orderBy('name')->get();
        
        return response()->json($categories);
    }
}
