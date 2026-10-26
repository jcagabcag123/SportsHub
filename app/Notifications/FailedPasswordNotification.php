<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Warning sent to the holder of an account after somebody typed the wrong
 * password for it.
 *
 * The wording mirrors the message the sign-in screen shows ("Invalid email or
 * password"), so the account holder reads the same warning in both places:
 * on the screen and in the inbox.
 */
class FailedPasswordNotification extends Notification
{
    /**
     * @param  string  $context  Where the wrong password was typed:
     *                           "login" or "change-password".
     * @param  array<string, mixed>  $details  The attempt details: when it
     *                                         happened, where it came from
     *                                         and how many wrong passwords
     *                                         were entered recently.
     */
    public function __construct(
        public string $context,
        public array $details = [],
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Security alert: an incorrect password was entered for your SportsHub account')
            ->greeting('Hello '.($notifiable->name ?: 'there').',')
            ->line($this->headline())
            ->line('Your password was not changed and nobody was let into your account.')
            ->line('Details of the attempt:')
            ->line('• When: '.($this->details['when'] ?? 'just now'))
            ->line('• Where from: '.($this->details['ip'] ?? 'Unknown'))
            ->line('• Device: '.($this->details['device'] ?? 'Unknown'))
            ->line('• Wrong passwords in the last '.($this->details['window'] ?? 15).' minutes: '.($this->details['attempts'] ?? 1));

        if ($this->context === 'change-password') {
            $message->line('The wrong password was typed on the password change page, so your password was left untouched.');
        }

        return $message
            ->action('Reset Your Password', route('password.request'))
            ->line('If this was you, simply sign in again and type your password carefully. If you do not recognise this attempt, reset your password now and contact the Sports Coordinator.')
            ->salutation('SNNHS Sports Activity Hub');
    }

    /**
     * The opening line, which differs between the sign-in screen and the
     * password change screens.
     */
    private function headline(): string
    {
        return $this->context === 'change-password'
            ? 'Somebody typed the wrong password for your SNNHS SportsHub account.'
            : 'Somebody tried to sign in to your SNNHS SportsHub account with the wrong password.';
    }
}
