<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    /**
     * Upload an image file to Cloudinary if configured via CLOUDINARY_URL
     * or individual keys (CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY, CLOUDINARY_API_SECRET),
     * otherwise fallback to local public/uploads storage.
     *
     * @param UploadedFile $file
     * @param string $folder
     * @return string Permanent Image URL
     */
    public static function upload(UploadedFile $file, string $folder = 'products'): string
    {
        $cloudinaryUrl = env('CLOUDINARY_URL');
        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $apiKey    = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');

        if (!empty($cloudinaryUrl) && (empty($cloudName) || empty($apiKey) || empty($apiSecret))) {
            $parsed = parse_url($cloudinaryUrl);
            $cloudName = $parsed['host'] ?? $cloudName;
            $apiKey    = $parsed['user'] ?? $apiKey;
            $apiSecret = $parsed['pass'] ?? $apiSecret;
        }

        if (!empty($cloudName) && !empty($apiKey) && !empty($apiSecret)) {
            try {
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
