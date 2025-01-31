<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SavedArticleController;
use App\Http\Controllers\Api\UserPreferenceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/user', [AuthController::class, 'user']);

// Articles
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/search', [ArticleController::class, 'search']);
Route::get('/articles/feed', [ArticleController::class, 'feed']);
Route::get('/articles/{article}', [ArticleController::class, 'show']);

// Categories
Route::get('/categories', [CategoryController::class, 'index']);

// User Preferences
Route::get('/preferences', [UserPreferenceController::class, 'show']);
Route::put('/preferences', [UserPreferenceController::class, 'update']);
Route::get('/preferences/options', [UserPreferenceController::class, 'options']);
Route::post('/preferences/reset', [UserPreferenceController::class, 'reset']);

// Saved Articles
Route::get('/saved-articles', [SavedArticleController::class, 'index']);
Route::post('/saved-articles', [SavedArticleController::class, 'store']);
Route::delete('/saved-articles/{article}', [SavedArticleController::class, 'destroy']);
Route::get('/saved-articles/{article}/check', [SavedArticleController::class, 'check']);
