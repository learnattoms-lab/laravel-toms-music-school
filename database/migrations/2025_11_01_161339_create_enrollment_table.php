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
        Schema::create('enrollment', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('student_id')->constrained('user')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('course')->onDelete('cascade');
            $table->foreignId('offering_id')->nullable()->constrained('course_offerings')->onDelete('set null');
            
            // Enrollment Information
            $table->dateTime('enrolled_at');
            $table->string('status', 50);
            $table->dateTime('completed_at')->nullable();
            
            // Progress Tracking (additional fields from schema_only.sql)
            $table->decimal('progress_pct', 5, 2)->default(0)->nullable();
            $table->dateTime('last_accessed_at')->nullable();
            $table->unsignedInteger('lessons_completed')->default(0);
            $table->unsignedInteger('total_lessons')->default(0);
            $table->json('completed_lessons')->nullable();
            $table->json('quiz_scores')->nullable();
            $table->json('assignment_scores')->nullable();
            $table->longText('notes')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('student_id');
            $table->index('course_id');
            $table->index('offering_id');
            $table->unique(['student_id', 'course_id']);
            $table->unique(['student_id', 'offering_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment');
    }
};
