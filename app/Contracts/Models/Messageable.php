<?php

namespace App\Contracts\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

interface Messageable
{
    /**
     * Get the attributes that can be searched
     */
    public static function getSearchableAttributes(): array;

    /**
     * Get the entity's messages.
     */
    public function messages(): Builder;

    /**
     * Get the entity's sent messages.
     */
    public function sentMessages(): MorphMany;

    /**
     * Get the entity's most recent sent message.
     */
    public function lastSentMessage(): MorphOne;

    /**
     * Get the entity's received messages.
     */
    public function receivedMessages(): MorphMany;

    /**
     * Get the entity's most recent sent message.
     */
    public function latestReceivedMessage(): MorphOne;

    /**
     * Get the entity's seen received messages.
     */
    public function seenMessages(): Builder;

    /**
     * Get the entity's unseen received messages.
     */
    public function unseenMessages(): Builder;
}
