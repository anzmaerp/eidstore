<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\PaymentRequest;

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

    /**
     * Create a Fawaterk invoice and return its ID.
     */
    public function createInvoice(PaymentRequest $payment, array $payer, array $cartItems): array
    {
        try {
            $payload = [
                'vendorKey' => $this->vendorKey,
                'cartItems' => $cartItems,
                'customer'  => [
                    'name'  => $payer['name'] ?? 'Customer',
                    'email' => $payer['email'] ?? 'example@example.com',
                    'phone' => $payer['phone'] ?? '0000000000',
                ],
                'currency' => $payment->currency_code,
                'amount'   => $payment->payment_amount,
                'successUrl' => route('fawaterk.success'),
                'failUrl'    => route('fawaterk.failed'),
                // 'webhookUrl' => route('fawaterk.webhook'),
            ];

            Log::info("Sending createInvoice request to Fawaterk", ['payload' => $payload]);

            // Simulated API response
            $response = [
                'success' => true,
                'data' => [
                    'invoice_id' => rand(100000, 999999),
                    'url'        => "{$this->baseUrl}/invoice/" . rand(100000, 999999),
                ],
            ];

            if (!empty($response['success'])) {
                Log::info("Fawaterk invoice created", $response['data']);
                return [
                    'invoice_id'  => $response['data']['invoice_id'],
                    'payment_url' => $response['data']['url'],
                ];
            }

            return ['error' => true, 'message' => 'Failed to create invoice'];

        } catch (\Exception $e) {
            Log::error("Fawaterk createInvoice exception", ['error' => $e->getMessage()]);
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }
}
