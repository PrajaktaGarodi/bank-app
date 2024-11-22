<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();        // Meta title for SEO
            $table->text('meta_description')->nullable();    // Meta description for SEO
            $table->unsignedBigInteger('parent_category_id')->nullable();  // Parent category for hierarchical categories
            $table->string('slug')->unique();                // Slug for URLs
            $table->timestamps();
            // Foreign key for parent category (if using foreign relationships)
            $table->foreign('parent_category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
// 2024_11_22_035419_create_categories_table