<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rate_limit_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('api_key_id')->nullable()->constrained('api_keys')->cascadeOnDelete();
            $table->integer('tokens_remaining');
            $table->timestamp('refill_at');
            $table->integer('window_size')->default(3600);
            $table->integer('max_tokens')->default(100);
            $table->timestamps();

            $table->unique(['team_id', 'api_key_id'], 'unique_limit');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_limit_tokens');
    }
};
