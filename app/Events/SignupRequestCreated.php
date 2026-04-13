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


class SignupRequestCreated implements ShouldBroadcastNow, ShouldDispatchAfterCommit
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
        // logger()->info('Broadcasting channels', [
        //     'signup_request_id' => $this->accountRequest->id,
        //     'referred_by' => $this->accountRequest->referral_id,
        //     'status' => $this->accountRequest->status,
        // ]);
        return [
            new PrivateChannel('super-admin.signup-requests'),
            new PrivateChannel('networker.' . $this->accountRequest->referral_id . '.signup-requests'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'signup-request.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->accountRequest->id,
            'username' => $this->accountRequest->username,
            // 'email' => $this->accountRequest->email,
            'role'=> $this->accountRequest->role,
            'referred_by' => $this->accountRequest->referral()->first()?->username,
            'referred_by_role' => $this->accountRequest->referral()->first()?->role,
            'country' => $this->accountRequest->country->name,
            'reviewed_by'=> $this->accountRequest->reviewer()->first()?->username,
            'referral_code'=>$this->accountRequest->user()->first()?->referral_code,
            'status' => $this->accountRequest->status->value,
            'reviewed_at' => optional(
                $this->accountRequest->approved_at ?? $this->accountRequest->rejected_at
            )?->toDateTimeString(),
            'created_at' => optional($this->accountRequest->created_at)?->toDateTimeString(),
        ];
    }
}
