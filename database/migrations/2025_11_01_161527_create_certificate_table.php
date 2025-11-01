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
        Schema::create('certificate', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('user_id')->constrained('user')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('course')->onDelete('cascade');
            
            // Certificate Information
            $table->dateTime('issued_at');
            $table->string('certificate_url', 500);
            $table->string('serial', 100);
            $table->decimal('final_score', 5, 2);
            $table->string('grade', 20);
            $table->longText('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_valid')->default(true);
            $table->dateTime('revoked_at')->nullable();
            $table->longText('revocation_reason')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('course_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate');
    }
};
