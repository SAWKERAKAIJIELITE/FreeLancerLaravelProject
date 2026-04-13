<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\AnonymousNotifiable;
use App\Models\AccountRequest;

class SignupRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public AccountRequest $accountRequest
    ) {
        $this->onQueue('mail');
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $resubmitUrl = URL::temporarySignedRoute(
            'signup-requests.resubmit',
            now()->addDays(3),
            ['id' => $this->accountRequest->id]
        );
        return (new MailMessage)
            ->subject('Your signup request needs to be resubmitted')
            ->greeting('Hello ' . $this->accountRequest->full_name . ',')
            ->line('Your signup request was reviewed and rejected.')
            ->lineIf(
                filled($this->accountRequest->rejection_reason),
                'Reason: ' . $this->accountRequest->rejection_reason
            )
            ->line(
                'You can review your previous information, make corrections,
                    and submit a new request using the button below.'
            )
            ->action('Resubmit Signup Request', $resubmitUrl)
            ->line('This link will expire in 3 days.')
            ->line('If you did not submit this request, you can safely ignore this email.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
