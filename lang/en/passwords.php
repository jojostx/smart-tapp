<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'reset' => 'Your password has been reset!',
    'sent' => 'We have emailed your password reset link!',
    'throttled' => 'Please wait before retrying.',
    'token' => 'This password reset token is invalid.',
    'user' => "We can't find a user with that email address.",

    'notifications' => [
        'salutation' => 'Regards',
        'password_reset' => [
            'title' => 'Your password for the :tenant admin',
            'message' => 'You are receiving this email because we received a password reset request for your account.',
            'button' => 'Reset Password',
            'expiry' => 'This password reset link will expire in :count minutes. If you did not request a password reset, no further action is required.',
        ],
        'password_set' => [
            'title' => 'Your account for the :tenant admin',
            'message' => 'You are receiving this email because an admin account was recently created for you for the :tenant admin. Please click on the following link to set your password:',
            'button' => 'Set Password',
            'expiry' => 'This password set link will expire in :count minutes. Should the link have expired, you can attempt to [reset your password manually](:url).',
        ],
        'otp' => [
            'title' => 'Your verification code for the :tenant admin',
            'message' => 'To confirm your login, please use the following verification code. The code is valid for 5 minutes.',
        ],
    ],
];
