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
        Schema::create('stored_file', function (Blueprint $table) {
            $table->id();
            $table->string('original_filename', 255);
            $table->string('stored_path', 500);
            $table->string('mime_type', 100);
            $table->unsignedInteger('file_size');
            $table->foreignId('uploader_id')->constrained('user')->onDelete('restrict');
            $table->dateTime('created_at');
            // Note: Symfony schema only has created_at, no updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stored_file');
    }
};
