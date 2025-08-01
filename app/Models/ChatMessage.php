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
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get chat session statistics
     */
    public static function getSessionStats($sessionId)
    {
        return self::where('session_id', $sessionId)
            ->selectRaw('COUNT(*) as total_messages, COUNT(CASE WHEN message_type = "user" THEN 1 END) as user_messages, COUNT(CASE WHEN message_type = "bot" THEN 1 END) as bot_messages')
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
        return self::select('session_id', 'visitor_ip', 'created_at')
            ->groupBy('session_id', 'visitor_ip', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
