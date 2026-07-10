# OpenAI Laravel

Production-ready Laravel package for OpenAI API integration with database persistence, multi-tenancy, and team management.

`hardiksolanki/openai-laravel` wraps OpenAI's Chat Completions and Image Generation APIs behind a fluent builder/facade, backed by Eloquent models for conversations, teams, usage logs, budgets, prompt templates, and API keys. It adds the concerns a real SaaS product needs on top of a bare API client: multi-tenancy, RBAC, rate limiting, cost tracking with budget enforcement, prompt caching, context window optimization, and streaming.

## Features

- **Fluent builder API** — `OpenAI::text()->prompt(...)->team($id)->generate()`
- **Database-backed conversations** with full message history, per-team isolation
- **Multi-tenancy & RBAC** — admin / member / viewer roles via `spatie/laravel-permission` (team-scoped)
- **Usage tracking & cost calculation** — per-request token/cost logging, team and per-user breakdowns
- **Budget limits** — monthly caps with warning thresholds and optional hard blocking
- **Rate limiting** — in-memory token bucket, persisted for restart recovery
- **Prompt templates** — reusable prompts with `{{ variable }}` interpolation
- **Context window optimization** — automatic truncation of oldest messages to fit the model's context window
- **Prompt caching** — SHA-256 query hashing with TTL, avoids duplicate API calls
- **Streaming** — SSE-style streamed responses with post-completion usage logging
- **REST API** — full set of endpoints for conversations, templates, usage, API keys, budgets, and teams
- **Artisan commands** — key management, usage reports, budget checks, cache/context/rate-limit maintenance
- **Encrypted API keys** — multiple OpenAI keys per team, encrypted with Laravel's `Crypt` facade

## Requirements

- PHP 8.1+
- Laravel 10.0+ or 11.0+
- MySQL or PostgreSQL

## Installation

This package is not yet published on Packagist. Until it is, install it via a path or VCS repository.

**Local path repo** (developing against a checkout on the same machine):

```json
{
    "repositories": [
        { "type": "path", "url": "../laravel-openAI" }
    ]
}
```

```bash
composer require hardiksolanki/openai-laravel:@dev
```

**VCS repo** (once pushed to GitHub):

```json
{
    "repositories": [
        { "type": "vcs", "url": "https://github.com/you/laravel-openAI" }
    ]
}
```

```bash
composer require hardiksolanki/openai-laravel:dev-main
```

**Once published to Packagist:**

```bash
composer require hardiksolanki/openai-laravel
```

Laravel's package auto-discovery picks up the service provider and `OpenAI` facade automatically from `composer.json`'s `extra.laravel` block — no manual registration needed.

Publish the config and migrations:

```bash
php artisan vendor:publish --tag=openai-config
php artisan vendor:publish --tag=openai-migrations
php artisan migrate
```

Add your OpenAI key and tune defaults in `.env`:

```env
OPENAI_API_KEY=sk-...
OPENAI_DEFAULT_MODEL=gpt-4
OPENAI_RATE_LIMITING_ENABLED=true
OPENAI_MAX_REQUESTS_PER_HOUR=100
OPENAI_CACHE_ENABLED=true
OPENAI_CACHE_TTL=3600
OPENAI_LOG_REQUESTS=false
```

RBAC is enforced through `spatie/laravel-permission` in **team-scoped mode**. Your application's `User` model must use the `Spatie\Permission\Traits\HasRoles` trait:

```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
}
```

## Quick start

```php
use HardikSolanki\OpenAILaravel\Facades\OpenAI;

$response = OpenAI::text()
    ->prompt('Write a blog post about Laravel')
    ->model('gpt-4')
    ->temperature(0.7)
    ->tokens(2000)
    ->team($teamId)
    ->user($userId)
    ->generate();

echo $response->content();
echo $response->tokensUsed();
echo $response->costIncurred();
```

### Chat with history

```php
$response = OpenAI::text()
    ->systemPrompt('You are a helpful coding assistant.')
    ->history([
        ['role' => 'user', 'content' => 'What is Laravel?'],
        ['role' => 'assistant', 'content' => 'Laravel is a PHP framework...'],
    ])
    ->prompt('Tell me about Eloquent.')
    ->team($teamId)
    ->user($userId)
    ->generate();
```

### Save to a conversation

```php
use HardikSolanki\OpenAILaravel\Services\ConversationService;

$conversation = app(ConversationService::class)->create($teamId, $userId, 'Coding Help');

OpenAI::text()
    ->prompt('How do I use migrations?')
    ->saveToConversation($conversation->id)
    ->team($teamId)
    ->user($userId)
    ->generate();
```

### Prompt templates

```php
use HardikSolanki\OpenAILaravel\Models\PromptTemplate;
use HardikSolanki\OpenAILaravel\Services\PromptTemplateService;

$template = PromptTemplate::findBySlug('product-review', $teamId);
$service = app(PromptTemplateService::class);

$content = $service->interpolate($template, ['product_name' => 'MacBook Pro']);

OpenAI::text()->prompt($content)->team($teamId)->user($userId)->generate();
```

### Streaming

```php
foreach (OpenAI::text()->prompt('Write a story...')->team($teamId)->user($userId)->streamGenerate() as $chunk) {
    echo $chunk->content();
}
```

### Image generation

```php
$images = OpenAI::image()
    ->prompt('A serene landscape at sunset')
    ->size('1024x1024')
    ->quality('hd')
    ->count(2)
    ->team($teamId)
    ->generate();

foreach ($images as $image) {
    echo $image->url;
}
```

### Handling limits

```php
use HardikSolanki\OpenAILaravel\Exceptions\BudgetExceededException;
use HardikSolanki\OpenAILaravel\Exceptions\RateLimitExceededException;

try {
    $response = OpenAI::text()->prompt('...')->team($teamId)->user($userId)->generate();
} catch (BudgetExceededException $e) {
    // $e->remainingBudget()
} catch (RateLimitExceededException $e) {
    // $e->resetInSeconds()
}
```

## REST API

The package registers routes under `/api/openai/*`, guarded by authentication, team access, rate limit, and budget middleware:

| Method | Endpoint | Description |
|---|---|---|
| GET/POST | `/api/openai/conversations` | List / create conversations |
| GET/PUT/DELETE | `/api/openai/conversations/{id}` | Show / update / archive |
| POST | `/api/openai/conversations/{id}/messages` | Send a message, get a response |
| POST | `/api/openai/conversations/{id}/stream` | Streamed response (SSE) |
| GET/POST | `/api/openai/templates` | List / create prompt templates |
| POST | `/api/openai/templates/{id}/use` | Interpolate and run a template |
| GET | `/api/openai/usage/summary` | Team usage summary |
| GET | `/api/openai/usage/daily` | Daily usage breakdown |
| GET | `/api/openai/usage/user/{userId}` | Per-user usage |
| GET/POST/DELETE | `/api/openai/api-keys` | Manage team API keys |
| GET/PUT | `/api/openai/budget` | View / update budget limits |
| GET/POST/PUT/DELETE | `/api/openai/teams/{id}/members` | Team member management |

## Artisan commands

```bash
php artisan openai:keys:create
php artisan openai:keys:list
php artisan openai:keys:revoke {keyId}
php artisan openai:keys:test {keyId}

php artisan openai:usage:report --team={teamId} --month=07 --format=json
php artisan openai:usage:by-model --team={teamId}
php artisan openai:usage:by-user --team={teamId}

php artisan openai:budget:check
php artisan openai:budget:reset --team={teamId}
php artisan openai:budget:notify

php artisan openai:cache:cleanup
php artisan openai:context:optimize --team={teamId}
php artisan openai:rate-limit:reset
```

## Configuration

See [`config/openai.php`](config/openai.php) for the full set of options: default model, per-model pricing/context windows, rate limiting, caching, context optimization, logging, and RBAC role/permission mappings.

## Testing

```bash
composer install
vendor/bin/phpunit
```

Tests run against an in-memory SQLite database via Orchestra Testbench.

## Demo app

A Blade-based multi-tenant SaaS demo (dashboard, conversations, templates, settings, usage reports) is provided as an overlay in [`demo-app/`](demo-app/README.md), meant to be applied on top of a fresh Laravel + Breeze install. See its README for setup.

## License

MIT
