<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Warning sent to the sports coordinators after somebody typed the wrong
 * password.
 *
 * The coordinators are told about every attempt, including the ones against
 * an identifier that has no account, because a run of guesses is exactly what
 * they need to see. The account holder is warned separately by
 * FailedPasswordNotification.
 */
class FailedPasswordAttemptNotification extends Notification
{
    /**
     * @param  string  $context  Where the wrong password was typed:
     *                           "login" or "change-password".
     * @param  array<string, mixed>  $details  The attempt details.
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
        return (new MailMessage)
            ->subject('SportsHub: an incorrect password was entered - '.$this->accountLabel())
            ->greeting('Hello '.($notifiable->name ?: 'Sports Coordinator').',')
            ->line($this->headline())
            ->line('Details of the attempt:')
            ->line('• Account: '.$this->accountLabel())
            ->line('• Identifier used: '.($this->details['identifier'] ?? 'Unknown'))
            ->line('• Portal: '.$this->roleLabel())
            ->line('• When: '.($this->details['when'] ?? 'just now'))
            ->line('• IP address: '.($this->details['ip'] ?? 'Unknown'))
            ->line('• Device: '.($this->details['device'] ?? 'Unknown'))
            ->line('• Browser: '.($this->details['user_agent'] ?? 'Unknown'))
            ->line('• Wrong passwords in the last '.($this->details['window'] ?? 15).' minutes: '.($this->details['attempts'] ?? 1))
            ->line('If you do not recognise this attempt, reset the account password and review the athlete record.')
            ->salutation('SNNHS Sports Activity Hub');
    }

    /**
     * The account the attempt was aimed at, or a clear statement that no
     * account matched the identifier that was typed.
     */
    private function accountLabel(): string
    {
        $account = $this->details['account'] ?? null;

        if (! $account) {
            return 'No matching account';
        }

        return $account.($this->details['account_email'] ? ' ('.$this->details['account_email'].')' : '');
    }

    /**
     * The portal the attempt was made on.
     */
    private function roleLabel(): string
    {
        return ($this->details['role'] ?? '') === 'Administrator'
            ? 'Administrator / Sports Coordinator'
            : 'Athlete / Student';
    }

    /**
     * The opening line, which differs between the sign-in screen and the
     * password change screens.
     */
    private function headline(): string
    {
        return $this->context === 'change-password'
            ? 'The wrong password was entered on the password change page of the SNNHS SportsHub portal.'
            : 'A sign-in attempt on the SNNHS SportsHub portal was rejected because the password was not correct.';
    }
}
