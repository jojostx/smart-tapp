<?php

declare(strict_types=1);

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

class SingleOptionMultiSelect extends Select
{
    public function relationshipName($relationshipName, $titleColumnName): static
    {
        $this->relationship = $relationshipName;
        $this->relationshipTitleColumnName = $titleColumnName;

        return $this;
    }

    public function getRelationship(): BelongsToMany
    {
        $model = $this->getModelInstance();

        if (! $model instanceof Model) {
            $class = $this->getModel();
            $model = new $class;
        }

        $relationship = $this->getRelationshipName();

        return $model->{$relationship}();
    }

    public function saveRelationships(): void
    {
        $this->getRelationship()->sync($this->getState() !== null ? [$this->getState()] : []);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (self $component): void {
            $relationship = $component->getRelationship();

            $model = $relationship->first();

            if (! $model) {
                return;
            }

            $component->state($model->id);
        });

        $this->rule(
            static fn (Select $component): Exists => Rule::exists(
                $component->getRelationship()->getModel()::class,
                $component->getRelationship()->getRelatedKeyName(),
            )
        );

        $this->dehydrated(false);
    }
}
