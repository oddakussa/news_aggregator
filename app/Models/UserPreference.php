<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'preferred_sources',
        'preferred_categories',
        'preferred_authors',
    ];

    protected $casts = [
        'preferred_sources' => 'array',
        'preferred_categories' => 'array',
        'preferred_authors' => 'array',
    ];

    /**
     * Get the user that owns the preferences
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get articles based on user preferences
     */
    public function getPersonalizedFeed()
    {
        $query = Article::query();

        if (!empty($this->preferred_sources)) {
            $query->whereIn('source_id', $this->preferred_sources);
        }

        if (!empty($this->preferred_categories)) {
            $query->whereIn('category_id', $this->preferred_categories);
        }

        if (!empty($this->preferred_authors)) {
            $query->whereIn('author', $this->preferred_authors);
        }

        return $query->latest('published_at');
    }
}
