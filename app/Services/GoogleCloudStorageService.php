<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\StoredFile;
use App\Models\User;
use App\Services\Interfaces\FileStorageServiceInterface;
use Carbon\Carbon;
use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Storage\Bucket;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Google Cloud Storage Service
 *
 * Handles file storage operations using Google Cloud Storage (GCS).
 *
 * Note: Requires google/cloud-storage package
 * Install via: composer require google/cloud-storage
 *
 * Requires GCP credentials JSON file:
 * - Set GOOGLE_CLOUD_KEY_FILE in .env to path to credentials JSON
 * - Or set GOOGLE_APPLICATION_CREDENTIALS environment variable
 */
class GoogleCloudStorageService implements FileStorageServiceInterface
{
    private StorageClient $storageClient;
    private Bucket $bucket;
    private string $bucketName;
    private ?string $publicBaseUrl;

    public function __construct()
    {
        $this->initializeGcsClient();
    }

    private function initializeGcsClient(): void
    {
        try {
            // Initialize GCS client
            $credentialsPath = config('services.gcs.key_file');
            
            if ($credentialsPath && file_exists($credentialsPath)) {
                $this->storageClient = new StorageClient([
                    'keyFilePath' => $credentialsPath,
                ]);
            } else {
                // Use default credentials (e.g., from GOOGLE_APPLICATION_CREDENTIALS env var)
                $this->storageClient = new StorageClient();
            }

            $this->bucketName = config('services.gcs.bucket', 'toms-lms');
            $this->publicBaseUrl = config('services.gcs.public_base_url');

            // Get bucket
            $this->bucket = $this->storageClient->bucket($this->bucketName);

            // Ensure bucket exists (or create it if it doesn't)
            if (!$this->bucket->exists()) {
                Log::warning('GCS bucket does not exist', ['bucket' => $this->bucketName]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to initialize Google Cloud Storage client', [
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to initialize Google Cloud Storage: ' . $e->getMessage());
        }
    }

    /**
     * Upload a file to Google Cloud Storage.
     */
    public function upload(string $path, UploadedFile $file, User $uploadedBy): StoredFile
    {
        try {
            // Generate unique object name
            $extension = $file->getClientOriginalExtension();
            $objectName = $this->generateObjectName($path, $extension);

            // Read file content
            $fileContent = file_get_contents($file->getPathname());
            if ($fileContent === false) {
                throw new \Exception('Failed to read uploaded file');
            }

            // Upload to GCS
            $object = $this->bucket->upload($fileContent, [
                'name' => $objectName,
                'metadata' => [
                    'contentType' => $file->getMimeType(),
                    'metadata' => [
                        'original_filename' => $file->getClientOriginalName(),
                        'uploaded_by' => (string) $uploadedBy->id,
                    ],
                ],
            ]);

            // Make object publicly readable if needed
            if (config('services.gcs.make_public', false)) {
                $object->update(['acl' => []], ['predefinedAcl' => 'publicRead']);
            }

            // Create StoredFile entity
            $storedFile = StoredFile::create([
                'original_filename' => $file->getClientOriginalName(),
                'stored_path' => $objectName,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploader_id' => $uploadedBy->id,
            ]);

            Log::info('File uploaded successfully to Google Cloud Storage', [
                'object_name' => $objectName,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'user_id' => $uploadedBy->id,
            ]);

            return $storedFile;
        } catch (\Exception $e) {
            Log::error('Google Cloud Storage upload failed', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
            ]);
            throw new \Exception('Failed to upload file to cloud storage: ' . $e->getMessage());
        }
    }

    /**
     * Delete a file from Google Cloud Storage.
     */
    public function delete(string $objectName): void
    {
        try {
            // Delete from GCS
            $object = $this->bucket->object($objectName);
            if ($object->exists()) {
                $object->delete();
            }

            // Find and remove from database
            $storedFile = StoredFile::where('stored_path', $objectName)->first();
            if ($storedFile) {
                $storedFile->delete();
            }

            Log::info('File deleted successfully from Google Cloud Storage', [
                'object_name' => $objectName,
            ]);
        } catch (\Exception $e) {
            Log::error('Google Cloud Storage deletion failed', [
                'object_name' => $objectName,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to delete file from cloud storage: ' . $e->getMessage());
        }
    }

    /**
     * Generate a temporary signed URL for a file.
     */
    public function temporaryUrl(string $objectName, \DateInterval $ttl): string
    {
        try {
            $object = $this->bucket->object($objectName);
            
            if (!$object->exists()) {
                throw new \Exception('Object does not exist: ' . $objectName);
            }

            // Calculate expiration time
            $expiration = Carbon::now()->add($ttl);

            // Generate signed URL
            $signedUrl = $object->signedUrl($expiration->toDateTime());

            return $signedUrl;
        } catch (\Exception $e) {
            Log::error('Failed to generate signed URL', [
                'object_name' => $objectName,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to generate temporary download link: ' . $e->getMessage());
        }
    }

    /**
     * Get the public URL for a file.
     */
    public function getPublicUrl(string $objectName): string
    {
        if (!empty($this->publicBaseUrl)) {
            return rtrim($this->publicBaseUrl, '/') . '/' . $objectName;
        }

        // Default GCS public URL format
        return sprintf(
            'https://storage.googleapis.com/%s/%s',
            $this->bucketName,
            $objectName
        );
    }

    /**
     * Generate a download URL with TTL.
     */
    public function downloadUrl(string $objectName, int $ttlMinutes = 60): string
    {
        $ttl = new \DateInterval('PT' . $ttlMinutes . 'M');
        return $this->temporaryUrl($objectName, $ttl);
    }

    /**
     * Copy a file within Google Cloud Storage.
     */
    public function copyFile(string $sourceObjectName, string $destinationPath, ?string $newExtension = null): StoredFile
    {
        try {
            $sourceObject = $this->bucket->object($sourceObjectName);
            
            if (!$sourceObject->exists()) {
                throw new \Exception('Source object does not exist: ' . $sourceObjectName);
            }

            // Get source file metadata from database
            $sourceFile = StoredFile::where('stored_path', $sourceObjectName)->first();
            if (!$sourceFile) {
                throw new \Exception('Source file not found in database');
            }

            // Generate new object name
            $extension = $newExtension ?: pathinfo($sourceObjectName, PATHINFO_EXTENSION);
            $newObjectName = $this->generateObjectName($destinationPath, $extension);

            // Copy object in GCS
            $sourceObject->copy($this->bucket, [
                'name' => $newObjectName,
            ]);

            // Create new StoredFile entity
            $newStoredFile = StoredFile::create([
                'original_filename' => $sourceFile->original_filename,
                'stored_path' => $newObjectName,
                'mime_type' => $sourceFile->mime_type,
                'file_size' => $sourceFile->file_size,
                'uploader_id' => $sourceFile->uploader_id,
            ]);

            Log::info('File copied successfully in Google Cloud Storage', [
                'source' => $sourceObjectName,
                'destination' => $newObjectName,
            ]);

            return $newStoredFile;
        } catch (\Exception $e) {
            Log::error('Google Cloud Storage copy failed', [
                'source' => $sourceObjectName,
                'destination' => $destinationPath,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to copy file: ' . $e->getMessage());
        }
    }

    /**
     * Check if a file exists in Google Cloud Storage.
     */
    public function fileExists(string $objectName): bool
    {
        try {
            $object = $this->bucket->object($objectName);
            return $object->exists();
        } catch (\Exception $e) {
            Log::error('Failed to check file existence', [
                'object_name' => $objectName,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get file properties/metadata.
     */
    public function getFileProperties(string $objectName): array
    {
        try {
            $object = $this->bucket->object($objectName);
            
            if (!$object->exists()) {
                throw new \Exception('Object does not exist: ' . $objectName);
            }

            $info = $object->info();

            return [
                'size' => $info['size'] ?? 0,
                'content_type' => $info['contentType'] ?? 'application/octet-stream',
                'last_modified' => isset($info['updated']) ? Carbon::parse($info['updated']) : null,
                'etag' => $info['etag'] ?? null,
                'metadata' => $info['metadata'] ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get object properties', [
                'object_name' => $objectName,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to get file properties: ' . $e->getMessage());
        }
    }

    /**
     * Test the connection to Google Cloud Storage.
     */
    public function testConnection(): bool
    {
        try {
            // Try to list objects (limit to 1 to minimize data transfer)
            $iterator = $this->bucket->objects([
                'maxResults' => 1,
            ]);
            
            // Just check if we can iterate (even if empty)
            iterator_count($iterator);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Google Cloud Storage connection test failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Generate a unique object name.
     */
    private function generateObjectName(string $path, string $extension): string
    {
        $timestamp = now()->format('Y/m/d/H/i/s');
        $random = bin2hex(random_bytes(8));

        $path = trim($path, '/');
        return sprintf('%s/%s_%s.%s', $path, $timestamp, $random, $extension);
    }

    /**
     * Get bucket URL.
     */
    public function getBucketUrl(): string
    {
        return sprintf(
            'https://storage.googleapis.com/%s',
            $this->bucketName
        );
    }
}

