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
        Schema::create('comment', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('submission_id')->constrained('assignment_submission')->onDelete('cascade');
            $table->foreignId('author_id')->constrained('user')->onDelete('cascade');
            
            // Comment Information
            $table->longText('body');
            $table->boolean('is_internal')->default(false);
            $table->json('attachments')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('submission_id');
            $table->index('author_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment');
    }
};
