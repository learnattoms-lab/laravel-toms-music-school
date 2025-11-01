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
        Schema::create('user', function (Blueprint $table) {
            $table->id();
            
            // Authentication
            $table->string('email', 180)->unique();
            $table->json('roles');
            $table->string('password', 255)->nullable();
            
            // Personal Information
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            
            // Music Profile
            $table->string('instrument', 100)->nullable();
            $table->string('skill_level', 50)->nullable();
            $table->longText('bio')->nullable();
            $table->string('profile_picture', 255)->nullable();
            
            // Location
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('timezone', 100)->nullable();
            
            // Preferences
            $table->json('preferences')->nullable();
            
            // Timestamps
            $table->dateTime('created_at');
            $table->dateTime('last_login_at')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('email_verified')->default(false);
            
            // OAuth IDs
            $table->string('google_id', 255)->nullable();
            $table->string('apple_id', 255)->nullable();
            $table->string('facebook_id', 255)->nullable();
            
            // Gamification
            $table->unsignedInteger('experience_points')->default(0);
            $table->unsignedInteger('level')->default(1);
            $table->json('achievements')->nullable();
            $table->json('badges')->nullable();
            
            // Rating
            $table->decimal('rating', 5, 2)->nullable();
            
            // Learning Progress
            $table->unsignedInteger('total_lessons')->default(0);
            $table->unsignedInteger('completed_lessons')->default(0);
            $table->unsignedInteger('practice_hours')->default(0);
            $table->dateTime('last_practice_at')->nullable();
            $table->json('learning_goals')->nullable();
            $table->json('progress_data')->nullable();
            
            // Notes
            $table->longText('notes')->nullable();
            
            // Teacher Profile
            $table->boolean('is_teacher')->default(false);
            $table->longText('teacher_bio')->nullable();
            $table->json('teacher_specialties')->nullable();
            $table->json('teacher_certifications')->nullable();
            $table->decimal('hourly_rate', 5, 2)->nullable();
            $table->json('availability')->nullable();
            $table->json('student_reviews')->nullable();
            $table->unsignedInteger('total_students')->default(0);
            $table->unsignedInteger('active_students')->default(0);
            
            // Security
            $table->unsignedInteger('failed_login_attempts')->default(0);
            $table->dateTime('last_failed_login_at')->nullable();
            $table->string('last_failed_login_ip', 45)->nullable();
            $table->boolean('is_locked')->default(false);
            $table->dateTime('locked_until')->nullable();
            
            // Indexes
            $table->index('email');
            $table->index('is_active');
            // Note: JSON columns (roles) cannot be indexed directly in MySQL without generated columns
            
            // Note: Laravel's timestamps() adds created_at and updated_at
            // But Symfony schema only has created_at
            // We'll add updated_at for Laravel conventions but keep created_at from schema
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
