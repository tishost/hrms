<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\PaymentMethod;

class BkashPaymentService
{
    private $merchantId;
    private $apiKey;
    private $apiSecret;
    private $baseUrl;
    private $isSandbox;
    private $accessToken;

    public function __construct()
    {
        $bkash = PaymentMethod::where('code', 'bkash')->first();
        if ($bkash && $bkash->is_active) {
            $this->merchantId = $bkash->settings['merchant_id'] ?? null;
            $this->apiKey = $bkash->settings['api_key'] ?? null;
            $this->apiSecret = $bkash->settings['api_secret'] ?? null;
            $this->baseUrl = $bkash->settings['gateway_url'] ?? 'https://www.bkash.com/payment';
            $this->isSandbox = $bkash->settings['is_sandbox'] ?? false;
        }
    }

    /**
     * Get access token for bKash API
     */
    public function getAccessToken()
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'username' => $this->apiKey,
                'password' => $this->apiSecret,
            ])->post($this->baseUrl . '/api/v1/token/grant', [
                'app_key' => $this->apiKey,
                'app_secret' => $this->apiSecret,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->accessToken = $data['access_token'] ?? null;
                return $this->accessToken;
            }

            Log::error('bKash access token failed', [
                'response' => $response->json(),
                'status' => $response->status()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('bKash access token error', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Create payment request
     */
    public function createPayment($amount, $invoiceNumber, $description, $callbackUrl)
    {
        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                throw new \Exception('Failed to get access token');
            }

            $paymentData = [
                'mode' => $this->isSandbox ? '0011' : '0000', // 0011 for sandbox, 0000 for live
                'payerReference' => $invoiceNumber,
                'callbackURL' => $callbackUrl,
                'amount' => number_format($amount, 2, '.', ''),
                'currency' => 'BDT',
                'intent' => 'sale',
                'merchantInvoiceNumber' => $invoiceNumber,
                'merchantAssociationInfo' => $description,
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken,
                'X-APP-Key' => $this->apiKey,
            ])->post($this->baseUrl . '/api/v1/payment/create', $paymentData);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'paymentID' => $data['paymentID'] ?? null,
                    'bkashURL' => $data['bkashURL'] ?? null,
                    'transactionStatus' => $data['transactionStatus'] ?? null,
                    'amount' => $amount,
                    'currency' => 'BDT',
                    'merchantInvoiceNumber' => $invoiceNumber,
                ];
            }

            Log::error('bKash payment creation failed', [
                'response' => $response->json(),
                'status' => $response->status()
            ]);

            return [
                'success' => false,
                'error' => 'Payment creation failed',
                'details' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('bKash payment creation error', [
                'error' => $e->getMessage(),
                'amount' => $amount,
                'invoice' => $invoiceNumber
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Execute payment
     */
    public function executePayment($paymentID, $payerReference)
    {
        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                throw new \Exception('Failed to get access token');
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken,
                'X-APP-Key' => $this->apiKey,
            ])->post($this->baseUrl . '/api/v1/payment/execute', [
                'paymentID' => $paymentID,
                'payerReference' => $payerReference,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'transactionStatus' => $data['transactionStatus'] ?? null,
                    'paymentID' => $data['paymentID'] ?? null,
                    'payerReference' => $data['payerReference'] ?? null,
                    'amount' => $data['amount'] ?? null,
                    'currency' => $data['currency'] ?? null,
                    'merchantInvoiceNumber' => $data['merchantInvoiceNumber'] ?? null,
                    'trxID' => $data['trxID'] ?? null,
                ];
            }

            Log::error('bKash payment execution failed', [
                'response' => $response->json(),
                'status' => $response->status(),
                'paymentID' => $paymentID
            ]);

            return [
                'success' => false,
                'error' => 'Payment execution failed',
                'details' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('bKash payment execution error', [
                'error' => $e->getMessage(),
                'paymentID' => $paymentID
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Query payment status
     */
    public function queryPayment($paymentID)
    {
        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                throw new \Exception('Failed to get access token');
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken,
                'X-APP-Key' => $this->apiKey,
            ])->get($this->baseUrl . '/api/v1/payment/query/' . $paymentID);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'transactionStatus' => $data['transactionStatus'] ?? null,
                    'paymentID' => $data['paymentID'] ?? null,
                    'payerReference' => $data['payerReference'] ?? null,
                    'amount' => $data['amount'] ?? null,
                    'currency' => $data['currency'] ?? null,
                    'merchantInvoiceNumber' => $data['merchantInvoiceNumber'] ?? null,
                    'trxID' => $data['trxID'] ?? null,
                ];
            }

            Log::error('bKash payment query failed', [
                'response' => $response->json(),
                'status' => $response->status(),
                'paymentID' => $paymentID
            ]);

            return [
                'success' => false,
                'error' => 'Payment query failed',
                'details' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('bKash payment query error', [
                'error' => $e->getMessage(),
                'paymentID' => $paymentID
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Refund payment
     */
    public function refundPayment($paymentID, $amount, $trxID, $sku, $reason)
    {
        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                throw new \Exception('Failed to get access token');
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken,
                'X-APP-Key' => $this->apiKey,
            ])->post($this->baseUrl . '/api/v1/payment/refund', [
                'paymentID' => $paymentID,
                'amount' => number_format($amount, 2, '.', ''),
                'trxID' => $trxID,
                'sku' => $sku,
                'reason' => $reason,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'refundTrxID' => $data['refundTrxID'] ?? null,
                    'transactionStatus' => $data['transactionStatus'] ?? null,
                    'amount' => $data['amount'] ?? null,
                    'currency' => $data['currency'] ?? null,
                ];
            }

            Log::error('bKash payment refund failed', [
                'response' => $response->json(),
                'status' => $response->status(),
                'paymentID' => $paymentID
            ]);

            return [
                'success' => false,
                'error' => 'Payment refund failed',
                'details' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('bKash payment refund error', [
                'error' => $e->getMessage(),
                'paymentID' => $paymentID
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check if bKash is configured
     */
    public function isConfigured()
    {
        return !empty($this->merchantId) &&
               !empty($this->apiKey) &&
               !empty($this->apiSecret) &&
               !empty($this->baseUrl);
    }

    /**
     * Get bKash configuration status
     */
    public function getConfigurationStatus()
    {
        $status = [
            'configured' => $this->isConfigured(),
            'merchant_id' => !empty($this->merchantId),
            'api_key' => !empty($this->apiKey),
            'api_secret' => !empty($this->apiSecret),
            'gateway_url' => !empty($this->baseUrl),
            'sandbox_mode' => $this->isSandbox,
        ];

        return $status;
    }

    /**
     * Test bKash connection
     */
    public function testConnection()
    {
        try {
            $accessToken = $this->getAccessToken();
            return [
                'success' => !empty($accessToken),
                'message' => !empty($accessToken) ? 'Connection successful' : 'Connection failed',
                'access_token' => $accessToken ? 'Available' : 'Not available'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage()
            ];
        }
    }
}
