<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Prunable;

class Article extends Model
{
    use Prunable;

    protected $fillable = [
        'source_id',
        'category_id',
        'title',
        'description',
        'content',
        'author',
        'url',
        'image_url',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Get the prunable model query.
     * Keep articles for 30 days
     */
    public function prunable()
    {
        return static::where('published_at', '<=', now()->subDays(30));
    }

    /**
     * Get the source that the article belongs to
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    /**
     * Get the category that the article belongs to
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the users who have saved this article
     */
    public function savedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saved_articles')
            ->withTimestamps();
    }

    /**
     * Scope a query to filter articles by search term
     */
    public function scopeSearch($query, string $term)
    {
        return $query->whereFullText(['title', 'description', 'content'], $term);
    }

    /**
     * Scope a query to filter articles by date range
     */
    public function scopePublishedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('published_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter articles by category
     */
    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to filter articles by source
     */
    public function scopeFromSource($query, $sourceId)
    {
        return $query->where('source_id', $sourceId);
    }

    /**
     * Scope a query to filter articles by author
     */
    public function scopeByAuthor($query, string $author)
    {
        return $query->where('author', $author);
    }
}
