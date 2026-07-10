<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prompt_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->longText('content');
            $table->json('variables');
            $table->string('model', 100)->default('gpt-4');
            $table->boolean('is_public')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->unique(['team_id', 'slug'], 'unique_team_slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prompt_templates');
    }
};
