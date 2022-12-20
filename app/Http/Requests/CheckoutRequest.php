<?php

namespace App\Http\Requests;

use App\Models\Tenant;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutRequest extends FormRequest
{
    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'plan' => 'required|exists:' . getCentralConnection() . '.' . 'plans,slug',
            'organization' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:12',
            'credit_card' => 'nullable|string|exists:' . getCentralConnection() . '.' . 'credit_cards,uuid',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        /** @var Tenant */
        $tenant = \tenant();
        $credit_card = $this->get('credit_card');

        if (\blank($tenant)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'plan' => __('Operation not allowed'),
            ]);
        }

        if ($this->hasUnchargeableCard($credit_card)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'credit_card' => __('Invalid credit card'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 30)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'plan' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::lower($this->input('plan')) . '|' . $this->ip();
    }

    /**
     * check if card is present and chargeable
     */
    public function hasChargeableCard(?string $credit_card): bool
    {
        /** @var Tenant */
        $tenant = \tenant();

        return filled($credit_card) && $tenant->chargeableCards()->contains('uuid', $credit_card);
    }

    /**
     * check if card is present and unchargeable
     */
    public function hasUnchargeableCard(?string $credit_card): bool
    {
        /** @var Tenant */
        $tenant = \tenant();

        return filled($credit_card) && $tenant->chargeableCards()->doesntContain('uuid', $credit_card);
    }
}
