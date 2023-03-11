<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

class Message extends Model
{
    // Cache::forever('last_active_at:uuid', time());
    // Cache::get('last_active_at:uuid');

    use HasFactory;

    /**
     * The guarded attributes on the model.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'seen_at' => 'datetime',
    ];

    /**
     * Get the primary key for the model.
     */
    public static function getPrimaryKeyName()
    {
        return (new static())->getKeyName();
    }

    /**
     * The sender of the message.
     */
    public function sender(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The receiver of the message.
     */
    public function receiver(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Mark the message as seen.
     */
    public function markAsSeen()
    {
        if (is_null($this->seen_at)) {
            $this->forceFill(['seen_at' => $this->freshTimestamp()])->save();
        }
    }

    /**
     * Mark the selected messages as seen.
     */
    public static function markMessagesAsSeen(array|string|int $keys = [])
    {
        collect(Arr::wrap($keys))->whenNotEmpty(function (Collection $keys) {
            static::query()
                ->whereIn(static::getPrimaryKeyName(), $keys->toArray())
                ->update(['seen_at' => Date::now()]);
        });
    }

    /**
     * Mark the message as unseen.
     */
    public function markAsUnseen()
    {
        if (!is_null($this->seen_at)) {
            $this->forceFill(['seen_at' => null])->save();
        }
    }

    /**
     * Determine if a message has been seen.
     */
    public function seen()
    {
        return $this->seen_at !== null;
    }

    /**
     * Determine if a message has not been seen.
     */
    public function unseen()
    {
        return $this->seen_at === null;
    }

    /**
     * Scope a query to only include seen messages.
     */
    public function scopeWhereSeen(Builder $query): Builder
    {
        return $query->whereNotNull('seen_at');
    }

    /**
     * Scope a query to only include unseen messages.
     */
    public function scopeWhereUnseen(Builder $query): Builder
    {
        return $query->whereNull('seen_at');
    }

    /**
     * Scope a query to only include messages sent by a given model.
     */
    public function scopeWhereSender(Builder $query, Model $sender): Builder
    {
        return $query->where('sender_id', $sender->getKey())
            ->where('sender_type', $sender->getMorphClass());
    }

    /**
     * Scope a query to only include messages received by a given model.
     */
    public function scopeWhereReceiver(Builder $query, Model $receiver): Builder
    {
        return $query->where('receiver_id', $receiver->getKey())
            ->where('receiver_type', $receiver->getMorphClass());
    }

    /**
     * Scope a query to only include messages between two given models.
     */
    public function scopeWhereBetween(Builder $query, Model $messageable_1, Model $messageable_2): Builder
    {
        return $query->where([
            ['sender_id', '=', $messageable_2->getKey()],
            ['sender_type', '=', $messageable_2->getMorphClass()],
            ['receiver_id', '=', $messageable_1->getKey()],
            ['receiver_type', '=', $messageable_1->getMorphClass()],
        ])->orWhere([
            ['sender_id', '=', $messageable_1->getKey()],
            ['sender_type', '=', $messageable_1->getMorphClass()],
            ['receiver_id', '=', $messageable_2->getKey()],
            ['receiver_type', '=', $messageable_2->getMorphClass()],

        ]);
    }
}
