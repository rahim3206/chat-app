<?php

namespace App\Events;

use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FriendRequestSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $friend_request;
    public $sender;
    public $receiver;

    /**
     * Create a new event instance.
     */
    public function __construct(FriendRequest $friend_request, User $sender, User $receiver)
    {
        $this->friend_request = $friend_request;
        $this->sender = $sender;
        $this->receiver = $receiver;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('send-friend-request.'.$this->receiver->id),
        ];
    }
    public function broadcastAs()
    {
        return 'FriendRequestSent';
    }
}
