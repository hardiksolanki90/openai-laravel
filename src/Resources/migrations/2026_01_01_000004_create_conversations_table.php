<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('api_key_id')->nullable()->constrained('api_keys')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('model', 100)->default('gpt-4');
            $table->text('system_prompt')->nullable();
            $table->json('messages');
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('total_tokens')->default(0);
            $table->decimal('total_cost', 12, 6)->default(0);
            $table->integer('context_window_used')->default(0);
            $table->boolean('is_archived')->default(false);
            $table->timestamps();

            $table->index(['team_id', 'user_id'], 'idx_team_user');
            $table->index('created_at', 'idx_created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
