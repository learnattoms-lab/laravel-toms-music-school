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
        Schema::create('course', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('teacher_id')->constrained('user')->onDelete('restrict');
            $table->foreignId('category_id')->constrained('course_category')->onDelete('restrict');
            $table->foreignId('cover_file_id')->nullable()->constrained('stored_file')->onDelete('set null');
            
            // Course Information
            $table->string('title', 200);
            $table->string('slug', 220);
            $table->text('description')->nullable();
            $table->string('instrument', 100)->default('other');
            $table->string('level', 50)->default('beginner');
            $table->unsignedInteger('price_cents')->default(0);
            $table->string('status', 20)->default('draft');
            
            // Timestamps
            $table->dateTime('published_at')->nullable();
            $table->dateTime('created_at_utc');
            $table->dateTime('updated_at_utc');
            
            // Indexes
            $table->index('teacher_id');
            $table->index('status');
            
            // Laravel timestamps (will be added as created_at and updated_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course');
    }
};
