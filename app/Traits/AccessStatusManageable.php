<?php

namespace App\Traits;

use App\Enums\Models\AccessStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;

trait AccessStatusManageable
{
	/**
	 * Scope a query to only include popular users.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $query
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function scopeStatus(Builder $query, string|AccessStatus $type = '')
	{
		$status = is_string($type) ? AccessStatus::from($type) :  $type;

		$elapsedFragment = 'DATE_ADD(issued_at, INTERVAL IFNULL(expiry_period, 0) MINUTE)';
		$invalidFragment = 'DATE_SUB(TIMESTAMPADD(DAY, `validity_period`, `issued_at`), INTERVAL IFNULL(`expiry_period`, 0) MINUTE)';

		$query = match ($status) {
			AccessStatus::EXPIRED => $query->where('expiry_period', '!=', 0)
				->whereRaw("now() > {$elapsedFragment}")
				->whereRaw("now() < {$invalidFragment}"),

			AccessStatus::ISSUED => $query->where('expiry_period', '!=', 0)
				->whereRaw("now() < {$elapsedFragment}")
				->whereRaw("now() < {$invalidFragment}"),

			AccessStatus::ACTIVE => $query->where('expiry_period', '=', 0)
				->whereRaw("now() < {$invalidFragment}"),

			AccessStatus::INACTIVE => $query->where('expiry_period', '=', 0)
				->whereRaw("now() > {$invalidFragment}"),

			default => $query
		};

		return $query;
	}

	/**
	 * Get the current state of the access.
	 * [expired, issued, active, inactive]
	 * to specify when the access is expired, the 'expiry_period' attribute should be set and elapsed and the 'valid_until' is not in the past.
	 * to specify when the access is issued the 'expiry_period' attribute should be set and not elapsed and the 'valid_until' attribute should not be in the past.
	 * to specify when the access is active, the 'expiry_period' attribute should be 0 and the 'valid_until' attribute should not be in the past.
	 * to specify when the access is inactive the expiry_period' attribute should be 0 and the 'valid_until' attribute should be in the past.
	 *
	 * @return \Illuminate\Database\Eloquent\Casts\Attribute
	 */
	protected function status(): Attribute
	{
		return Attribute::make(
			get: function ($value, $attributes) {
				$issued_at = $this->asDateTime($attributes['issued_at']);

				$valid_until =  $this->asDateTime($attributes['issued_at'])->addDays($attributes['validity_period']);

				$expiry_period = $attributes['expiry_period'];

				$elapsed = \elapsed($issued_at->addMinutes($expiry_period));

				$invalid = \elapsed($valid_until->subMinutes($expiry_period));

				// expired
				if (boolval($expiry_period) && $elapsed && !$invalid) {
					return AccessStatus::EXPIRED;
				}

				// issued
				if (boolval($expiry_period) && !$elapsed && !$invalid) {
					return AccessStatus::ISSUED;
				}

				// active
				if (!boolval($expiry_period) && !$invalid) {
					return AccessStatus::ACTIVE;
				}

				// inactive | deactivated
				if (!boolval($expiry_period) && $invalid) {
					return AccessStatus::INACTIVE;
				}

				return AccessStatus::INACTIVE;
			},
		);
	}

	/**
	 * checks if the access is valid (not inactive or deactived).
	 *
	 * @return bool
	 */
	public function isValid(): bool
	{
		return !($this->status === AccessStatus::INACTIVE);
	}

	/**
	 * checks if the access is issued.
	 *
	 * @return bool
	 */
	public function isIssued(): bool
	{
		return $this->status === AccessStatus::ISSUED;
	}

	/**
	 * checks if the access is active.
	 *
	 * @return bool
	 */
	public function isActive(): bool
	{
		return $this->status === AccessStatus::ACTIVE;
	}

	/**
	 * checks if the access is expired.
	 *
	 * @return bool
	 */
	public function isExpired(): bool
	{
		return $this->status === AccessStatus::EXPIRED;
	}

	/**
	 * checks if the access is inactive or deactivated.
	 *
	 * @return bool
	 */
	public function isInactive(): bool
	{
		return $this->status === AccessStatus::INACTIVE;
	}

	/**
	 * issue the access and send out the access activation notification.
	 * issue sets 'expiry_period' to the expiry period set by tenant's Super Admin or admin.
	 *
	 * @param int $expiry_period
	 * @param int $validity_period
	 * @param bool $shouldNotify
	 * 
	 * @return bool
	 */
	public function issue(?int $expiry_period = 0, ?int $validity_period = 0, bool $shouldNotify = false): bool
	{
		// MAX_VALIDITY_PERIOD
		// MAX_EXPIRY_PERIOD
		if (blank($expiry_period) || !in_range($expiry_period, 9, 31)) {
			$expiry_period = $this->expiry_period ?? 30;
		}

		if (blank($validity_period) || !in_range($validity_period, 0, 3)) {
			$validity_period = $this->validity_period ?? 2;
		}

		/**
		 * @var \Illuminate\Database\Eloquent\Model|\App\Concerns\CanSendAccessActivationNotification $this
		 */
		$updated = $this->forceFill([
			'expiry_period' => $expiry_period,
			'validity_period' => $validity_period,
			'issued_at' => $this->freshTimestamp(),
		])->save();

		if ($shouldNotify && $updated) {
			return $this->sendAccessActivationNotification();
		}

		return $updated;
	}

	/**
	 * activate the access.
	 * activate sets 'expiry_period' attribute to 0, if and only if the access is issued or expired.
	 *
	 * @return bool
	 */
	public function activate(bool $shouldNotify = false): bool
	{
		if ($this->isActive()) {
			return false;
		}

		if (elapsed($this->valid_until?->subMinutes($this->expiry_period) ?? now())) {
			$this->forceFill([
				'issued_at' => now(),
			]);
		}

		$updated = $this->forceFill([
			'expiry_period' => 0,
		])->save();

		if ($shouldNotify && $updated) {
			return $this->sendAccessActivationNotification();
		}

		return $updated;
	}

	/**
	 * deactivate the access.
	 * deactivate sets 'expiry_period' attribute to 0 and the 'valid_until' to a period in the past.
	 *
	 * @return bool
	 */
	public function deactivate(): bool
	{
		if ($this->isInactive()) {
			return true;
		}

		return $this->forceFill([
			'expiry_period' => 0,
			'issued_at' => $this->issued_at->subDays($this->validity_period)
		])->save();
	}
}
