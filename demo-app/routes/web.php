<?php

use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Settings\APIKeySettingsController;
use App\Http\Controllers\Settings\BudgetSettingsController;
use App\Http\Controllers\Settings\TeamSettingsController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\UsageReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');
    Route::post('/conversations', [ConversationController::class, 'store'])->name('conversations.store');
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'sendMessage'])->name('conversations.messages.store');

    Route::get('/templates', [TemplateController::class, 'index'])->name('templates.index');
    Route::post('/templates', [TemplateController::class, 'store'])->name('templates.store');

    Route::get('/usage', UsageReportController::class)->name('usage.reports');

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/api-keys', [APIKeySettingsController::class, 'index'])->name('api-keys');
        Route::post('/api-keys', [APIKeySettingsController::class, 'store'])->name('api-keys.store');
        Route::delete('/api-keys/{id}', [APIKeySettingsController::class, 'destroy'])->name('api-keys.destroy');

        Route::get('/budget', [BudgetSettingsController::class, 'edit'])->name('budget');
        Route::put('/budget', [BudgetSettingsController::class, 'update'])->name('budget.update');

        Route::get('/team', [TeamSettingsController::class, 'index'])->name('team');
        Route::post('/team/invite', [TeamSettingsController::class, 'invite'])->name('team.invite');
    });
});

require __DIR__.'/auth.php';
