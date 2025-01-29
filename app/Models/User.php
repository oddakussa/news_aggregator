<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's preferences
     */
    public function preferences(): HasOne
    {
        return $this->hasOne(UserPreference::class);
    }

    /**
     * Get the articles saved by the user
     */
    public function savedArticles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'saved_articles')
            ->withTimestamps()
            ->latest('saved_articles.created_at');
    }

    /**
     * Save an article for the user
     */
    public function saveArticle(Article $article): void
    {
        $this->savedArticles()->syncWithoutDetaching([$article->id]);
    }

    /**
     * Unsave an article for the user
     */
    public function unsaveArticle(Article $article): void
    {
        $this->savedArticles()->detach($article->id);
    }

    /**
     * Check if user has saved an article
     */
    public function hasSavedArticle(Article $article): bool
    {
        return $this->savedArticles()->where('article_id', $article->id)->exists();
    }

    /**
     * Get personalized news feed for the user
     */
    public function getPersonalizedFeed()
    {
        return $this->preferences?->getPersonalizedFeed() ?? Article::query()->latest('published_at');
    }

    /**
     * Initialize user preferences
     */
    public function initializePreferences(array $data = []): UserPreference
    {
        return $this->preferences()->create($data);
    }
}
