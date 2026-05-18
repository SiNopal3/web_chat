<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        // Menyertakan data user agar nama pengirim bisa muncul di layar teman
        $this->message = $message->load('user');
    }

    public function broadcastOn(): array
    {
        // Menyiarkan pesan ini khusus ke saluran grup yang tepat
        return [
            new PresenceChannel('group.' . $this->message->group_id),
        ];
    }
}