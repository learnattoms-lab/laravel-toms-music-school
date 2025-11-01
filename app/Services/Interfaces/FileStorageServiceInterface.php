<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\StoredFile;
use App\Models\User;
use Illuminate\Http\UploadedFile;

/**
 * File Storage Service Interface
 *
 * Defines the contract for cloud file storage operations.
 */
interface FileStorageServiceInterface
{
    /**
     * Upload a file to cloud storage.
     *
     * @param string $path The path where the file should be stored
     * @param UploadedFile $file The uploaded file
     * @param User $uploadedBy The user who uploaded the file
     * @return StoredFile The stored file entity with metadata
     * @throws \Exception If upload fails
     */
    public function upload(string $path, UploadedFile $file, User $uploadedBy): StoredFile;

    /**
     * Delete a file from cloud storage.
     *
     * @param string $objectName The name/identifier of the object to delete
     * @throws \Exception If deletion fails
     */
    public function delete(string $objectName): void;

    /**
     * Generate a temporary URL for a file.
     *
     * @param string $objectName The name/identifier of the object
     * @param \DateInterval $ttl Time to live for the temporary URL
     * @return string The temporary URL
     * @throws \Exception If URL generation fails
     */
    public function temporaryUrl(string $objectName, \DateInterval $ttl): string;

    /**
     * Get the public URL for a file (if applicable).
     *
     * @param string $objectName The name/identifier of the object
     * @return string The public URL
     */
    public function getPublicUrl(string $objectName): string;

    /**
     * Generate a download URL with TTL.
     *
     * @param string $objectName The name/identifier of the object
     * @param int $ttlMinutes Time to live in minutes (default: 60)
     * @return string The temporary download URL
     */
    public function downloadUrl(string $objectName, int $ttlMinutes = 60): string;

    /**
     * Copy a file within storage.
     *
     * @param string $sourceObjectName The source object name
     * @param string $destinationPath The destination path
     * @param string|null $newExtension Optional new extension for the copied file
     * @return StoredFile The new stored file entity
     */
    public function copyFile(string $sourceObjectName, string $destinationPath, ?string $newExtension = null): StoredFile;

    /**
     * Check if a file exists in storage.
     *
     * @param string $objectName The name/identifier of the object
     * @return bool True if file exists, false otherwise
     */
    public function fileExists(string $objectName): bool;

    /**
     * Get file properties/metadata.
     *
     * @param string $objectName The name/identifier of the object
     * @return array<string, mixed> File properties
     */
    public function getFileProperties(string $objectName): array;

    /**
     * Test the connection to the storage service.
     *
     * @return bool True if connection is successful, false otherwise
     */
    public function testConnection(): bool;
}

