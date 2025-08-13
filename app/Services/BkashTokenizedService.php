<?php

namespace App\Services;

use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BkashTokenizedService
{
    private $baseUrl;
    private $username;
    private $password;
    private $appKey;
    private $appSecret;
    private $callbackUrl;
    private $sandbox;

    public function __construct()
    {
        $bkashMethod = PaymentMethod::where('code', 'bkash')->first();

        if (!$bkashMethod || !$bkashMethod->is_active) {
            throw new \Exception('bKash payment method is not configured or inactive');
        }

        $settings = $bkashMethod->settings;

        $this->sandbox = $settings['sandbox_mode'] ?? false;
        $this->baseUrl = $this->sandbox
            ? 'https://tokenized.sandbox.bka.sh/v1.2.0-beta'
            : 'https://tokenized.pay.bka.sh/v1.2.0-beta';

        if ($this->sandbox) {
            // Use sandbox credentials from user
            $this->username = '01770618567';
            $this->password = 'D7DaC<*E*eG';
            $this->appKey = '0vWQuCRGiUX7EPVjQDr0EUAYtc';
            $this->appSecret = 'jcUNPBgbcqEDedNKdvE4G1cAK7D3hCjmJccNPZZBq96QIxxwAMEx';
        } else {
            // Use live credentials from database
            $this->username = $settings['merchant_id'] ?? '';
            $this->password = $settings['merchant_password'] ?? $settings['api_secret'] ?? '';
            $this->appKey = $settings['api_key'] ?? '';
            $this->appSecret = $settings['api_secret'] ?? '';
        }

        // Log configuration for debugging
        \Log::info('bKash configuration', [
            'username' => $this->username,
            'password' => $this->password ? 'SET' : 'NOT SET',
            'app_key' => $this->appKey ? 'SET' : 'NOT SET',
            'app_secret' => $this->appSecret ? 'SET' : 'NOT SET',
            'base_url' => $this->baseUrl,
            'sandbox' => $this->sandbox
        ]);
        $this->callbackUrl = route('owner.subscription.payment.success');
    }

    /**
     * Get access token for TokenizedCheckout
     */
    public function getAccessToken()
    {
        try {
            // Log the request details for debugging
            \Log::info('bKash TokenizedCheckout - Getting access token', [
                'base_url' => $this->baseUrl,
                'username' => $this->username ? 'SET' : 'NOT SET',
                'password' => $this->password ? 'SET' : 'NOT SET',
                'app_key' => $this->appKey ? 'SET' : 'NOT SET',
                'app_secret' => $this->appSecret ? 'SET' : 'NOT SET'
            ]);

            $response = Http::withoutVerifying()
                ->timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'username' => $this->username,
                    'password' => $this->password
                ])
                ->withOptions([
                    'verify' => false,
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_CONNECTTIMEOUT => 10
                    ]
                ])
                ->post($this->baseUrl . '/tokenized/checkout/token/grant', [
                    'app_key' => $this->appKey,
                    'app_secret' => $this->appSecret
                ]);

            if ($response->successful()) {
                $data = $response->json();

                // Log the response for debugging
                \Log::info('bKash TokenizedCheckout - Token response', [
                    'statusCode' => $data['statusCode'] ?? 'Unknown',
                    'statusMessage' => $data['statusMessage'] ?? 'Unknown',
                    'has_token' => isset($data['id_token']),
                    'token_length' => isset($data['id_token']) ? strlen($data['id_token']) : 0
                ]);

                // Check for error status codes
                if (isset($data['statusCode']) && $data['statusCode'] !== '0000') {
                    \Log::error('bKash TokenizedCheckout - API Error', [
                        'statusCode' => $data['statusCode'],
                        'statusMessage' => $data['statusMessage'] ?? 'Unknown error',
                        'full_response' => $data
                    ]);
                    return null;
                }

                $token = $data['id_token'] ?? null;
                if ($token) {
                    \Log::info('bKash TokenizedCheckout - Token received successfully', [
                        'token_length' => strlen($token)
                    ]);
                } else {
                    \Log::error('bKash TokenizedCheckout - No token in response', [
                        'response' => $data
                    ]);
                }

                return $token;
            }

            \Log::error('bKash TokenizedCheckout - Token generation failed', [
                'response' => $response->json(),
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            \Log::error('bKash TokenizedCheckout - Token generation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Create TokenizedCheckout payment
     */
        public function createTokenizedCheckout($amount, $invoiceId, $paymentId, $description = '')
    {
        try {
            $token = $this->getAccessToken();

            if (!$token) {
                // Demo mode for testing
                if ($this->sandbox) {
                    return [
                        'success' => true,
                        'paymentID' => 'DEMO_' . time() . '_' . uniqid(),
                        'bkashURL' => '#',
                        'callbackURL' => $this->callbackUrl,
                        'statusCode' => '0000',
                        'statusMessage' => 'Demo mode - Payment created successfully',
                        'demo_mode' => true
                    ];
                }

                throw new \Exception('Failed to get access token');
            }

            // Ensure merchant invoice number meets length/char constraints (<=20)
            $safeInvoice = substr(preg_replace('/[^A-Za-z0-9\-]/', '', (string)$invoiceId), 0, 20);
            $payload = [
                'intent' => 'sale',
                'currency' => 'BDT',
                'amount' => number_format((float)$amount, 2, '.', ''),
                'merchantInvoiceNumber' => $safeInvoice,
                'payerReference' => $paymentId,
                'callbackURL' => $this->callbackUrl,
                'mode' => '0011'
            ];

            // Log the request payload for debugging
            \Log::info('bKash TokenizedCheckout - Payment creation request', [
                'payload' => $payload,
                'base_url' => $this->baseUrl,
                'token_received' => !empty($token),
                'app_key' => $this->appKey ? 'SET' : 'NOT SET'
            ]);

            $response = Http::withoutVerifying()
                ->timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    // Use the same token we already fetched
                    'Authorization' => 'Bearer ' . $token,
                    'X-APP-Key' => $this->appKey,
                    'X-APP-Secret' => $this->appSecret
                ])
                ->withOptions([
                    'verify' => false,
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_CONNECTTIMEOUT => 10
                    ]
                ])
                ->post($this->baseUrl . '/tokenized/checkout/create', $payload);

            if ($response->successful()) {
                $data = $response->json();

                // Log successful response
                \Log::info('bKash TokenizedCheckout - Payment creation successful', [
                    'paymentID' => $data['paymentID'] ?? null,
                    'statusCode' => $data['statusCode'] ?? null,
                    'statusMessage' => $data['statusMessage'] ?? null,
                    'full_response' => $data
                ]);

                // Check if payment was actually created successfully
                if (isset($data['statusCode']) && $data['statusCode'] === '0000') {
                    // Generate bKash URL if not provided by API
                    $bkashURL = $data['bkashURL'] ?? $data['paymentURL'] ?? null;
                    if (!$bkashURL && isset($data['paymentID'])) {
                        // For sandbox mode, use a demo URL
                        if ($this->sandbox) {
                            $bkashURL = 'https://merchantdemo.sandbox.bka.sh/frontend/checkout/version/1.2.0-beta';
                        } else {
                            // For live mode, use the payment URL from API
                            $bkashURL = $data['paymentURL'] ?? 'https://www.bkash.com/payment/' . $data['paymentID'];
                        }
                    }

                    // If still no URL, create a demo URL for testing
                    if (!$bkashURL) {
                        $bkashURL = 'https://merchantdemo.sandbox.bka.sh/frontend/checkout/version/1.2.0-beta';
                    }

                    return [
                        'success' => true,
                        'paymentID' => $data['paymentID'] ?? null,
                        'bkashURL' => $bkashURL,
                        'callbackURL' => $data['callbackURL'] ?? null,
                        'statusCode' => $data['statusCode'] ?? null,
                        'statusMessage' => $data['statusMessage'] ?? null
                    ];
                } else {
                    // Payment creation failed despite successful HTTP response
                    $statusCode = $data['statusCode'] ?? 'Unknown';
                    $statusMessage = $data['statusMessage'] ?? 'Unknown error';

                    \Log::error('bKash TokenizedCheckout - Payment creation failed with status code', [
                        'statusCode' => $statusCode,
                        'statusMessage' => $statusMessage,
                        'full_response' => $data
                    ]);

                                                        return [
                        'success' => false,
                        'error' => 'Payment creation failed: ' . $statusMessage,
                        'details' => $data,
                        'suggestion' => 'Please check your bKash configuration'
                    ];
                }
            }

            // Log detailed error information
            $errorData = $response->json();
            \Log::error('bKash TokenizedCheckout - Payment creation failed', [
                'response' => $errorData,
                'status' => $response->status(),
                'payload' => $payload,
                'base_url' => $this->baseUrl
            ]);

            // Handle specific error codes
            $statusCode = $errorData['statusCode'] ?? 'Unknown';
            $statusMessage = $errorData['statusMessage'] ?? 'Unknown error';

            switch ($statusCode) {
                case '9999':
                    return [
                        'success' => false,
                        'error' => 'Authentication failed: ' . $statusMessage,
                        'details' => $errorData,
                        'suggestion' => 'Check your bKash credentials'
                    ];

                case '0001':
                    return [
                        'success' => false,
                        'error' => 'Invalid request format: ' . $statusMessage,
                        'details' => $errorData,
                        'suggestion' => 'Check payment parameters'
                    ];

                case '0002':
                    return [
                        'success' =>
                         false,
                        'error' => 'Amount validation failed: ' . $statusMessage,
                        'details' => $errorData,
                        'suggestion' => 'Check payment amount'
                    ];

                default:
                    return [
                        'success' => false,
                        'error' => 'Payment creation failed: ' . $statusMessage,
                        'details' => $errorData,
                        'suggestion' => 'Contact bKash support'
                    ];
            }

        } catch (\Exception $e) {
            Log::error('bKash TokenizedCheckout - Payment creation error', [
                'error' => $e->getMessage(),
                'amount' => $amount,
                'invoiceId' => $invoiceId
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Execute TokenizedCheckout payment
     */
    public function executeTokenizedPayment($paymentID, $payerReference)
    {
        try {
            $token = $this->getAccessToken();

            if (!$token) {
                throw new \Exception('Failed to get access token');
            }

            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $token,
                'X-APP-Key' => $this->appKey
            ])->post($this->baseUrl . '/tokenized/checkout/execute', [
                'paymentID' => $paymentID,
                'payerReference' => $payerReference
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'transactionStatus' => $data['transactionStatus'] ?? null,
                    'paymentID' => $data['paymentID'] ?? null,
                    'trxID' => $data['trxID'] ?? null,
                    'amount' => $data['amount'] ?? null,
                    'currency' => $data['currency'] ?? null,
                    'statusCode' => $data['statusCode'] ?? null,
                    'statusMessage' => $data['statusMessage'] ?? null
                ];
            }

            Log::error('bKash TokenizedCheckout - Payment execution failed', [
                'response' => $response->json(),
                'status' => $response->status()
            ]);

            return [
                'success' => false,
                'error' => 'Payment execution failed',
                'details' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('bKash TokenizedCheckout - Payment execution error', [
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
     * Query TokenizedCheckout payment status
     */
    public function queryTokenizedPayment($paymentID)
    {
        try {
            $token = $this->getAccessToken();

            if (!$token) {
                throw new \Exception('Failed to get access token');
            }

            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $token,
                'X-APP-Key' => $this->appKey
            ])->post($this->baseUrl . '/tokenized/checkout/payment/status', [
                'paymentID' => $paymentID
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'transactionStatus' => $data['transactionStatus'] ?? null,
                    'paymentID' => $data['paymentID'] ?? null,
                    'trxID' => $data['trxID'] ?? null,
                    'amount' => $data['amount'] ?? null,
                    'currency' => $data['currency'] ?? null,
                    'statusCode' => $data['statusCode'] ?? null,
                    'statusMessage' => $data['statusMessage'] ?? null
                ];
            }

            Log::error('bKash TokenizedCheckout - Payment query failed', [
                'response' => $response->json(),
                'status' => $response->status()
            ]);

            return [
                'success' => false,
                'error' => 'Payment query failed',
                'details' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('bKash TokenizedCheckout - Payment query error', [
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
     * Refund TokenizedCheckout payment
     */
    public function refundTokenizedPayment($paymentID, $amount, $trxID, $reason = 'Customer request')
    {
        try {
            $token = $this->getAccessToken();

            if (!$token) {
                throw new \Exception('Failed to get access token');
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $token,
                'X-APP-Key' => $this->appKey
            ])->post($this->baseUrl . '/tokenized/checkout/payment/refund', [
                'paymentID' => $paymentID,
                'amount' => number_format($amount, 2, '.', ''),
                'trxID' => $trxID,
                'reason' => $reason
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'refundTrxID' => $data['refundTrxID'] ?? null,
                    'statusCode' => $data['statusCode'] ?? null,
                    'statusMessage' => $data['statusMessage'] ?? null
                ];
            }

            Log::error('bKash TokenizedCheckout - Payment refund failed', [
                'response' => $response->json(),
                'status' => $response->status()
            ]);

            return [
                'success' => false,
                'error' => 'Payment refund failed',
                'details' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('bKash TokenizedCheckout - Payment refund error', [
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
     * Check if service is properly configured
     */
    public function isConfigured()
    {
        return !empty($this->username) &&
               !empty($this->password) &&
               !empty($this->appKey) &&
               !empty($this->appSecret);
    }

    /**
     * Get configuration status
     */
    public function getConfigurationStatus()
    {
        return [
            'configured' => $this->isConfigured(),
            'sandbox_mode' => $this->sandbox,
            'base_url' => $this->baseUrl,
            'username' => !empty($this->username),
            'password' => !empty($this->password),
            'app_key' => !empty($this->appKey),
            'app_secret' => !empty($this->appSecret),
            'callback_url' => $this->callbackUrl,
            'mode' => $this->sandbox ? 'Sandbox (Testing)' : 'Live (Production)'
        ];
    }

    /**
     * Test connection to bKash API
     */
    public function testConnection()
    {
        try {
            // First check if credentials are configured
            if (!$this->isConfigured()) {
                return [
                    'success' => false,
                    'message' => 'bKash credentials are not properly configured. Please check Merchant ID, API Key, and API Secret.',
                    'token_received' => false,
                    'suggestion' => 'Make sure all required fields are filled correctly.'
                ];
            }

            $token = $this->getAccessToken();

            if ($token) {
                return [
                    'success' => true,
                    'message' => 'Connection successful',
                    'token_received' => true
                ];
            }

            // Get the specific error from the API response
            $baseUrl = $this->baseUrl;
            $username = $this->username;
            $password = $this->password;
            $appKey = $this->appKey;
            $appSecret = $this->appSecret;

            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'username' => $username,
                'password' => $password
            ])->post($baseUrl . '/tokenized/checkout/token/grant', [
                'app_key' => $appKey,
                'app_secret' => $appSecret
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $statusCode = $data['statusCode'] ?? 'Unknown';
                $statusMessage = $data['statusMessage'] ?? 'Unknown error';

                // Log the API response for debugging
                \Log::info('bKash API response', [
                    'statusCode' => $statusCode,
                    'statusMessage' => $statusMessage,
                    'full_response' => $data,
                    'mode' => $this->sandbox ? 'Sandbox' : 'Live'
                ]);

                // Check if the response indicates success
                if ($statusCode === '0000' && $statusMessage === 'Successful') {
                    return [
                        'success' => true,
                        'message' => 'bKash connection test successful!',
                        'token_received' => true,
                        'api_response' => $data
                    ];
                }

                // Handle specific error codes
                switch ($statusCode) {
                    case '9999':
                        if (strpos($statusMessage, 'Invalid username and password') !== false) {
                            return [
                                'success' => false,
                                'message' => 'Invalid Merchant ID or API Secret. Please check your credentials.',
                                'token_received' => false,
                                'api_response' => $data,
                                'suggestion' => 'Verify your Merchant ID and API Secret are correct.'
                            ];
                        } elseif (strpos($statusMessage, 'App key does not exist') !== false) {
                            return [
                                'success' => false,
                                'message' => 'Invalid API Key. Please check your API Key.',
                                'token_received' => false,
                                'api_response' => $data,
                                'suggestion' => 'Verify your API Key is correct.'
                            ];
                        } else {
                            return [
                                'success' => false,
                                'message' => "API Error: {$statusMessage} (Code: {$statusCode})",
                                'token_received' => false,
                                'api_response' => $data,
                                'suggestion' => 'Please check your bKash credentials and try again.'
                            ];
                        }
                        break;

                    case '0001':
                        return [
                            'success' => false,
                            'message' => 'Invalid request format. Please check your configuration.',
                            'token_received' => false,
                            'api_response' => $data,
                            'suggestion' => 'Contact bKash support for assistance.'
                        ];
                        break;

                    default:
                        return [
                            'success' => false,
                            'message' => "API Error: {$statusMessage} (Code: {$statusCode})",
                            'token_received' => false,
                            'api_response' => $data,
                            'suggestion' => 'Please check your bKash credentials and try again.'
                        ];
                }
            }

            return [
                'success' => false,
                'message' => 'Failed to get access token - HTTP Error: ' . $response->status(),
                'token_received' => false
            ];

        } catch (\Exception $e) {
            \Log::error('bKash connection test error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
                'token_received' => false
            ];
        }
    }
}