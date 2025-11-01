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
        Schema::create('assignment_submission', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('assignment_id')->constrained('assignment')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('user')->onDelete('cascade');
            $table->foreignId('graded_by_id')->nullable()->constrained('user')->onDelete('set null');
            
            // Submission Information
            $table->text('submission_text')->nullable();
            $table->foreignId('submitted_file_id')->nullable()->constrained('stored_file')->onDelete('set null');
            $table->json('files')->nullable();
            $table->string('status', 50)->default('pending');
            
            // Grading Information
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('graded_at')->nullable();
            $table->decimal('grade', 5, 2)->nullable();
            $table->unsignedInteger('grade_points')->nullable();
            $table->text('feedback')->nullable();
            $table->longText('feedback_html')->nullable();
            $table->longText('notes')->nullable();
            $table->boolean('is_late')->default(false);
            $table->unsignedInteger('late_penalty_applied')->default(0);
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('assignment_id');
            $table->index('student_id');
            $table->index('graded_by_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_submission');
    }
};
