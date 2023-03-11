<?php

namespace App\Traits;

use App\Contracts\Models\Messageable as ModelsMessageable;
use App\DTOs\InboxSearchResult;
use App\Models\Tenant\Message;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait Messageable
{
    /**
     * Get the entity's messages.
     */
    public function messages(): Builder
    {
        return Message::query()
            ->whereSender($this)
            ->orWhere(fn ($query) => $query->whereReceiver($this))
            ->oldest();
    }

    /**
     * Get the entity's sent messages.
     */
    public function sentMessages(): MorphMany
    {
        return $this->morphMany(Message::class, 'sender')->oldest();
    }

    /**
     * Get the entity's most recent sent message.
     */
    public function lastSentMessage(): MorphOne
    {
        return $this->morphOne(Message::class, 'sender')->latestOfMany();
    }

    /**
     * Get the entity's received messages.
     */
    public function receivedMessages(): MorphMany
    {
        return $this->morphMany(Message::class, 'receiver')->oldest();
    }

    /**
     * Get the entity's most recent sent message.
     */
    public function latestReceivedMessage(): MorphOne
    {
        return $this->morphOne(Message::class, 'receiver')->latestOfMany();
    }

    /**
     * Get the entity's seen received messages.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function seenMessages(): Builder
    {
        return $this->receivedMessages()->seen();
    }

    /**
     * Get the entity's unseen received messages.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function unseenMessages(): Builder
    {
        return $this->receivedMessages()->unseen();
    }

    abstract public static function getSearchableAttributes(): array;

    public static function getMessageableSearchResults(string $searchQuery): Collection
    {
        $searchQuery = strtolower($searchQuery);

        $query = static::getMessagebleSearchEloquentQuery();

        foreach (explode(' ', $searchQuery) as $searchQueryWord) {
            $query->where(function (Builder $query) use ($searchQueryWord) {
                $isFirst = true;

                foreach (static::getSearchableAttributes() as $attributes) {
                    static::applySearchAttributeConstraint($query, Arr::wrap($attributes), $searchQueryWord, $isFirst);
                }
            });
        }

        return $query
            ->limit(static::getMessageableSearchResultsLimit())
            ->get()
            ->map(function (Model|ModelsMessageable $messageable): ?InboxSearchResult {
                return new InboxSearchResult(
                    title: static::getMessageableSearchResultTitle($messageable),
                    identifier: static::getSearchIdentifier($messageable),
                    details: static::getMessageableSearchResultDetails($messageable),
                );
            })
            ->filter();
    }

    public static function getSearchIdentifier(ModelsMessageable|Model $messageable): string|int
    {
        return $messageable->getAttribute('uuid') ?? $messageable->getKey();
    }

    public static function getMessagebleSearchEloquentQuery(): Builder
    {
        return static::getModel()::query();
    }

    public static function getMessageableSearchResultsLimit(): int
    {
        return 50;
    }

    public static function getMessageableSearchResultDetails(ModelsMessageable|Model $messageable): array
    {
        // all the searchable attributes
        $details = [];

        foreach (static::getSearchableAttributes() as $attribute) {
            $details[ucfirst($attribute)] = $messageable->getAttribute($attribute);
        }

        return $details;
    }

    public static function getMessageableSearchResultTitle(Model $messageable): string
    {
        return (string) Str::of(class_basename($messageable))
            ->kebab()
            ->replace('-', ' ');
    }

    protected static function applySearchAttributeConstraint(Builder $query, array $searchAttributes, string $searchQuery, bool &$isFirst): Builder
    {
        /** @var Connection $databaseConnection */
        $databaseConnection = $query->getConnection();

        $searchOperator = match ($databaseConnection->getDriverName()) {
            'pgsql' => 'ilike',
            default => 'like',
        };

        $messageable = $query->getModel();

        foreach ($searchAttributes as $searchAttribute) {
            $whereClause = $isFirst ? 'where' : 'orWhere';

            $query->when(
                method_exists($messageable, 'isTranslatableAttribute') && $messageable->isTranslatableAttribute($searchAttribute),
                function (Builder $query) use ($databaseConnection, $searchAttribute, $searchOperator, $searchQuery, $whereClause): Builder {
                    $searchColumn = match ($databaseConnection->getDriverName()) {
                        'pgsql' => "{$searchAttribute}::text",
                        default => "json_extract({$searchAttribute}, '$')",
                    };

                    return $query->{"{$whereClause}Raw"}(
                        "lower({$searchColumn}) {$searchOperator} ?",
                        "%{$searchQuery}%",
                    );
                },
                fn (Builder $query): Builder => $query->when(
                    Str::of($searchAttribute)->contains('.'),
                    fn ($query) => $query->{"{$whereClause}Relation"}(
                        (string) Str::of($searchAttribute)->beforeLast('.'),
                        (string) Str::of($searchAttribute)->afterLast('.'),
                        $searchOperator,
                        "%{$searchQuery}%",
                    ),
                    fn ($query) => $query->{$whereClause}(
                        $searchAttribute,
                        $searchOperator,
                        "%{$searchQuery}%",
                    ),
                ),
            );

            $isFirst = false;
        }

        return $query;
    }
}
