<?php

namespace App\Traits;

use App\Mail\RecoverOldEmail;
use App\Mail\VerifyNewEmail;
use App\Models\Tenant\PendingUserEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

trait MustVerifyNewEmail
{
    /**
     * returns the model to use as PendingUserModel model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getEmailVerificationModel(): Model
    {
        return new PendingUserEmail();
    }

    /**
     * Deletes all previous attempts for this user, creates a new model/token
     * to verify the given email address and send the verification URL
     * to the new email address.
     *
     * @param  string  $email
     * @param  callable  $withMailable
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function newEmail(string $email, callable $withMailable = null): ?Model
    {
        $currentEmail = $this->getEmailForVerification();

        if ($currentEmail === $email && $this->hasVerifiedEmail()) {
            return null;
        }

        return $this->createPendingUserEmailModel($email)->refresh()->tap(function ($model) use ($withMailable) {
            $this->sendPendingEmailVerificationMail($model, $withMailable);
        });
    }

    /**
     * Send a notification to original email address regarding change of email address.
     *
     * @param  string  $originalEmail
     * @return void
     */
    public function newRecoveryEmail(string $originalEmail, Carbon $changed_at): ?Model
    {
        return $this->createRecoveryPendingUserEmailModel($originalEmail)->tap(function ($model) use ($changed_at) {
            $this->sendRecoveryMail($model, $changed_at);
        });
    }

    /**
     * Creates new PendingUserModel model for the given email.
     *
     * @param  string  $email
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createPendingUserEmailModel(string $email): Model
    {
        $this->clearPendingEmail();

        $token = hash_hmac('sha256', str()->random(40), $this->getKey());

        return $this->getEmailVerificationModel()->create([
            'user_type' => get_class($this),
            'user_id' => $this->getKey(),
            'email' => $email,
            'token' => $token,
            'type' => PendingUserEmail::TYPE_PENDING,
        ]);
    }

    /**
     * Returns the pending email address.
     *
     * @return string|null
     */
    public function getPendingEmail(): ?string
    {
        return $this->getEmailVerificationModel()->forUser($this)->value('email');
    }

    /**
     * Deletes the pending email address models for this user, with type pending.
     *
     * @return void
     */
    public function clearPendingEmail()
    {
        $this->getEmailVerificationModel()->forUser($this)->where('type', PendingUserEmail::TYPE_PENDING)->get()->each->delete();
    }

    /**
     * Deletes the pending email address models for this user, with type recover.
     *
     * @return void
     */
    public function clearRecoveryEmail()
    {
        $this->getEmailVerificationModel()->forUser($this)->where('type', PendingUserEmail::TYPE_RECOVER)->get()->each->delete();
    }

    /**
     * Sends the VerifyNewEmail Mailable to the new email address.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $pendingUserEmail
     * @param  callable  $withMailable
     * @return mixed
     */
    public function sendPendingEmailVerificationMail(Model $pendingUserEmail, callable $withMailable = null)
    {
        $mailable = new VerifyNewEmail($pendingUserEmail);

        if ($withMailable) {
            $withMailable($mailable, $pendingUserEmail);
        }

        return Mail::to($pendingUserEmail->email)->send($mailable);
    }

    /**
     * Grabs the pending user email address, generates a new token and sends the Mailable.
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resendPendingEmailVerificationMail(): ?Model
    {
        $pendingUserEmail = $this->getEmailVerificationModel()->forUser($this)->firstOrFail();

        return $this->newEmail($pendingUserEmail->email);
    }

    /**
     * Creates new PendingUserModel model for the given email, with type recover.
     *
     * @param  string  $currentEmail
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createRecoveryPendingUserEmailModel(string $currentEmail): Model
    {
        $this->clearRecoveryEmail();

        return $this->getEmailVerificationModel()->create([
            'user_type' => get_class($this),
            'user_id' => $this->getKey(),
            'email' => $currentEmail,
            'token' => hash_hmac('sha256', str()->random(40), $this->getKey()),
            'type' => PendingUserEmail::TYPE_RECOVER,
        ]);
    }

    /**
     * Sends the recoverEmail Mailable to the old email address.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $pendingUserEmail
     * @return mixed
     */
    public function sendRecoveryMail(Model $pendingUserEmail, Carbon $changed_at)
    {
        $mailable = new RecoverOldEmail($pendingUserEmail, $changed_at);

        return Mail::to($pendingUserEmail->email)->send($mailable);
    }
}
