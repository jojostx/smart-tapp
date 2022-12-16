<?php

namespace App\Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\Concerns;
use Filament\Forms\Components\Field;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Validation\Rule;

class PhoneNumberInput extends Field
{
    use Concerns\CanBeAutocompleted;
    use Concerns\CanBeLengthConstrained;
    use Concerns\HasAffixes;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasPlaceholder;
    use HasExtraAlpineAttributes;

    protected string $view = 'filament::forms.components.phone-number-input';

    protected array | Arrayable | Closure | null $allowedCountries = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(static function (PhoneNumberInput $component, $state): void {
            if (blank($state)) {
                return;
            }

            $component->state((string) $state);
        });

        $this->dehydrateStateUsing(static function (PhoneNumberInput $component, $state) {
            if (blank($state)) {
                return null;
            }
        });

        $this->regex(static fn (PhoneNumberInput $component) => '/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/');
    }

    public function allowedCountries(array | Arrayable | Closure | null $countries, $shouldBeValidated = true): static
    {
        $this->allowedCountries = $countries;

        $countries = $this->evaluate($countries);

        if ($shouldBeValidated && is_array($countries) && filled($countries)) {
            $this->rule(Rule::phone()->country($countries));
        }

        return $this;
    }

    public function getAllowedCountries(): ?array
    {
        $options = $this->evaluate($this->allowedCountries);

        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        return $options;
    }
}
