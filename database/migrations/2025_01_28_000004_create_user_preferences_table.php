<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('preferred_sources')->nullable(); // Array of source IDs
            $table->json('preferred_categories')->nullable(); // Array of category IDs
            $table->json('preferred_authors')->nullable(); // Array of author names
            $table->timestamps();

            $table->unique(['user_id']); // One preference record per user
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
