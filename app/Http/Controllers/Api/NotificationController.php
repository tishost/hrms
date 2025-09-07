<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\NotificationHelper;
use App\Models\User;
use App\Models\Owner;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * 🚀 HRMS Push Notification Controller
 * 
 * Handles all push notification related API endpoints
 */
class NotificationController extends Controller
{
    /**
     * Send push notification to user
     */
    public function sendNotification(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id',
                'title' => 'required|string|max:255',
                'body' => 'required|string|max:1000',
                'type' => 'required|string|in:rent_reminder,payment_confirmation,payment_failed,maintenance_request,new_tenant,subscription_expiry,system_update,general',
                'data' => 'nullable|array',
                'image_url' => 'nullable|string|url',
                'action_url' => 'nullable|string|url',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::find($request->user_id);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Send push notification
            $result = NotificationHelper::sendPushNotification(
                $user,
                $request->title,
                $request->body,
                $request->type,
                $request->data ?? [],
                $request->image_url,
                $request->action_url
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification sent successfully',
                    'data' => $result['data']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error sending push notification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Send notification to multiple users
     */
    public function sendBulkNotification(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'integer|exists:users,id',
                'title' => 'required|string|max:255',
                'body' => 'required|string|max:1000',
                'type' => 'required|string|in:rent_reminder,payment_confirmation,payment_failed,maintenance_request,new_tenant,subscription_expiry,system_update,general',
                'data' => 'nullable|array',
                'image_url' => 'nullable|string|url',
                'action_url' => 'nullable|string|url',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $users = User::whereIn('id', $request->user_ids)->get();
            $results = [];

            foreach ($users as $user) {
                $result = NotificationHelper::sendPushNotification(
                    $user,
                    $request->title,
                    $request->body,
                    $request->type,
                    $request->data ?? [],
                    $request->image_url,
                    $request->action_url
                );
                $results[] = [
                    'user_id' => $user->id,
                    'success' => $result['success'],
                    'message' => $result['message']
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Bulk notification sent',
                'data' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending bulk notification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Send notification to all users of a specific role
     */
    public function sendRoleBasedNotification(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'role' => 'required|string|in:owner,tenant,admin',
                'title' => 'required|string|max:255',
                'body' => 'required|string|max:1000',
                'type' => 'required|string|in:rent_reminder,payment_confirmation,payment_failed,maintenance_request,new_tenant,subscription_expiry,system_update,general',
                'data' => 'nullable|array',
                'image_url' => 'nullable|string|url',
                'action_url' => 'nullable|string|url',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $users = User::where('role', $request->role)->get();
            $results = [];

            foreach ($users as $user) {
                $result = NotificationHelper::sendPushNotification(
                    $user,
                    $request->title,
                    $request->body,
                    $request->type,
                    $request->data ?? [],
                    $request->image_url,
                    $request->action_url
                );
                $results[] = [
                    'user_id' => $user->id,
                    'success' => $result['success'],
                    'message' => $result['message']
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Role-based notification sent',
                'data' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending role-based notification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Send rent reminder notification
     */
    public function sendRentReminder(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'tenant_id' => 'required|integer|exists:tenants,id',
                'amount' => 'required|numeric|min:0',
                'due_date' => 'required|date',
                'property_name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $tenant = Tenant::with('user')->find($request->tenant_id);
            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found'
                ], 404);
            }

            $result = NotificationHelper::sendRentReminderNotification(
                $tenant,
                $request->amount,
                $request->due_date,
                $request->property_name
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Rent reminder sent successfully',
                    'data' => $result['data']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error sending rent reminder: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Send payment confirmation notification
     */
    public function sendPaymentConfirmation(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id',
                'amount' => 'required|numeric|min:0',
                'transaction_id' => 'required|string|max:255',
                'payment_method' => 'required|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::find($request->user_id);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $result = NotificationHelper::sendPaymentConfirmationNotification(
                $user,
                $request->amount,
                $request->transaction_id,
                $request->payment_method
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment confirmation sent successfully',
                    'data' => $result['data']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error sending payment confirmation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Send maintenance request notification
     */
    public function sendMaintenanceRequest(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'owner_id' => 'required|integer|exists:owners,id',
                'tenant_id' => 'required|integer|exists:tenants,id',
                'property_name' => 'required|string|max:255',
                'issue_description' => 'required|string|max:1000',
                'priority' => 'required|string|in:low,medium,high,urgent',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $owner = Owner::with('user')->find($request->owner_id);
            $tenant = Tenant::with('user')->find($request->tenant_id);

            if (!$owner || !$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner or tenant not found'
                ], 404);
            }

            $result = NotificationHelper::sendMaintenanceRequestNotification(
                $owner,
                $tenant,
                $request->property_name,
                $request->issue_description,
                $request->priority
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Maintenance request notification sent successfully',
                    'data' => $result['data']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error sending maintenance request notification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Send subscription expiry notification
     */
    public function sendSubscriptionExpiry(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'owner_id' => 'required|integer|exists:owners,id',
                'days_left' => 'required|integer|min:0',
                'plan_name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $owner = Owner::with('user')->find($request->owner_id);
            if (!$owner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner not found'
                ], 404);
            }

            $result = NotificationHelper::sendSubscriptionExpiryNotification(
                $owner,
                $request->days_left,
                $request->plan_name
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Subscription expiry notification sent successfully',
                    'data' => $result['data']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error sending subscription expiry notification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get user's notification history
     */
    public function getNotificationHistory(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $notifications = $user->notifications()
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $notifications
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting notification history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'notification_id' => 'required|integer|exists:notifications,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();
            $notification = $user->notifications()->find($request->notification_id);

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $user->unreadNotifications()->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Delete notification
     */
    public function deleteNotification(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'notification_id' => 'required|integer|exists:notifications,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();
            $notification = $user->notifications()->find($request->notification_id);

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting notification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get notification statistics
     */
    public function getNotificationStats(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            $stats = [
                'total' => $user->notifications()->count(),
                'unread' => $user->unreadNotifications()->count(),
                'read' => $user->readNotifications()->count(),
                'by_type' => $user->notifications()
                    ->selectRaw('type, COUNT(*) as count')
                    ->groupBy('type')
                    ->pluck('count', 'type')
                    ->toArray(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting notification stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }
}
