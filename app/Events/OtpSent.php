<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OtpSent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $otp;
    public $phone;
    public $type;
    public $minutes;

    /**
     * Create a new event instance.
     */
    public function __construct($user, $otp, $phone, $type = 'general', $minutes = 10)
    {
        $this->user = $user;
        $this->otp = $otp;
        $this->phone = $phone;
        $this->type = $type;
        $this->minutes = $minutes;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}