<?php

namespace App\Providers;

use App\Console\Commands\ScrapeNewsArticles;
use App\Models\Article;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class NewsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register any news-related services here
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register model observers or event listeners here
        Article::deleted(function ($article) {
            // Clean up any associated files or resources
            if ($article->image_url && str_starts_with($article->image_url, 'storage/')) {
                Storage::delete($article->image_url);
            }
        });

        if ($this->app->runningInConsole()) {
            // Register the news scraping command
            $this->commands([
                ScrapeNewsArticles::class,
            ]);
        }
    }
}
