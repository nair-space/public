<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Interfaces\ClientBioRepositoryInterface;
use App\Models\ClientBio;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ClientBioService
{
    private const CACHE_TTL = 3600; // 1 hour

    public function __construct(
        private readonly ClientBioRepositoryInterface $clientBioRepository
    ) {
    }

    public function getClient(string $clientId): ?ClientBio
    {
        return Cache::remember(
            "client_bio_{$clientId}",
            self::CACHE_TTL,
            fn() => $this->clientBioRepository->findById($clientId)
        );
    }

    public function getPaginatedClients(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        // No caching for paginated/filtered results
        return $this->clientBioRepository->getPaginated($perPage, $filters);
    }

    public function createClient(array $data): ClientBio
    {
        // Generate client_id if not provided
        if (empty($data['client_id'])) {
            $data['client_id'] = $this->generateClientId();
        }

        $client = $this->clientBioRepository->create($data);

        // Clear related caches
        $this->clearClientCaches();

        return $client;
    }

    public function updateClient(string $clientId, array $data): bool
    {
        if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
            $data['photo_path'] = $this->processPhoto($data['photo'], $clientId);
            $data['ada_foto'] = 'ya';
            unset($data['photo']);
        }

        $result = $this->clientBioRepository->update($clientId, $data);

        if ($result) {
            Cache::forget("client_bio_{$clientId}");
            $this->clearClientCaches();
        }

        return $result;
    }

    private function processPhoto(\Illuminate\Http\UploadedFile $file, string $clientId): string
    {
        if (!extension_loaded('gd')) {
            throw new \Exception("The PHP GD extension is not enabled. Please enable it in your php.ini file.");
        }

        $image = null;
        $realPath = $file->getRealPath();

        $imageInfo = @getimagesize($realPath);
        if (!$imageInfo) {
            throw new \Exception("The file is not a valid image.");
        }

        $mime = $imageInfo['mime'];

        if ($mime === 'image/jpeg') {
            $image = \imagecreatefromjpeg($realPath);
        } elseif ($mime === 'image/png') {
            $image = @\imagecreatefrompng($realPath);
            if (!$image) {
                // Some PNGs might be corrupt or use unsupported features, let's try to handle potential errors
                // For example, if it's actually a JPEG with a PNG extension
                throw new \Exception("Failed to process PNG image. It might be corrupted or in an unsupported format.");
            }
            // Handle transparency for PNG
            \imagepalettetotruecolor($image);
            \imagealphablending($image, true);
            \imagesavealpha($image, true);
        } elseif ($mime === 'image/webp') {
            $image = \imagecreatefromwebp($realPath);
        }

        if (!$image) {
            throw new \Exception("Unsupported image format ({$mime}) or failed to create image resource.");
        }

        // Get original dimensions
        $width = \imagesx($image);
        $height = \imagesy($image);

        // If image is very large, let's downscale it first to help compression
        $maxDimension = 800;
        if ($width > $maxDimension || $height > $maxDimension) {
            $ratio = $width / $height;
            if ($ratio > 1) {
                $newWidth = $maxDimension;
                $newHeight = (int) ($maxDimension / $ratio);
            } else {
                $newHeight = $maxDimension;
                $newWidth = (int) ($maxDimension * $ratio);
            }
            $resizedImage = \imagecreatetruecolor($newWidth, $newHeight);
            \imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            \imagedestroy($image);
            $image = $resizedImage;
        }

        // Convert to WebP and compress iterative
        $tempPath = tempnam(sys_get_temp_dir(), 'photo_');
        $quality = 80;
        $downscaleCount = 0;

        do {
            \imagewebp($image, $tempPath, $quality);
            $size = filesize($tempPath);
            $quality -= 5;

            // If quality gets too low, we might need to downscale further if still too big
            if ($quality < 5 && $size > 50 * 1024 && $downscaleCount < 5) {
                $width = \imagesx($image);
                $height = \imagesy($image);
                $newWidth = (int) ($width * 0.7);
                $newHeight = (int) ($height * 0.7);

                if ($newWidth < 10 || $newHeight < 10) {
                    break; // Stop if image gets too small
                }

                $resizedImage = \imagecreatetruecolor($newWidth, $newHeight);
                \imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                \imagedestroy($image);
                $image = $resizedImage;
                $quality = 60; // Reset quality for smaller image
                $downscaleCount++;
            }
        } while ($size > 50 * 1024 && $quality > 0);

        \imagedestroy($image);

        $fileName = "photos/client_bios/{$clientId}.webp";
        \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, file_get_contents($tempPath));
        unlink($tempPath);

        return $fileName;
    }

    public function deleteClient(string $clientId): bool
    {
        $result = $this->clientBioRepository->delete($clientId);

        if ($result) {
            Cache::forget("client_bio_{$clientId}");
            $this->clearClientCaches();
        }

        return $result;
    }

    public function getStatsByProvinsi(): array
    {
        return Cache::remember(
            'client_stats_provinsi',
            self::CACHE_TTL,
            fn() => $this->clientBioRepository->getCountByProvinsi()
        );
    }

    public function getStatsByDisabilitas(): array
    {
        return Cache::remember(
            'client_stats_disabilitas',
            self::CACHE_TTL,
            fn() => $this->clientBioRepository->getCountByDisabilitas()
        );
    }

    private function generateClientId(): string
    {
        return 'CLN' . strtoupper(Str::random(7));
    }

    private function clearClientCaches(): void
    {
        Cache::forget('client_stats_provinsi');
        Cache::forget('client_stats_disabilitas');
        Cache::forget('dashboard_stats');
    }
}
