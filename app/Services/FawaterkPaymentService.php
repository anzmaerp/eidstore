<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FawaterkPaymentService
{
    protected string $vendorKey = '';
    protected string $providerKey = '';
    protected string $baseUrl = '';
    protected string $mode = 'test';

    public function __construct()
    {
        Log::info("FawaterkPaymentService::__construct called");

        $paymentMethods = DB::table("addon_settings as os")
            ->where('key_name', 'fawaterk')
            ->select('live_values', 'test_values', 'mode')
            ->first();

        if ($paymentMethods) {
            $this->mode = $paymentMethods->mode;
            Log::info("Fawaterk mode detected", ['mode' => $this->mode]);

            $decoded = $this->mode === 'live'
                ? json_decode($paymentMethods->live_values, true)
                : json_decode($paymentMethods->test_values, true);

            $this->vendorKey   = $decoded['vendor_key'] ?? '';
            $this->providerKey = $decoded['provider_key'] ?? '';
            $this->baseUrl     = $this->mode === 'live'
                ? 'https://app.fawaterk.com'
                : 'https://staging.fawaterk.com';

            Log::info("Fawaterk keys loaded", [
                'vendorKey'   => substr($this->vendorKey, 0, 6) . '***',
                'providerKey' => substr($this->providerKey, 0, 6) . '***',
                'baseUrl'     => $this->baseUrl,
            ]);
        } else {
            Log::warning("FawaterkPaymentService: No payment method found in DB");
        }
    }

    public function getKeys(): array
    {
        Log::info("Fawaterk getKeys called");
        return [
            'vendor_key'   => $this->vendorKey,
            'provider_key' => $this->providerKey,
            'domain'       => parse_url(url('/'), PHP_URL_HOST),
            'mode'         => $this->mode,
        ];
    }

    public function generateTransactionReference(): string
    {
        $ref = 'FAW-' . uniqid() . '-' . now()->timestamp;
        Log::info("Generated transaction reference", ['ref' => $ref]);
        return $ref;
    }

    public function generateHashKey(): string
    {
        $keys = $this->getKeys();
        $secretKey   = $keys['vendor_key'];
        $domain      = $keys['domain'];
        $providerKey = $keys['provider_key'];

        $queryParam = "Domain={$domain}&ProviderKey={$providerKey}";
        $hash = hash_hmac('sha256', $queryParam, $secretKey, false);

        Log::info("Generated hash key", ['queryParam' => $queryParam, 'hash' => $hash]);
        return $hash;
    }

    public function chargeCustomer(array $params)
    {
        Log::info("ChargeCustomer called", ['params' => $params]);

        $paymentData = session('payment_data');
        Log::info("ChargeCustomer session data", ['paymentData' => $paymentData]);

        return redirect()->route('fawaterk.iframe', $paymentData['order_id'] ?? null);
    }

    public function paymentResponse(array $params): array
    {
        Log::info("PaymentResponse called", ['params' => $params]);

        $status = match(strtolower($params['status'] ?? 'pending')) {
            'success' => 200,
            'fail'    => 400,
            'pending' => 202,
            default   => 204
        };

        $response = [
            'status' => $status,
            'data'   => [
                'transaction_id' => $params['transaction_id'] ?? null,
                'order_id'       => session()->get('fawaterk_subscription_id'),
            ],
            'message' => $params['message'] ?? 'Transaction is being processed',
        ];

        Log::info("PaymentResponse result", $response);
        return $response;
    }

    public function driverName(): string
    {
        return 'fawaterk';
    }
}
