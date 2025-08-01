<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'visitor_ip',
        'visitor_user_agent',
        'message_type',
        'message',
        'intent',
        'agent_id',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the agent who responded
     */
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Get chat session statistics
     */
    public static function getSessionStats($sessionId)
    {
        return self::where('session_id', $sessionId)
            ->selectRaw('COUNT(*) as total_messages, COUNT(CASE WHEN message_type = "user" THEN 1 END) as user_messages, COUNT(CASE WHEN message_type = "bot" THEN 1 END) as bot_messages, COUNT(CASE WHEN message_type = "agent" THEN 1 END) as agent_messages')
            ->first();
    }

    /**
     * Get popular intents
     */
    public static function getPopularIntents()
    {
        return self::whereNotNull('intent')
            ->selectRaw('intent, COUNT(*) as count')
            ->groupBy('intent')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get recent chat sessions
     */
    public static function getRecentSessions($limit = 20)
    {
        return self::select('session_id', 'visitor_ip', 'status', 'created_at')
            ->groupBy('session_id', 'visitor_ip', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get active sessions waiting for agent
     */
    public static function getWaitingSessions()
    {
        return self::where('status', 'waiting')
            ->select('session_id', 'visitor_ip', 'created_at')
            ->groupBy('session_id', 'visitor_ip', 'created_at')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get sessions assigned to specific agent
     */
    public static function getAgentSessions($agentId)
    {
        return self::where('agent_id', $agentId)
            ->where('status', 'active')
            ->select('session_id', 'visitor_ip', 'created_at')
            ->groupBy('session_id', 'visitor_ip', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Transfer session to agent
     */
    public static function transferToAgent($sessionId, $agentId)
    {
        return self::where('session_id', $sessionId)
            ->update([
                'status' => 'active',
                'agent_id' => $agentId
            ]);
    }

    /**
     * Mark session as resolved
     */
    public static function markAsResolved($sessionId)
    {
        return self::where('session_id', $sessionId)
            ->update(['status' => 'resolved']);
    }
}
