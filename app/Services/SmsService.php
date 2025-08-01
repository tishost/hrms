<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private $apiUrl = 'https://api.smsinbd.com/sms-api/sendsms';
    private $apiToken;
    private $senderId;

    public function __construct()
    {
        $this->apiToken = config('services.sms.api_token', '');
        $this->senderId = config('services.sms.sender_id', '');
        
        // Check if API token is configured
        if (empty($this->apiToken)) {
            Log::warning('SMS API token is not configured');
        }
    }

    /**
     * Send SMS to a single number
     */
    public function sendSms($phoneNumber, $message)
    {
        try {
            $response = $this->makeApiRequest([
                'api_token' => $this->apiToken,
                'senderid' => $this->senderId,
                'contact_number' => $phoneNumber,
                'message' => $message,
            ]);

            return $this->processResponse($response);
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'SMS sending failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send SMS to multiple numbers
     */
    public function sendBulkSms($phoneNumbers, $message)
    {
        try {
            // Convert array to comma-separated string
            $numbers = is_array($phoneNumbers) ? implode(',', $phoneNumbers) : $phoneNumbers;

            $response = $this->makeApiRequest([
                'api_token' => $this->apiToken,
                'senderid' => $this->senderId,
                'contact_number' => $numbers,
                'message' => $message,
            ]);

            return $this->processResponse($response);
        } catch (\Exception $e) {
            Log::error('Bulk SMS sending failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Bulk SMS sending failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send rent reminder SMS
     */
    public function sendRentReminder($tenantName, $phoneNumber, $amount, $dueDate)
    {
        $message = "Dear {$tenantName}, your rent payment of ৳{$amount} is due on {$dueDate}. Please pay on time to avoid late fees. - Bari Manager";
        
        return $this->sendSms($phoneNumber, $message);
    }

    /**
     * Send maintenance update SMS
     */
    public function sendMaintenanceUpdate($tenantName, $phoneNumber, $issue, $status)
    {
        $message = "Dear {$tenantName}, your maintenance request: '{$issue}' has been {$status}. - Bari Manager";
        
        return $this->sendSms($phoneNumber, $message);
    }

    /**
     * Send welcome SMS to new tenant
     */
    public function sendWelcomeSms($tenantName, $phoneNumber, $propertyName)
    {
        $message = "Welcome {$tenantName}! You have been successfully registered at {$propertyName}. For support, contact us. - Bari Manager";
        
        return $this->sendSms($phoneNumber, $message);
    }

    /**
     * Send payment confirmation SMS
     */
    public function sendPaymentConfirmation($tenantName, $phoneNumber, $amount, $paymentDate)
    {
        $message = "Dear {$tenantName}, your payment of ৳{$amount} has been received on {$paymentDate}. Thank you! - Bari Manager";
        
        return $this->sendSms($phoneNumber, $message);
    }

    /**
     * Send checkout reminder SMS
     */
    public function sendCheckoutReminder($tenantName, $phoneNumber, $checkoutDate)
    {
        $message = "Dear {$tenantName}, please complete your checkout process by {$checkoutDate}. Return keys and complete final inspection. - Bari Manager";
        
        return $this->sendSms($phoneNumber, $message);
    }

    /**
     * Make API request to SMS gateway
     */
    private function makeApiRequest($postData)
    {
        $postString = '';
        foreach ($postData as $key => $value) {
            $postString .= $key . '=' . urlencode($value) . '&';
        }
        $postString = rtrim($postString, '&');

        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \Exception('cURL Error: ' . $error);
        }

        if ($httpCode !== 200) {
            throw new \Exception('HTTP Error: ' . $httpCode);
        }

        return $response;
    }

    /**
     * Process API response
     */
    private function processResponse($response)
    {
        // Clean response from unwanted characters
        $cleanResponse = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response);
        $array = json_decode($cleanResponse, true);

        if (!$array) {
            return [
                'success' => false,
                'message' => 'Invalid response from SMS gateway',
                'response' => $response
            ];
        }

        $result = [
            'success' => $array['status'] === 'SUCCESS',
            'status' => $array['status'] ?? 'UNKNOWN',
            'message' => $array['message'] ?? 'No message received',
            'response' => $array
        ];

        // Add bulk SMS specific data
        if (isset($array['success'])) {
            $result['success_count'] = $array['success'];
        }
        if (isset($array['failed'])) {
            $result['failed_count'] = $array['failed'];
        }

        return $result;
    }

    /**
     * Validate phone number format
     */
    public function validatePhoneNumber($phoneNumber)
    {
        // Remove any non-digit characters
        $cleanNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Check if it's a valid Bangladeshi number
        if (preg_match('/^(01[3-9])\d{8}$/', $cleanNumber)) {
            return $cleanNumber;
        }
        
        return false;
    }

    /**
     * Format phone number for SMS
     */
    public function formatPhoneNumber($phoneNumber)
    {
        $validNumber = $this->validatePhoneNumber($phoneNumber);
        if ($validNumber) {
            return $validNumber;
        }
        
        throw new \Exception('Invalid phone number format');
    }

    /**
     * Get SMS balance from SMSinBD API
     */
    public function getBalance()
    {
        try {
            $balanceUrl = 'https://api.smsinbd.com/sms-api/balance?api_token=' . $this->apiToken;
            
            $ch = curl_init($balanceUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                throw new \Exception('cURL Error: ' . $error);
            }

            if ($httpCode !== 200) {
                throw new \Exception('HTTP Error: ' . $httpCode);
            }

            // Clean response from unwanted characters
            $cleanResponse = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response);
            $array = json_decode($cleanResponse, true);

            if (!$array) {
                return [
                    'success' => false,
                    'message' => 'Invalid response from balance API',
                    'response' => $response
                ];
            }

            return [
                'success' => $array['status'] === 'success',
                'status' => $array['status'] ?? 'UNKNOWN',
                'message' => $array['message'] ?? 'Balance retrieved successfully',
                'mask' => $array['mask'] ?? 0,
                'nonmask' => $array['nonmask'] ?? 0,
                'voice' => $array['voice'] ?? 0,
                'total_balance' => ($array['mask'] ?? 0) + ($array['nonmask'] ?? 0) + ($array['voice'] ?? 0),
                'response' => $array
            ];

        } catch (\Exception $e) {
            Log::error('SMS balance check failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'SMS balance check failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Test SMS gateway connection
     */
    public function testConnection()
    {
        try {
            // Check if API token is configured
            if (empty($this->apiToken)) {
                return [
                    'success' => false,
                    'message' => 'SMS API token is not configured. Please add your API token in the settings.'
                ];
            }
            
            // Use balance API to test connection instead of sending SMS
            $balanceUrl = 'https://api.smsinbd.com/sms-api/balance?api_token=' . $this->apiToken;
            
            $ch = curl_init($balanceUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_USERAGENT, 'BariManager/1.0');
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                throw new \Exception('cURL Error: ' . $error);
            }

            // Log the response for debugging
            Log::info('SMS API Response', [
                'http_code' => $httpCode,
                'response' => $response,
                'url' => $balanceUrl
            ]);

            if ($httpCode !== 200) {
                throw new \Exception('HTTP Error: ' . $httpCode . ' - Response: ' . $response);
            }

            // Clean response from unwanted characters
            $cleanResponse = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response);
            $array = json_decode($cleanResponse, true);

            if (!$array) {
                return [
                    'success' => false,
                    'message' => 'Invalid response from SMS gateway. Response: ' . $response,
                    'response' => $response
                ];
            }

            // Check if API token is valid
            if ($array['status'] === 'success') {
                return [
                    'success' => true,
                    'message' => 'SMS gateway connection successful! API token is valid.',
                    'balance' => $array['mask'] ?? 0,
                    'response' => $array
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'SMS gateway connection failed: ' . ($array['message'] ?? 'Unknown error'),
                    'response' => $array
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('SMS connection test failed', [
                'error' => $e->getMessage(),
                'api_token' => !empty($this->apiToken) ? 'configured' : 'not configured'
            ]);
            
            return [
                'success' => false,
                'message' => 'SMS gateway connection test failed: ' . $e->getMessage()
            ];
        }
    }
} 