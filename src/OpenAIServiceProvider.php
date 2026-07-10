<?php

namespace HardikSolanki\OpenAILaravel;

use HardikSolanki\OpenAILaravel\Console\Commands\BudgetCheckCommand;
use HardikSolanki\OpenAILaravel\Console\Commands\BudgetNotifyCommand;
use HardikSolanki\OpenAILaravel\Console\Commands\BudgetResetCommand;
use HardikSolanki\OpenAILaravel\Console\Commands\CacheCleanupCommand;
use HardikSolanki\OpenAILaravel\Console\Commands\ContextOptimizeCommand;
use HardikSolanki\OpenAILaravel\Console\Commands\KeysCreateCommand;
use HardikSolanki\OpenAILaravel\Console\Commands\KeysListCommand;
use HardikSolanki\OpenAILaravel\Console\Commands\KeysRevokeCommand;
use HardikSolanki\OpenAILaravel\Console\Commands\KeysTestCommand;
use HardikSolanki\OpenAILaravel\Console\Commands\RateLimitResetCommand;
use HardikSolanki\OpenAILaravel\Console\Commands\UsageByModelCommand;
use HardikSolanki\OpenAILaravel\Console\Commands\UsageByUserCommand;
use HardikSolanki\OpenAILaravel\Console\Commands\UsageReportCommand;
use HardikSolanki\OpenAILaravel\Http\Middleware\AuthenticateOpenAI;
use HardikSolanki\OpenAILaravel\Http\Middleware\CheckBudgetLimit;
use HardikSolanki\OpenAILaravel\Http\Middleware\CheckRateLimit;
use HardikSolanki\OpenAILaravel\Http\Middleware\EnsureTeamAccess;
use HardikSolanki\OpenAILaravel\Http\Middleware\ValidateAPIKey;
use HardikSolanki\OpenAILaravel\Services\ContextOptimizerService;
use HardikSolanki\OpenAILaravel\Services\ConversationService;
use HardikSolanki\OpenAILaravel\Services\CostCalculationService;
use HardikSolanki\OpenAILaravel\Services\OpenAIClientService;
use HardikSolanki\OpenAILaravel\Services\PromptCacheService;
use HardikSolanki\OpenAILaravel\Services\RateLimitService;
use HardikSolanki\OpenAILaravel\Services\UsageTrackingService;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class OpenAIServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/Resources/config/openai.php', 'openai');

        // Force spatie/laravel-permission's team support on, scoped by team_id,
        // so RBAC (admin/member/viewer) is isolated per team.
        config([
            'permission.teams' => true,
            'permission.column_names.team_foreign_key' => 'team_id',
        ]);

        $this->app->singleton(ConversationService::class);
        $this->app->singleton(UsageTrackingService::class);
        $this->app->singleton(CostCalculationService::class);
        $this->app->singleton(ContextOptimizerService::class);
        $this->app->singleton(PromptCacheService::class);
        $this->app->singleton(OpenAIClientService::class);

        $this->app->singleton(RateLimitService::class, function () {
            return new RateLimitService(
                (int) config('openai.rate_limiting.max_requests_per_hour', 100),
                3600
            );
        });

        $this->app->singleton('openai', fn ($app) => new OpenAIManager($app));
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/Resources/config/openai.php' => config_path('openai.php'),
        ], 'openai-config');

        $this->publishes([
            __DIR__.'/Resources/migrations' => database_path('migrations'),
        ], 'openai-migrations');

        $this->loadMigrationsFrom(__DIR__.'/Resources/migrations');
        $this->loadRoutesFrom(__DIR__.'/Resources/routes/api.php');

        $this->registerMiddleware();

        if ($this->app->runningInConsole()) {
            $this->commands([
                KeysCreateCommand::class,
                KeysListCommand::class,
                KeysRevokeCommand::class,
                KeysTestCommand::class,
                UsageReportCommand::class,
                UsageByModelCommand::class,
                UsageByUserCommand::class,
                BudgetCheckCommand::class,
                BudgetResetCommand::class,
                BudgetNotifyCommand::class,
                CacheCleanupCommand::class,
                ContextOptimizeCommand::class,
                RateLimitResetCommand::class,
            ]);
        }
    }

    protected function registerMiddleware(): void
    {
        /** @var Router $router */
        $router = $this->app['router'];

        $router->aliasMiddleware('openai.auth', AuthenticateOpenAI::class);
        $router->aliasMiddleware('openai.team', EnsureTeamAccess::class);
        $router->aliasMiddleware('openai.rate-limit', CheckRateLimit::class);
        $router->aliasMiddleware('openai.budget', CheckBudgetLimit::class);
        $router->aliasMiddleware('openai.api-key', ValidateAPIKey::class);
    }
}
