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
        Schema::create('quiz_attempt', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('quiz_id')->constrained('quiz')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('user')->onDelete('cascade');
            
            // Attempt Information
            $table->unsignedInteger('score');
            $table->boolean('passed');
            $table->dateTime('submitted_at');
            $table->json('responses');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->unsignedInteger('time_spent')->nullable();
            $table->json('question_order')->nullable();
            $table->longText('notes')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('quiz_id');
            $table->index('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempt');
    }
};
