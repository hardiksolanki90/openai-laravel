<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->decimal('monthly_limit', 12, 6)->default(100);
            $table->decimal('current_spend', 12, 6)->default(0);
            $table->decimal('warning_threshold', 12, 6)->default(80);
            $table->boolean('is_active')->default(true);
            $table->boolean('block_on_limit')->default(false);
            $table->integer('month_starts_at')->default(1);
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('reset_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_limits');
    }
};
