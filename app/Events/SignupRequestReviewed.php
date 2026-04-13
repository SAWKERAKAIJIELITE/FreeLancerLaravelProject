<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\AccountRequest;


class SignupRequestReviewed implements ShouldBroadcastNow, ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public AccountRequest $accountRequest
    ) {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('super-admin.signup-requests'),
            new PrivateChannel('networker.' . $this->accountRequest->referral_id . '.signup-requests'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'signup-request.reviewed';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->accountRequest->id,
            // 'full_name' => $this->accountRequest->username,
            'username' => $this->accountRequest->username,
            // 'email' => $this->accountRequest->email,
            'role' => $this->accountRequest->role,
            'referred_by' => $this->accountRequest->referral()->first()?->username,
            'rejection_reason'=> $this->accountRequest->rejection_reason,
            'status' => $this->accountRequest->status->value,
            'reviewed_by' => $this->accountRequest->reviewer()->first()?->username,
            'reviewed_at' => optional(
                $this->accountRequest->approved_at ?? $this->accountRequest->rejected_at
            )?->toDateTimeString(),
        ];
    }
}
