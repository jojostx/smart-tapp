<?php

namespace App\Models;

use App\Traits\MustVerifyTenantEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * create a pending tenant when the user registers,
 * send verification email to pending tenant email,
 * when user verifies the email address, redirect to
 * the account creation page,
 * 
 * create tenant, then set tenant email to verified,
 * proceed with onboarding flow.
 * 
 * if user navigates away, and returns later,
 * they should be able to input their email and be 
 * redirected to the verify page where their email will be verified,
 */
class PendingTenant extends Model implements MustVerifyEmail
{
    use MustVerifyTenantEmail;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'domain',
        'organization',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Set a callback that should be used when creating the email verification URL.
     * overrides the method from the MustVerifyTenantEmail trait
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function createUrlUsing()
    {
        // build url here
        return function (Model $notifiable) {
            return route('verification.pending.notice', ['id' => $notifiable->getKey()]);
        };
    }
}
