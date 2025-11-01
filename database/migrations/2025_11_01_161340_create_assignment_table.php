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
        Schema::create('assignment', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('course_id')->constrained('course')->onDelete('cascade');
            $table->foreignId('lesson_id')->nullable()->constrained('lesson')->onDelete('set null');
            $table->foreignId('session_id')->nullable()->constrained('session')->onDelete('set null');
            
            // Assignment Information
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->longText('instructions_html')->nullable();
            $table->dateTime('due_at')->nullable();
            $table->unsignedInteger('max_points')->default(0);
            $table->longText('rubric')->nullable();
            $table->json('attachments')->nullable();
            $table->boolean('is_required')->default(true);
            $table->boolean('allow_late_submission')->default(false);
            $table->unsignedInteger('late_penalty')->default(0);
            
            // Timestamps
            // Note: Symfony schema has created_at/updated_at as DATETIME, we'll use Laravel's timestamps()
            $table->timestamps();
            
            // Indexes
            $table->index('course_id');
            $table->index('lesson_id');
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment');
    }
};
