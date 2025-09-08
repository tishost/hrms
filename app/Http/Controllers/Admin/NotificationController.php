<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\NotificationHelper;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Show the notification sending form
     */
    public function index()
    {
        try {
            // Get all owners and tenants based on owner_id and tenant_id
            $owners = User::whereNotNull('owner_id')
                         ->select('id', 'name', 'phone as mobile', 'email')
                         ->orderBy('name')
                         ->get();
            
            $tenants = User::whereNotNull('tenant_id')
                          ->select('id', 'name', 'phone as mobile', 'email')
                          ->orderBy('name')
                          ->get();
            
            // Get all roles - if Role model doesn't exist, create empty collection
            $roles = collect();
            if (class_exists('App\Models\Role')) {
                $roles = Role::select('id', 'name')
                            ->orderBy('name')
                            ->get();
            }
            
            return view('admin.notifications.send', compact('owners', 'tenants', 'roles'));
            
        } catch (\Exception $e) {
            Log::error('Error loading notification form: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Return with error message
            return redirect()->route('admin.dashboard')
                           ->with('error', 'Failed to load notification form: ' . $e->getMessage());
        }
    }
    
    /**
     * Send push notification
     */
    public function send(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'target_type' => 'required|in:all_users,all_owners,all_tenants,specific_users,role_based',
                'notification_type' => 'required|string|max:50',
                'title' => 'required|string|max:100',
                'body' => 'required|string|max:500',
                'priority' => 'required|in:normal,high,urgent',
                'action_url' => 'nullable|url',
                'image_url' => 'nullable|url',
                'scheduled_at' => 'nullable|date|after:now',
                'user_ids' => 'required_if:target_type,specific_users|array',
                'user_ids.*' => 'exists:users,id',
                'role_id' => 'required_if:target_type,role_based|nullable|integer',
            ]);
            
            if ($validator->fails()) {
                $errors = $validator->errors();
                $errorMessages = [];
                
                foreach ($errors->all() as $error) {
                    $errorMessages[] = $error;
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', $errorMessages),
                    'errors' => $errors
                ], 422);
            }
            
            $data = $request->all();
            
            // Log the notification request
            Log::info('Notification send requested', [
                'target_type' => $data['target_type'],
                'title' => $data['title'],
                'body' => $data['body'],
                'type' => $data['notification_type'],
                'priority' => $data['priority'],
                'timestamp' => now()
            ]);
            
            // For now, just return success (Firebase integration pending)
            $result = [
                'success' => true,
                'message' => 'Notification logged successfully! (Firebase integration pending)',
                'data' => [
                    'target_type' => $data['target_type'],
                    'title' => $data['title'],
                    'body' => $data['body'],
                    'type' => $data['notification_type'],
                    'priority' => $data['priority'],
                    'timestamp' => now()->toISOString()
                ]
            ];
            
            if ($result && $result['success']) {
                // Log the notification
                $this->logNotification($data, $result);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Notification sent successfully',
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to send notification'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Error sending notification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending notification'
            ], 500);
        }
    }
    
    /**
     * Send to all users (owners + tenants)
     */
    private function sendToAllUsers($data)
    {
        try {
            // Get all user IDs (both owners and tenants)
            $allUserIds = User::where(function($query) {
                $query->whereNotNull('owner_id')
                      ->orWhereNotNull('tenant_id');
            })->pluck('id')->toArray();
            
            if (empty($allUserIds)) {
                return ['success' => false, 'message' => 'No users found'];
            }
            
            return NotificationHelper::sendBulkPushNotification(
                $allUserIds,
                $data['title'],
                $data['body'],
                $data['notification_type'],
                [
                    'priority' => $data['priority'],
                    'action_url' => $data['action_url'] ?? null,
                    'image_url' => $data['image_url'] ?? null,
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error sending to all users: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Send to all owners
     */
    private function sendToAllOwners($data)
    {
        try {
            // Get all owner user IDs
            $ownerIds = User::whereNotNull('owner_id')->pluck('id')->toArray();
            
            if (empty($ownerIds)) {
                return ['success' => false, 'message' => 'No owners found'];
            }
            
            return NotificationHelper::sendBulkPushNotification(
                $ownerIds,
                $data['title'],
                $data['body'],
                $data['notification_type'],
                [
                    'priority' => $data['priority'],
                    'action_url' => $data['action_url'] ?? null,
                    'image_url' => $data['image_url'] ?? null,
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error sending to all owners: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Send to all tenants
     */
    private function sendToAllTenants($data)
    {
        try {
            // Get all tenant user IDs
            $tenantIds = User::whereNotNull('tenant_id')->pluck('id')->toArray();
            
            if (empty($tenantIds)) {
                return ['success' => false, 'message' => 'No tenants found'];
            }
            
            return NotificationHelper::sendBulkPushNotification(
                $tenantIds,
                $data['title'],
                $data['body'],
                $data['notification_type'],
                [
                    'priority' => $data['priority'],
                    'action_url' => $data['action_url'] ?? null,
                    'image_url' => $data['image_url'] ?? null,
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error sending to all tenants: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Send to specific users
     */
    private function sendToSpecificUsers($data)
    {
        try {
            $userIds = $data['user_ids'] ?? [];
            if (empty($userIds)) {
                return ['success' => false, 'message' => 'No users selected'];
            }
            
            return NotificationHelper::sendBulkPushNotification(
                $userIds,
                $data['title'],
                $data['body'],
                $data['notification_type'],
                [
                    'priority' => $data['priority'],
                    'action_url' => $data['action_url'] ?? null,
                    'image_url' => $data['image_url'] ?? null,
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error sending to specific users: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Send to role-based users
     */
    private function sendToRoleBased($data)
    {
        try {
            $roleId = $data['role_id'] ?? null;
            if (!$roleId) {
                return ['success' => false, 'message' => 'No role selected'];
            }
            
            // Get users with specific role
            $users = User::whereHas('roles', function($query) use ($roleId) {
                $query->where('role_id', $roleId);
            })->pluck('id')->toArray();
            
            if (empty($users)) {
                return ['success' => false, 'message' => 'No users found with selected role'];
            }
            
            return NotificationHelper::sendBulkPushNotification(
                $users,
                $data['title'],
                $data['body'],
                $data['notification_type'],
                [
                    'priority' => $data['priority'],
                    'action_url' => $data['action_url'] ?? null,
                    'image_url' => $data['image_url'] ?? null,
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error sending to role-based users: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Log notification for history
     */
    private function logNotification($data, $result)
    {
        try {
            // You can create a notifications_log table to store notification history
            // For now, we'll just log it
            Log::info('Notification sent', [
                'target_type' => $data['target_type'],
                'notification_type' => $data['notification_type'],
                'title' => $data['title'],
                'body' => $data['body'],
                'priority' => $data['priority'],
                'result' => $result,
                'sent_at' => now(),
                'sent_by' => auth()->id()
            ]);
        } catch (\Exception $e) {
            Log::error('Error logging notification: ' . $e->getMessage());
        }
    }
    
    /**
     * Get notification history
     */
    public function history(Request $request)
    {
        try {
            // This would typically fetch from a notifications_log table
            // For now, return a placeholder
            return view('admin.notifications.history');
        } catch (\Exception $e) {
            Log::error('Error loading notification history: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load notification history');
        }
    }
    
    /**
     * Get notification statistics
     */
    public function stats()
    {
        try {
            // Get user counts for statistics
            $totalUsers = User::count();
            $totalOwners = User::whereNotNull('owner_id')->count();
            $totalTenants = User::whereNotNull('tenant_id')->count();
            
            // Get users with FCM tokens (assuming you have a fcm_tokens table)
            $usersWithTokens = User::whereNotNull('fcm_token')->count();
            
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
    
    /**
     * Send test notification to all users
     */
    public function sendTestNotification(Request $request)
    {
        try {
            $title = $request->input('title', 'Test Notification');
            $body = $request->input('body', 'This is a test notification from HRMS Admin Panel');
            $type = $request->input('type', 'general');
            
            // Get all users with FCM tokens
            $usersWithTokens = User::whereNotNull('fcm_token')->get();
            
            if ($usersWithTokens->count() == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No users with FCM tokens found. Please ensure users have logged in and FCM tokens are saved.'
                ], 400);
            }
            
            $successCount = 0;
            $failureCount = 0;
            $results = [];
            
            // Send notification to each user
            foreach ($usersWithTokens as $user) {
                try {
                    $result = NotificationHelper::sendPushNotification(
                        $user,
                        $title,
                        $body,
                        $type,
                        [
                            'type' => $type,
                            'user_id' => $user->id,
                            'timestamp' => time(),
                            'test_notification' => true
                        ]
                    );
                    
                    if ($result['success']) {
                        $successCount++;
                        $results[] = [
                            'user_id' => $user->id,
                            'name' => $user->name,
                            'status' => 'success'
                        ];
                    } else {
                        $failureCount++;
                        $results[] = [
                            'user_id' => $user->id,
                            'name' => $user->name,
                            'status' => 'failed',
                            'error' => $result['message']
                        ];
                    }
                } catch (\Exception $e) {
                    $failureCount++;
                    $results[] = [
                        'user_id' => $user->id,
                        'name' => $user->name,
                        'status' => 'failed',
                        'error' => $e->getMessage()
                    ];
                }
            }
            
            Log::info('Test notification sent', [
                'title' => $title,
                'body' => $body,
                'type' => $type,
                'total_users' => $usersWithTokens->count(),
                'success_count' => $successCount,
                'failure_count' => $failureCount,
                'timestamp' => now()
            ]);
            
            return response()->json([
                'success' => $successCount > 0,
                'message' => "Test notification sent to {$successCount} users successfully. {$failureCount} failed.",
                'data' => [
                    'title' => $title,
                    'body' => $body,
                    'type' => $type,
                    'total_users' => $usersWithTokens->count(),
                    'success_count' => $successCount,
                    'failure_count' => $failureCount,
                    'results' => $results,
                    'timestamp' => now()->toISOString()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error sending test notification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending test notification: ' . $e->getMessage()
            ], 500);
        }
    }
}
