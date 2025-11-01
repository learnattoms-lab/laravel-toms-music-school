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
        Schema::create('session', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('course_id')->constrained('course')->onDelete('cascade');
            $table->foreignId('lesson_id')->nullable()->constrained('lesson')->onDelete('set null');
            $table->foreignId('tutor_id')->constrained('user')->onDelete('restrict');
            $table->foreignId('offering_id')->nullable()->constrained('course_offerings')->onDelete('set null');
            
            // Session Information
            $table->dateTime('start_at_utc');
            $table->dateTime('end_at_utc');
            $table->string('join_url', 500)->nullable();
            $table->string('google_meet_link', 500)->nullable();
            $table->string('google_event_id', 255)->nullable();
            $table->json('materials_json')->nullable();
            $table->string('recording_url', 500)->nullable();
            $table->string('status', 50)->default('scheduled');
            $table->unsignedInteger('max_students')->default(0);
            $table->longText('notes')->nullable();
            
            // Timestamps
            $table->dateTime('created_at_utc');
            $table->dateTime('updated_at_utc');
            $table->timestamps();
            
            // Indexes
            $table->index(['course_id', 'start_at_utc']);
            $table->index(['tutor_id', 'start_at_utc']);
            $table->index('offering_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session');
    }
};
