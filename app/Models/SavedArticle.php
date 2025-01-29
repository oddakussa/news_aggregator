<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedArticle extends Model
{
    protected $fillable = [
        'user_id',
        'article_id',
    ];

    /**
     * Get the user that saved the article
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the saved article
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
