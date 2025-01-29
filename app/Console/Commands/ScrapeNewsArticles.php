<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ScrapeNewsArticles extends Command
{
    protected $signature = 'news:scrape {source?}';
    protected $description = 'Scrape articles from configured news sources';

    private array $sources = [
        'newsapi' => [
            'name' => 'NewsAPI',
            'url' => 'NEWSAPI_URL',
            'key' => 'NEWSAPI_KEY',
        ],
        'guardian' => [
            'name' => 'The Guardian',
            'url' => 'GUARDIAN_URL',
            'key' => 'GUARDIAN_API_KEY',
        ],
        'nyt' => [
            'name' => 'New York Times',
            'url' => 'NYT_URL',
            'key' => 'NYT_API_KEY',
        ],
    ];

    public function handle(): int
    {
        $sourceArg = $this->argument('source');

        if ($sourceArg && !array_key_exists($sourceArg, $this->sources)) {
            $this->error("Unknown source: {$sourceArg}");
            return Command::FAILURE;
        }

        $sources = $sourceArg ? [$sourceArg => $this->sources[$sourceArg]] : $this->sources;

        foreach ($sources as $key => $config) {
            $this->info("Scraping articles from {$config['name']}...");

            try {
                $source = Source::firstOrCreate(
                    ['type' => $key],
                    [
                        'name' => $config['name'],
                        'api_id' => $key,
                        'base_url' => env($config['url']),
                        'is_active' => true,
                    ]
                );

                $method = "scrapeFrom" . ucfirst($key);
                if (method_exists($this, $method)) {
                    $this->$method($source);
                } else {
                    $this->warn("Scraper method not implemented for {$config['name']}");
                }
            } catch (\Exception $e) {
                $this->error("Error scraping from {$config['name']}: {$e->getMessage()}");
                Log::error("News scraping error for {$config['name']}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        return Command::SUCCESS;
    }

    private function scrapeFromNewsapi(Source $source): void
    {
        $response = Http::withHeaders([
            'X-Api-Key' => env('NEWSAPI_KEY'),
        ])->get(env('NEWSAPI_URL') . '/top-headlines', [
            'language' => 'en',
            'pageSize' => 100,
        ]);

        if (!$response->successful()) {
            throw new \Exception("NewsAPI request failed: " . $response->body());
        }

        $articles = $response->json()['articles'] ?? [];
        
        foreach ($articles as $article) {
            // Create default category if source name is not provided
            $categoryName = $article['source']['name'] ?? 'Uncategorized';
            $categorySlug = str()->slug($categoryName);
            
            $category = Category::firstOrCreate(
                ['slug' => $categorySlug],
                ['name' => $categoryName]
            );

            Article::updateOrCreate(
                [
                    'url' => $article['url'],
                    'source_id' => $source->id,
                ],
                [
                    'category_id' => $category->id,
                    'title' => $article['title'] ?? 'Untitled',
                    'description' => $article['description'],
                    'content' => $article['content'] ?: $article['description'], // Use description as fallback
                    'author' => $article['author'],
                    'image_url' => $article['urlToImage'],
                    'published_at' => $article['publishedAt'] ?? now(),
                ]
            );
        }

        $this->info("Scraped " . count($articles) . " articles from NewsAPI");
    }

    private function scrapeFromGuardian(Source $source): void
    {
        $response = Http::withHeaders([
            'api-key' => env('GUARDIAN_API_KEY'),
        ])->get(env('GUARDIAN_URL') . '/search', [
            'show-fields' => 'all',
            'page-size' => 50,
        ]);

        if (!$response->successful()) {
            throw new \Exception("Guardian API request failed: " . $response->body());
        }

        $articles = $response->json()['response']['results'] ?? [];

        foreach ($articles as $article) {
            $categoryName = $article['sectionName'] ?? 'Uncategorized';
            $categorySlug = str()->slug($categoryName);

            $category = Category::firstOrCreate(
                ['slug' => $categorySlug],
                ['name' => $categoryName]
            );

            Article::updateOrCreate(
                [
                    'url' => $article['webUrl'],
                    'source_id' => $source->id,
                ],
                [
                    'category_id' => $category->id,
                    'title' => $article['webTitle'] ?? 'Untitled',
                    'description' => $article['fields']['trailText'] ?? null,
                    'content' => $article['fields']['bodyText'] ?: ($article['fields']['trailText'] ?? null),
                    'author' => $article['fields']['byline'] ?? null,
                    'image_url' => $article['fields']['thumbnail'] ?? null,
                    'published_at' => $article['webPublicationDate'] ?? now(),
                ]
            );
        }

        $this->info("Scraped " . count($articles) . " articles from The Guardian");
    }

    private function scrapeFromNyt(Source $source): void
    {
        // Using the Top Stories API instead
        $response = Http::get(env('NYT_URL') . '/topstories/v2/home.json', [
            'api-key' => env('NYT_API_KEY'),
        ]);

        if (!$response->successful()) {
            throw new \Exception("NYT API request failed: " . $response->body());
        }

        $articles = $response->json()['results'] ?? [];

        foreach ($articles as $article) {
            $categoryName = $article['section'] ?? 'Uncategorized';
            $categorySlug = str()->slug($categoryName);

            $category = Category::firstOrCreate(
                ['slug' => $categorySlug],
                ['name' => $categoryName]
            );

            // Get the first multimedia URL if available
            $imageUrl = null;
            if (!empty($article['multimedia'])) {
                foreach ($article['multimedia'] as $media) {
                    if ($media['type'] === 'image') {
                        $imageUrl = $media['url'];
                        break;
                    }
                }
            }

            Article::updateOrCreate(
                [
                    'url' => $article['url'],
                    'source_id' => $source->id,
                ],
                [
                    'category_id' => $category->id,
                    'title' => $article['title'] ?? 'Untitled',
                    'description' => $article['abstract'] ?? null,
                    'content' => $article['abstract'] ?? null, // NYT API doesn't provide full content
                    'author' => $article['byline'] ?? null,
                    'image_url' => $imageUrl,
                    'published_at' => $article['published_date'] ?? now(),
                ]
            );
        }

        $this->info("Scraped " . count($articles) . " articles from New York Times");
    }
}
