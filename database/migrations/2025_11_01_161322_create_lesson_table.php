<?php

declare(strict_types=1);

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
        Schema::create('lesson', function (Blueprint $table) {
            $table->id();
            
            // Foreign Key
            $table->foreignId('course_id')->constrained('course')->onDelete('cascade');
            
            // Lesson Information
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->unsignedInteger('order_index');
            
            // Content
            $table->longText('content_html')->nullable();
            $table->string('video_url', 500)->nullable();
            $table->json('materials_json')->nullable();
            $table->json('resources')->nullable();
            $table->text('summary')->nullable();
            
            // Metadata
            $table->unsignedInteger('duration_min')->default(0);
            $table->json('learning_objectives')->nullable();
            $table->boolean('is_required')->default(false);
            
            // Timestamps (Symfony uses created_at_utc, updated_at_utc)
            $table->dateTime('created_at_utc');
            $table->dateTime('updated_at_utc');
            
            // Laravel timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('course_id');
            $table->index(['course_id', 'order_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson');
    }
};
