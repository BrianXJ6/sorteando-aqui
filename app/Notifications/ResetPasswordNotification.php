<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Support\ORM\BaseAuthenticable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param string $token
     */
    public function __construct(private string $token)
    {
        //
    }

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var array
     */
    public $backoff = [20, 60];

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 3;

    /**
     * Get the notification's delivery channels.
     *
     * @param \App\Support\ORM\BaseAuthenticable $notifiable
     *
     * @return array
     */
    public function via(BaseAuthenticable $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param \App\Support\ORM\BaseAuthenticable $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(BaseAuthenticable $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(trans('Reset Password Notification'))
            ->line(trans('You are receiving this email because we received a password reset request for your account.'))
            ->action(trans('Reset Password'), $this->getRoute($notifiable))
            ->line(trans('This password reset link will expire in :count minutes.', [
                'count' => config("auth.passwords.{$notifiable->getTable()}.expire")
            ]))
            ->line(trans('If you did not request a password reset, no further action is required.'));
    }

    /**
     * Get the reset URL for the given notifiable.
     *
     * @param \App\Support\ORM\BaseAuthenticable $notifiable
     *
     * @return string
     */
    protected function getRoute(BaseAuthenticable $notifiable): string
    {
        return url(route('home', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]));
    }
}
