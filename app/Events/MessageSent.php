<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn(): array
    {
        // Membuat ruang rahasia (Private Channel) khusus untuk 2 orang yang sedang chat
        $userIds = [$this->message->sender_id, $this->message->receiver_id];
        sort($userIds); // Diurutkan biar nama ruangannya selalu sama
        
        return [
            new PrivateChannel('chat.' . $userIds[0] . '.' . $userIds[1]),
        ];
    }
}