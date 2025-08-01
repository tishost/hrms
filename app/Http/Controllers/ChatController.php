<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    /**
     * Store a chat message
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
            'message' => 'required|string',
            'message_type' => 'required|in:user,bot',
            'intent' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        $chatMessage = ChatMessage::create([
            'session_id' => $request->session_id,
            'visitor_ip' => $request->ip(),
            'visitor_user_agent' => $request->userAgent(),
            'message_type' => $request->message_type,
            'message' => $request->message,
            'intent' => $request->intent,
            'metadata' => $request->metadata,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Chat message stored successfully',
            'data' => $chatMessage
        ]);
    }

    /**
     * Get chat analytics for admin
     */
    public function analytics(): JsonResponse
    {
        $popularIntents = ChatMessage::getPopularIntents();
        $recentSessions = ChatMessage::getRecentSessions(10);
        $totalMessages = ChatMessage::count();
        $todayMessages = ChatMessage::whereDate('created_at', today())->count();

        return response()->json([
            'success' => true,
            'data' => [
                'popular_intents' => $popularIntents,
                'recent_sessions' => $recentSessions,
                'total_messages' => $totalMessages,
                'today_messages' => $todayMessages,
            ]
        ]);
    }

    /**
     * Get chat session details
     */
    public function session($sessionId): JsonResponse
    {
        $messages = ChatMessage::where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();

        $stats = ChatMessage::getSessionStats($sessionId);

        return response()->json([
            'success' => true,
            'data' => [
                'messages' => $messages,
                'stats' => $stats,
            ]
        ]);
    }
}
