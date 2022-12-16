<?php

namespace App\Models\Tenant;

use Illuminate\Auth\Events\Verified;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Traits\Tappable;

/**
 * @mixin IdeHelperPendingUserEmail
 */
class PendingUserEmail extends Model
{
    use Tappable;

    const TYPE_PENDING = 'pending';

    const TYPE_RECOVER = 'recover';

    /**
     * This model won't be updated.
     */
    const UPDATED_AT = null;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * User relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function user(): MorphTo
    {
        return $this->morphTo('user');
    }

    /**
     * Scope for the user.
     *
     * @param $query
     * @param  \Illuminate\Database\Eloquent\Model  $user
     * @return void
     */
    public function scopeForUser($query, Model $user)
    {
        $query->where([
            $this->qualifyColumn('user_type') => get_class($user),
            $this->qualifyColumn('user_id') => $user->getKey(),
        ]);
    }

    /**
     * Updates the associated user and removes all pending models with this email.
     *
     * @return void
     */
    public function activate()
    {
        /** @var Model */
        $user = $this->user;

        $dispatchEvent = ! $user->hasVerifiedEmail() || $user->email !== $this->email;

        $originalEmail = $user->email;

        $user->email = $this->email;
        $user->save();
        $user->markEmailAsVerified();

        static::whereEmail($this->email)->get()->each->delete();

        $dispatchEvent ? event(new Verified($user)) : null;

        if ($this->type === PendingUserEmail::TYPE_RECOVER) {
            static::forUser($this->user)->get()->each->delete();

            return;
        }

        $user->newRecoveryEmail($originalEmail, $user->refresh()->updated_at);
    }

    /**
     * Creates a temporary signed URL to verify the pending email.
     *
     * @return string
     */
    public function verificationUrl(): string
    {
        return URL::temporaryTenantSignedRoute(
            'filament.auth.pending-email.verify',
            now()->addMinutes(config('auth.verification.expire', 120)),
            ['token' => $this->token],
        );
    }
}
