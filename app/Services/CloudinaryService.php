<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    /**
     * Upload an image file to Cloudinary if CLOUDINARY_URL is configured,
     * otherwise fallback to local public/uploads storage.
     *
     * @param UploadedFile $file
     * @param string $folder
     * @return string Permanent Image URL
     */
    public static function upload(UploadedFile $file, string $folder = 'products'): string
    {
        $cloudinaryUrl = env('CLOUDINARY_URL');

        if (!empty($cloudinaryUrl)) {
            try {
                // Parse CLOUDINARY_URL (format: cloudinary://API_KEY:API_SECRET@CLOUD_NAME)
                $parsed = parse_url($cloudinaryUrl);
                $cloudName = $parsed['host'] ?? null;
                $apiKey    = $parsed['user'] ?? null;
                $apiSecret = $parsed['pass'] ?? null;

                if ($cloudName && $apiKey && $apiSecret) {
                    $timestamp = time();
                    // Signature parameters must be alphabetized by key: folder, timestamp
                    $paramsToSign = "folder={$folder}&timestamp={$timestamp}";
                    $signature = sha1($paramsToSign . $apiSecret);

                    $response = Http::attach(
                        'file',
                        file_get_contents($file->getRealPath()),
                        $file->getClientOriginalName()
                    )->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload", [
                        'api_key'   => $apiKey,
                        'timestamp' => $timestamp,
                        'folder'    => $folder,
                        'signature' => $signature,
                    ]);

                    if ($response->successful() && isset($response->json()['secure_url'])) {
                        return $response->json()['secure_url'];
                    }

                    Log::error('Cloudinary Upload Failed: ' . $response->body());
                }
            } catch (\Throwable $e) {
                Log::error('Cloudinary Upload Exception: ' . $e->getMessage());
            }
        }

        // Fallback to local storage
        $filename = uniqid($folder . '_') . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/' . $folder), $filename);
        return '/uploads/' . $folder . '/' . $filename;
    }
}
