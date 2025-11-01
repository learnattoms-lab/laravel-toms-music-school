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
        Schema::create('course_offerings', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('course_id')->constrained('course')->onDelete('cascade');
            $table->foreignId('tutor_id')->constrained('user')->onDelete('restrict');
            
            // Offering Information
            $table->string('title', 200);
            $table->string('timezone', 64)->default('UTC');
            $table->dateTime('start_at_utc');
            $table->dateTime('end_at_utc');
            $table->unsignedInteger('capacity')->default(20);
            $table->unsignedInteger('price_cents_override')->nullable();
            $table->string('status', 20)->default('scheduled');
            
            // Recurrence
            $table->text('rrule')->nullable();
            $table->json('exdates_json')->nullable();
            $table->unsignedInteger('duration_minutes')->default(60);
            $table->unsignedInteger('sessions_count')->default(0);
            
            // Timestamps
            $table->dateTime('created_at_utc');
            $table->dateTime('updated_at_utc');
            $table->timestamps();
            
            // Indexes
            $table->index(['course_id', 'status']);
            $table->index(['tutor_id', 'start_at_utc']);
            $table->index(['status', 'start_at_utc']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_offerings');
    }
};
