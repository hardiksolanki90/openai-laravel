<?php

namespace HardikSolanki\OpenAILaravel\Tests;

use HardikSolanki\OpenAILaravel\OpenAIServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Permission\PermissionServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            PermissionServiceProvider::class,
            OpenAIServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('openai.api_key', 'sk-test');
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__.'/../src/Resources/migrations');
        $this->loadMigrationsFrom(__DIR__.'/../vendor/spatie/laravel-permission/database/migrations');
    }
}
