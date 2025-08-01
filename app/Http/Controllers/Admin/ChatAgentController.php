<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatAgentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|agent');
    }

    /**
     * Show agent dashboard
     */
    public function dashboard()
    {
        $agentId = auth()->id();
        
        $waitingSessions = ChatMessage::getWaitingSessions();
        $mySessions = ChatMessage::getAgentSessions($agentId);
        $recentSessions = ChatMessage::getRecentSessions(10);
        
        return view('admin.chat.dashboard', compact('waitingSessions', 'mySessions', 'recentSessions'));
    }

    /**
     * Get chat session messages
     */
    public function getSession($sessionId): JsonResponse
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
                'session_id' => $sessionId
            ]
        ]);
    }

    /**
     * Send message as agent
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
            'message' => 'required|string',
        ]);

        $agentId = auth()->id();

        $chatMessage = ChatMessage::create([
            'session_id' => $request->session_id,
            'visitor_ip' => $request->ip(),
            'visitor_user_agent' => $request->userAgent(),
            'message_type' => 'agent',
            'message' => $request->message,
            'agent_id' => $agentId,
            'status' => 'active',
        ]);

        // Update session status to active
        ChatMessage::where('session_id', $request->session_id)
            ->update(['status' => 'active', 'agent_id' => $agentId]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => $chatMessage
        ]);
    }

    /**
     * Take over a session
     */
    public function takeSession(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
        ]);

        $agentId = auth()->id();

        // Transfer session to this agent
        ChatMessage::transferToAgent($request->session_id, $agentId);

        // Send welcome message
        ChatMessage::create([
            'session_id' => $request->session_id,
            'message_type' => 'agent',
            'message' => 'Hello! I\'m ' . auth()->user()->name . ', your dedicated support agent. How can I help you today?',
            'agent_id' => $agentId,
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Session taken over successfully'
        ]);
    }

    /**
     * Resolve a session
     */
    public function resolveSession(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
        ]);

        ChatMessage::markAsResolved($request->session_id);

        return response()->json([
            'success' => true,
            'message' => 'Session resolved successfully'
        ]);
    }

    /**
     * Get waiting sessions
     */
    public function getWaitingSessions(): JsonResponse
    {
        $sessions = ChatMessage::getWaitingSessions();

        return response()->json([
            'success' => true,
            'data' => $sessions
        ]);
    }

    /**
     * Get my active sessions
     */
    public function getMySessions(): JsonResponse
    {
        $agentId = auth()->id();
        $sessions = ChatMessage::getAgentSessions($agentId);

        return response()->json([
            'success' => true,
            'data' => $sessions
        ]);
    }

    /**
     * Get chat analytics
     */
    public function getAnalytics(): JsonResponse
    {
        $agentId = auth()->id();
        
        $mySessions = ChatMessage::where('agent_id', $agentId)->count();
        $myMessages = ChatMessage::where('agent_id', $agentId)->where('message_type', 'agent')->count();
        $resolvedSessions = ChatMessage::where('agent_id', $agentId)->where('status', 'resolved')->count();
        $waitingSessions = ChatMessage::getWaitingSessions()->count();

        return response()->json([
            'success' => true,
            'data' => [
                'my_sessions' => $mySessions,
                'my_messages' => $myMessages,
                'resolved_sessions' => $resolvedSessions,
                'waiting_sessions' => $waitingSessions,
            ]
        ]);
    }
}
