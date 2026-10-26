<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Incorrect Password Warnings
    |--------------------------------------------------------------------------
    |
    | When the wrong password is typed on the sign-in screen or on the
    | password change screens, SportsHub warns about it by email as well as
    | on the screen. The settings below decide who is warned and how often a
    | warning may be sent, so a burst of attempts can never be turned into a
    | burst of emails.
    |
    */

    'failed_password_warnings' => [

        'enabled' => (bool) env('SECURITY_FAILED_PASSWORD_WARNINGS', true),

        /*
         * Warn the holder of the account that was targeted. Only an account
         * that really exists is warned, so the sign-in form can never be used
         * to send mail to an arbitrary address.
         */
        'notify_account_owner' => true,

        /*
         * Warn every sports coordinator about the attempt. Attempts against
         * an identifier that has no account are reported here as well, so a
         * pattern of guesses is visible to the staff.
         */
        'notify_administrators' => true,

        /*
         * Minutes a warning is suppressed after one was already sent for the
         * same account (owner warning) or the same network source
         * (coordinator warning).
         */
        'owner_throttle_minutes' => (int) env('SECURITY_FAILED_PASSWORD_OWNER_THROTTLE_MINUTES', 5),
        'admin_throttle_minutes' => (int) env('SECURITY_FAILED_PASSWORD_ADMIN_THROTTLE_MINUTES', 5),

        /*
         * Minutes used to count how many wrong passwords were entered
         * recently. The count is shown in the warning email.
         */
        'attempt_window_minutes' => (int) env('SECURITY_FAILED_PASSWORD_ATTEMPT_WINDOW_MINUTES', 15),

    ],

];
