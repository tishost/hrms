<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestNotificationController extends Controller
{
    /**
     * Send test notification to all users
     */
    public function sendTestNotification(Request $request)
    {
        try {
            $title = $request->input('title', 'Test Notification');
            $body = $request->input('body', 'This is a test notification from HRMS Admin Panel');
            $type = $request->input('type', 'general');
            
            // Send to all users
            $result = NotificationHelper::sendTopicNotification(
                'all_users',
                $title,
                $body,
                $type,
                [
                    'priority' => 'normal',
                    'action_url' => null,
                    'image_url' => null,
                ]
            );
            
            if ($result && $result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test notification sent successfully!',
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to send test notification'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Error sending test notification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending test notification: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Send test notification to specific user
     */
    public function sendTestToUser(Request $request)
    {
        try {
            $userId = $request->input('user_id');
            $title = $request->input('title', 'Test Notification');
            $body = $request->input('body', 'This is a test notification from HRMS Admin Panel');
            $type = $request->input('type', 'general');
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required'
                ], 400);
            }
            
            // Send to specific user
            $result = NotificationHelper::sendBulkPushNotification(
                [$userId],
                $title,
                $body,
                $type,
                [
                    'priority' => 'normal',
                    'action_url' => null,
                    'image_url' => null,
                ]
            );
            
            if ($result && $result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test notification sent successfully!',
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to send test notification'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Error sending test notification to user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending test notification: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get notification statistics
     */
    public function getStats()
    {
        try {
            // Get user counts
            $totalUsers = \App\Models\User::count();
            $totalOwners = \App\Models\User::where('user_type', 'owner')->count();
            $totalTenants = \App\Models\User::where('user_type', 'tenant')->count();
            
            // Get users with FCM tokens (assuming you have a fcm_tokens table or fcm_token column)
            $usersWithTokens = \App\Models\User::whereNotNull('fcm_token')->count();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_users' => $totalUsers,
                    'total_owners' => $totalOwners,
                    'total_tenants' => $totalTenants,
                    'users_with_tokens' => $usersWithTokens,
                    'notification_coverage' => $totalUsers > 0 ? round(($usersWithTokens / $totalUsers) * 100, 2) : 0
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting notification stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get notification statistics'
            ], 500);
        }
    }
}
