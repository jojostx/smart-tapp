<?php

namespace App\Filament\Tables\Columns;

use Closure;
use Filament\Support\Actions\Concerns\CanBeHidden;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;

class ActionableTextColumn extends TextColumn
{
    use CanBeHidden {
        isHidden as baseActionIsHidden;
    }

    protected string $view = 'filament::tables.columns.actionable-text-column';

    protected array $actions = [];

    protected bool | Closure $animated = false;

    protected string | Closure | null $triggerIcon = null;

    protected string | Closure | null $triggerPosition = null;

    protected string | Closure | null $triggerColor = null;

    protected string | Closure | null $triggerLabel = null;

    public function animated(bool | Closure $condition = true): static
    {
        $this->animated = $condition;

        return $this;
    }

    public function actions(array | Closure $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    public function triggerColor(string | Closure | null $triggerColor): static
    {
        $this->triggerColor = $triggerColor;

        return $this;
    }

    public function triggerIcon(string | Closure | null $icon): static
    {
        $this->triggerIcon = $icon;

        return $this;
    }

    public function triggerLabel(string | Closure | null $triggerLabel): static
    {
        $this->triggerLabel = $triggerLabel;

        return $this;
    }

    public function triggerPosition(string | Closure | null $position): static
    {
        $this->descriptionPosition = $position;

        return $this;
    }

    public function getActions(): array
    {
        $actions = [];

        foreach ($this->actions as $action) {
            $actions[$action->getName()] = $action->grouped()->record($this->getRecord());
        }

        return $actions;
    }

    public function getTriggerColor(): ?string
    {
        return $this->evaluate($this->triggerColor) ?? 'secondary';
    }

    public function getTriggerLabel(): string
    {
        return $this->evaluate($this->triggerLabel) ?? (string) Str::of(__('filament-support::actions/group.trigger.label'))
            ->before('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();
    }

    public function getTriggerIcon(): string
    {
        return $this->evaluate($this->triggerIcon) ?? 'heroicon-o-dots-vertical';
    }

    public function getTriggerPosition(): string
    {
        $triggerPosition = $this->evaluate($this->triggerPosition);

        if (blank($triggerPosition) || !in_array($triggerPosition, ['before', 'after'])) {
            return 'after';
        }

        return $triggerPosition;
    }

    public function isAnimated(): string
    {
        return  (bool) $this->evaluate($this->animated);
    }

    public function isActionHidden(): bool
    {
        $condition = $this->baseActionIsHidden();

        if ($condition) {
            return true;
        }

        foreach ($this->getActions() as $action) {
            if ($action->isHidden()) {
                continue;
            }

            return false;
        }

        return true;
    }
}
