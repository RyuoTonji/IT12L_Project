<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PayMongoService
{
    protected $baseUrl = 'https://api.paymongo.com/v1';
    protected $secretKey;

    public function __construct()
    {
        $this->secretKey = config('services.paymongo.secret_key');
    }

    /**
     * Create a GCash/PayMaya Source
     * @param float $amount Amount in PHP
     * @param string $description Payment description
     * @param int $orderId Order ID
     * @param string $type Payment source type: 'gcash', 'paymaya', 'qr_ph'
     * @param array $billingData Optional billing data [name, email, phone]
     */
    public function createSource($amount, $description, $orderId, $type = 'qr_ph', $billingData = [])
    {
        // Special handling for QRPh which uses Payment Intent API
        if ($type === 'qr_ph') {
            return $this->createQRPhPayment($amount, $description, $orderId, $billingData);
        }

        try {
            $payload = [
                'data' => [
                    'attributes' => [
                        'amount' => $amount * 100, // PayMongo uses cents
                        'type' => $type, // 'gcash' or 'paymaya'
                        'currency' => 'PHP',
                        'billing' => [
                            'name' => $billingData['name'] ?? Auth::user()->name ?? 'Guest',
                            'email' => $billingData['email'] ?? Auth::user()->email ?? '',
                            'phone' => $billingData['phone'] ?? Auth::user()->phone ?? '',
                        ],
                        'redirect' => [
                            'success' => route('checkout.confirm', ['order_id' => $orderId]),
                            'failed' => route('checkout.index'),
                        ],
                    ],
                ],
            ];

            Log::info('PayMongo Source Creation Request', ['payload' => $payload, 'type' => $type]);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/sources', $payload);

            Log::info('PayMongo Source Creation Response', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if (!$response->successful()) {
                Log::error('PayMongo Source Creation Failed - API Error', [
                    'status' => $response->status(),
                    'error' => $response->json(),
                    'type' => $type,
                    'amount' => $amount,
                    'order_id' => $orderId
                ]);
                return null;
            }

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('PayMongo Source Creation Failed - Exception', [
                'error' => $e->getMessage(),
                'type' => $type,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Specialized flow for QRPh using Payment Intents
     */
    public function createQRPhPayment($amount, $description, $orderId, $billingData = [])
    {
        try {
            // 1. Create Payment Intent
            $intentPayload = [
                'data' => [
                    'attributes' => [
                        'amount' => $amount * 100,
                        'payment_method_allowed' => ['qrph'],
                        'currency' => 'PHP',
                        'description' => $description,
                        'statement_descriptor' => 'BBQ Lagao',
                    ],
                ],
            ];

            $intentResponse = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/payment_intents', $intentPayload);

            Log::info('PayMongo PI Creation Response', ['body' => $intentResponse->json()]);

            if (!$intentResponse->successful()) {
                Log::error('PayMongo PI Creation Failed', ['error' => $intentResponse->json()]);
                return null;
            }

            $intentId = $intentResponse->json()['data']['id'];

            // 2. Create Payment Method
            $methodPayload = [
                'data' => [
                    'attributes' => [
                        'type' => 'qrph',
                        'billing' => [
                            'name' => $billingData['name'] ?? Auth::user()->name ?? 'Guest',
                            'email' => $billingData['email'] ?? Auth::user()->email ?? '',
                            'phone' => $billingData['phone'] ?? Auth::user()->phone ?? '',
                        ],
                    ],
                ],
            ];

            $methodResponse = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/payment_methods', $methodPayload);

            Log::info('PayMongo PM Creation Response', ['body' => $methodResponse->json()]);

            if (!$methodResponse->successful()) {
                Log::error('PayMongo PM Creation Failed', ['error' => $methodResponse->json()]);
                return null;
            }

            $methodId = $methodResponse->json()['data']['id'];

            // 3. Attach PM to PI
            $attachPayload = [
                'data' => [
                    'attributes' => [
                        'payment_method' => $methodId,
                        'return_url' => route('checkout.confirm', ['order_id' => $orderId]),
                    ],
                ],
            ];

            $attachResponse = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . "/payment_intents/{$intentId}/attach", $attachPayload);

            Log::info('PayMongo PI Attach Response', ['body' => $attachResponse->json()]);

            if (!$attachResponse->successful()) {
                Log::error('PayMongo PI Attach Failed', ['error' => $attachResponse->json()]);
                return null;
            }

            $data = $attachResponse->json();

            // Extract the QR data (can be show_qr.data or code.image_url depending on the method)
            $nextAction = $data['data']['attributes']['next_action'] ?? null;
            $qrData = null;

            if ($nextAction) {
                if (isset($nextAction['show_qr']['data'])) {
                    $qrData = $nextAction['show_qr']['data'];
                } elseif (isset($nextAction['code']['image_url'])) {
                    $qrData = $nextAction['code']['image_url'];
                }
            }

            // Map the response to a legacy source-like structure for the controller
            return [
                'data' => [
                    'id' => $intentId, // Use Intent ID as Source ID for polling
                    'attributes' => [
                        'type' => 'qr_ph',
                        'status' => 'pending',
                        'redirect' => [
                            'checkout_url' => $qrData
                        ]
                    ]
                ],
                'is_payment_intent' => true
            ];
        } catch (\Exception $e) {
            Log::error('PayMongo QRPh Flow Failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get Source/PaymentIntent Status
     */
    public function getSourceStatus($sourceId)
    {
        try {
            // Check if it's a Payment Intent (QRPh) or Source (GCash/Maya)
            $endpoint = str_starts_with($sourceId, 'src_') ? "/sources/{$sourceId}" : "/payment_intents/{$sourceId}";

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
            ])->get($this->baseUrl . $endpoint);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('PayMongo Status Check Failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Create a Payment from a chargeable source
     */
    public function createPayment($amount, $sourceId, $description)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/payments', [
                        'data' => [
                            'attributes' => [
                                'amount' => $amount * 100,
                                'source' => [
                                    'id' => $sourceId,
                                    'type' => 'source',
                                ],
                                'currency' => 'PHP',
                                'description' => $description,
                            ],
                        ],
                    ]);

            Log::info('PayMongo Payment Creation Response', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if (!$response->successful()) {
                Log::error('PayMongo Payment Creation Failed - API Error', [
                    'status' => $response->status(),
                    'error' => $response->json(),
                    'source_id' => $sourceId,
                    'amount' => $amount
                ]);
                return null;
            }

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('PayMongo Payment Creation Failed', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
