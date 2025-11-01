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
        Schema::create('quiz', function (Blueprint $table) {
            $table->id();
            
            // Foreign Key
            $table->foreignId('lesson_id')->constrained('lesson')->onDelete('cascade');
            
            // Quiz Information
            $table->json('questions');
            $table->unsignedInteger('pass_mark');
            $table->longText('instructions')->nullable();
            $table->unsignedInteger('time_limit')->default(0);
            $table->boolean('allow_retakes')->default(true);
            $table->unsignedInteger('max_attempts')->default(3);
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('show_correct_answers')->default(false);
            
            // Timestamps
            // Note: Symfony schema has created_at as VARCHAR(255), we'll use DATETIME
            $table->timestamps();
            
            // Indexes
            $table->index('lesson_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz');
    }
};
