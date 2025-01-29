# News Aggregator API

A RESTful API for aggregating news from multiple sources, built with Laravel.

## Features

- User authentication (register, login, logout)
- Article search and filtering
- Personalized news feed based on user preferences
- Automatic news scraping from three major sources:
  - NewsAPI
  - The Guardian
  - New York Times
- Article categorization and source tracking
- Save/unsave articles functionality

## Docker Setup

1. Clone the repository:
```bash
git clone <repository-url>
cd <repository-name>
```

2. Copy environment file:
```bash
cp .env.example .env
```

3. Configure your news API keys in `.env`:
```env
NEWSAPI_KEY=your_newsapi_key
GUARDIAN_API_KEY=your_guardian_key
NYT_API_KEY=your_nyt_key
```

4. Build and start Docker containers:
```bash
docker-compose up -d --build
```

5. Install dependencies and set up Laravel:
```bash
# Enter the app container
docker exec -it news-app bash

# Install dependencies
composer install

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Exit container
exit
```

The application will be available at: http://localhost:8000

## API Endpoints

### Authentication

- `POST /api/register` - Register a new user
  - Parameters: `name`, `email`, `password`, `password_confirmation`

- `POST /api/login` - Login user
  - Parameters: `email`, `password`, `remember`

- `POST /api/logout` - Logout user (requires authentication)

- `GET /api/user` - Get authenticated user details (requires authentication)

### Articles

- `GET /api/articles` - List articles with filters (requires authentication)
  - Parameters:
    - `source_id` (optional)
    - `category_id` (optional)
    - `author` (optional)
    - `start_date` (optional)
    - `end_date` (optional)
    - `per_page` (optional, default: 15)
    - `sort_by` (optional, default: published_at)
    - `sort_direction` (optional, default: desc)

- `GET /api/articles/search` - Search articles (requires authentication)
  - Parameters:
    - `query` (required, min: 3 characters)
    - Other filters same as /articles endpoint

- `GET /api/articles/feed` - Get personalized feed (requires authentication)

- `GET /api/articles/{article}` - Get article details (requires authentication)

### User Preferences

- `GET /api/preferences` - Get user preferences (requires authentication)

- `PUT /api/preferences` - Update user preferences (requires authentication)
  - Parameters:
    - `preferred_sources` (optional, array of source IDs)
    - `preferred_categories` (optional, array of category IDs)
    - `preferred_authors` (optional, array of author names)

- `GET /api/preferences/options` - Get available sources and categories (requires authentication)

- `POST /api/preferences/reset` - Reset preferences to default (requires authentication)

### Saved Articles

- `GET /api/saved-articles` - List saved articles (requires authentication)

- `POST /api/saved-articles` - Save an article (requires authentication)
  - Parameters: `article_id`

- `DELETE /api/saved-articles/{article}` - Remove saved article (requires authentication)

- `GET /api/saved-articles/{article}/check` - Check if article is saved (requires authentication)

## Scheduled Tasks

The application includes scheduled tasks that run automatically in the Docker environment:
- Hourly news scraping from configured sources
- Daily cleanup of old articles (30+ days old)

## Docker Commands

```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# View logs
docker-compose logs -f

# Rebuild containers
docker-compose up -d --build

# Run artisan commands
docker exec -it news-app php artisan [command]

# Access MySQL
docker exec -it news-db mysql -u news_user -p news_db
```

## Container Structure

- **news-app**: PHP 8.2 + Laravel application
- **news-db**: MySQL 8.0 database
- **news-nginx**: Nginx web server
- **news-redis**: Redis for caching and queues

## License

This project is open-sourced software licensed under the MIT license.
