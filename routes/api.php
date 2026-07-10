<?php

use HardikSolanki\OpenAILaravel\Http\Controllers\APIKeyController;
use HardikSolanki\OpenAILaravel\Http\Controllers\BudgetController;
use HardikSolanki\OpenAILaravel\Http\Controllers\ConversationController;
use HardikSolanki\OpenAILaravel\Http\Controllers\MessageController;
use HardikSolanki\OpenAILaravel\Http\Controllers\PromptTemplateController;
use HardikSolanki\OpenAILaravel\Http\Controllers\TeamController;
use HardikSolanki\OpenAILaravel\Http\Controllers\UsageController;
use HardikSolanki\OpenAILaravel\Http\Middleware\AuthenticateOpenAI;
use HardikSolanki\OpenAILaravel\Http\Middleware\CheckBudgetLimit;
use HardikSolanki\OpenAILaravel\Http\Middleware\CheckRateLimit;
use HardikSolanki\OpenAILaravel\Http\Middleware\EnsureTeamAccess;
use HardikSolanki\OpenAILaravel\Http\Middleware\ValidateAPIKey;
use Illuminate\Support\Facades\Route;

Route::middleware([AuthenticateOpenAI::class, EnsureTeamAccess::class])
    ->prefix('api/openai')
    ->group(function () {
        Route::apiResource('conversations', ConversationController::class)
            ->except(['destroy'])
            ->middleware('role:member,admin');
        Route::delete('conversations/{id}', [ConversationController::class, 'destroy'])
            ->middleware('role:member,admin');

        Route::middleware([CheckRateLimit::class, CheckBudgetLimit::class, ValidateAPIKey::class])
            ->group(function () {
                Route::post('conversations/{id}/messages', [MessageController::class, 'store'])
                    ->middleware('role:member,admin');
                Route::post('conversations/{id}/stream', [MessageController::class, 'stream'])
                    ->middleware('role:member,admin');
            });

        Route::apiResource('templates', PromptTemplateController::class)
            ->except(['update'])
            ->middleware('role:member,admin');
        Route::post('templates/{id}/use', [PromptTemplateController::class, 'use'])
            ->middleware('role:member,admin');

        Route::get('usage/summary', [UsageController::class, 'summary']);
        Route::get('usage/daily', [UsageController::class, 'daily']);
        Route::get('usage/user/{userId}', [UsageController::class, 'user']);

        Route::get('api-keys', [APIKeyController::class, 'index']);
        Route::post('api-keys', [APIKeyController::class, 'store'])->middleware('role:admin');
        Route::delete('api-keys/{id}', [APIKeyController::class, 'destroy'])->middleware('role:admin');

        Route::get('budget', [BudgetController::class, 'show']);
        Route::put('budget', [BudgetController::class, 'update'])->middleware('role:admin');

        Route::get('teams/{id}/members', [TeamController::class, 'members']);
        Route::post('teams/{id}/members', [TeamController::class, 'inviteMember'])->middleware('role:admin');
        Route::put('teams/{id}/members/{memberId}', [TeamController::class, 'updateMember'])->middleware('role:admin');
        Route::delete('teams/{id}/members/{memberId}', [TeamController::class, 'removeMember'])->middleware('role:admin');
    });
