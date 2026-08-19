<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class LalamoveService
{
    protected string $apiKey;
    protected string $apiSecret;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = (string) (config('services.lalamove.key') ?? '');
        $this->apiSecret = (string) (config('services.lalamove.secret') ?? '');
        $env = config('services.lalamove.env', 'sandbox');
        $this->baseUrl = $env === 'production' 
            ? 'https://rest.lalamove.com' 
            : 'https://rest.sandbox.lalamove.com';
    }

    /**
     * Check if Same-Day Lalamove dispatch is currently active (8:00 AM - 4:00 PM Asia/Manila)
     */
    public function isSameDayWindowActive(): array
    {
        $now = Carbon::now('Asia/Manila');
        $hour = $now->hour;

        $isActive = ($hour >= 8 && $hour < 16);

        if ($isActive) {
            $message = "🛵 Lalamove Same-Day Delivery ACTIVE (Cutoff: 4:00 PM)";
            $subtext = "Orders placed before 4:00 PM are dispatched today via Lalamove!";
        } elseif ($hour < 8) {
            $message = "🌙 Lalamove Dispatch Opens at 8:00 AM";
            $subtext = "Your order will be picked up by Lalamove starting 8:00 AM today.";
        } else {
            $message = "🌙 Next-Day Lalamove Dispatch (8:00 AM Tomorrow)";
            $subtext = "Cutoff was 4:00 PM. Your order will be dispatched via Lalamove at 8:00 AM tomorrow.";
        }

        return [
            'is_active' => $isActive,
            'current_time' => $now->format('g:i A'),
            'message' => $message,
            'subtext' => $subtext,
        ];
    }

    /**
     * Fetch order status from Lalamove API (or mock tracking if API credentials are not set)
     */
    public function getTrackingStatus(string $trackingNumber, ?int $forcedStep = null): array
    {
        if (empty($this->apiKey) || empty($this->apiSecret)) {
            return $this->getMockLalamoveTracking($trackingNumber, $forcedStep);
        }

        try {
            $timestamp = time() * 1000;
            $path = "/v3/orders/{$trackingNumber}";
            $rawSignature = "{$timestamp}\r\nGET\r\n{$path}\r\n\r\n";
            $signature = hash_hmac('sha256', $rawSignature, $this->apiSecret);

            $response = Http::withHeaders([
                'Authorization' => "HMAC {$this->apiKey}:{$timestamp}:{$signature}",
                'Market' => 'PH',
                'Content-Type' => 'application/json',
            ])->get("{$this->baseUrl}{$path}");

            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];
                return [
                    'success' => true,
                    'status' => $data['status'] ?? 'ON_GO',
                    'driver_name' => $data['driver']['name'] ?? 'Lalamove Rider',
                    'driver_phone' => $data['driver']['phone'] ?? '09XX-XXX-XXXX',
                    'driver_plate' => $data['driver']['plateNumber'] ?? 'MC-12345',
                    'share_url' => $data['shareLink'] ?? null,
                    'updated_at' => now()->format('M d, Y h:i A'),
                ];
            }
        } catch (\Exception $e) {
            // Fall back to mock response on error
        }

        return $this->getMockLalamoveTracking($trackingNumber, $forcedStep);
    }

    /**
     * Realistic Lalamove tracking simulator for test environment
     */
    protected function getMockLalamoveTracking(string $trackingNumber, ?int $forcedStep = null): array
    {
        $statuses = [
            1 => [
                'code' => 'ASSIGNING_DRIVER',
                'step' => 1,
                'title' => 'Finding Nearby Lalamove Rider 🛵',
                'desc' => 'Searching for a Lalamove rider near RAI warehouse in Metro Manila...',
                'badge' => 'warning',
            ],
            2 => [
                'code' => 'ON_GO',
                'step' => 2,
                'title' => 'Rider On The Way to Pickup 📦',
                'desc' => 'Lalamove rider Kuya Mark is heading to RAI Motorcycle Parts warehouse.',
                'badge' => 'info',
            ],
            3 => [
                'code' => 'PICKED_UP',
                'step' => 3,
                'title' => 'Package Picked Up & In Transit 💨',
                'desc' => 'Your motorcycle parts have been picked up and are en route to your delivery address!',
                'badge' => 'primary',
            ],
            4 => [
                'code' => 'COMPLETED',
                'step' => 4,
                'title' => 'Delivered Successfully 🎉',
                'desc' => 'Package delivered to recipient. Thank you for riding with RAI Motorcycle Parts!',
                'badge' => 'success',
            ],
        ];

        if ($forcedStep && isset($statuses[$forcedStep])) {
            $selected = $statuses[$forcedStep];
        } else {
            $stepIndex = (abs(crc32($trackingNumber)) % 4) + 1;
            $selected = $statuses[$stepIndex];
        }

        return [
            'success' => true,
            'is_mock' => true,
            'tracking_number' => $trackingNumber,
            'status_code' => $selected['code'],
            'step' => $selected['step'],
            'title' => $selected['title'],
            'description' => $selected['desc'],
            'badge' => $selected['badge'],
            'driver' => [
                'name' => 'Kuya Mark (Lalamove PH)',
                'phone' => '0917-889-2041',
                'vehicle' => 'Yamaha NMAX (Plate: 882-XYZ)',
            ],
            'eta' => ($selected['step'] === 4) ? 'Delivered' : now()->addMinutes(35)->format('g:i A'),
            'updated_at' => now()->format('M d, Y g:i A'),
        ];
    }
}
