<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prompt_caches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->string('query_hash')->unique();
            $table->string('model', 100);
            $table->json('response');
            $table->integer('tokens');
            $table->decimal('cost', 12, 6);
            $table->timestamp('ttl_expires_at')->nullable();
            $table->integer('hit_count')->default(1);
            $table->timestamps();

            $table->index(['team_id', 'ttl_expires_at'], 'idx_team_expires');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prompt_caches');
    }
};
