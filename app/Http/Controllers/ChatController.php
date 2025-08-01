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
            'message_type' => 'required|in:user,bot,agent',
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
            'agent_id' => $request->agent_id,
            'status' => $request->status ?? 'active',
            'metadata' => $request->metadata,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Chat message stored successfully',
            'data' => $chatMessage
        ]);
    }

    /**
     * Request agent transfer
     */
    public function requestAgent(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
            'reason' => 'nullable|string',
        ]);

        // Update session status to waiting
        ChatMessage::where('session_id', $request->session_id)
            ->update(['status' => 'waiting']);

        // Create transfer request message
        ChatMessage::create([
            'session_id' => $request->session_id,
            'visitor_ip' => $request->ip(),
            'visitor_user_agent' => $request->userAgent(),
            'message_type' => 'bot',
            'message' => 'I\'m transferring you to a human agent. Please wait a moment...',
            'intent' => 'agent_transfer',
            'status' => 'waiting',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Agent transfer requested',
            'data' => [
                'session_id' => $request->session_id,
                'status' => 'waiting'
            ]
        ]);
    }

    /**
     * Get chat analytics for admin
     */
    public function analytics(): JsonResponse
    {
        $popularIntents = ChatMessage::getPopularIntents();
        $recentSessions = ChatMessage::getRecentSessions(10);
        $waitingSessions = ChatMessage::getWaitingSessions();
        $totalMessages = ChatMessage::count();
        $todayMessages = ChatMessage::whereDate('created_at', today())->count();
        $agentMessages = ChatMessage::where('message_type', 'agent')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'popular_intents' => $popularIntents,
                'recent_sessions' => $recentSessions,
                'waiting_sessions' => $waitingSessions,
                'total_messages' => $totalMessages,
                'today_messages' => $todayMessages,
                'agent_messages' => $agentMessages,
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
            ->with('agent')
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

    /**
     * Check if agent is available
     */
    public function checkAgentAvailability(): JsonResponse
    {
        $waitingSessions = ChatMessage::getWaitingSessions()->count();
        $availableAgents = \App\Models\User::whereHas('roles', function($query) {
            $query->where('name', 'agent');
        })->where('is_online', true)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'waiting_sessions' => $waitingSessions,
                'available_agents' => $availableAgents,
                'agent_available' => $availableAgents > 0,
            ]
        ]);
    }
}
