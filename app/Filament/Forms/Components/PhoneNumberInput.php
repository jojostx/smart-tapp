<?php

namespace App\Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Concerns;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Illuminate\Contracts\Support\Arrayable;

class PhoneNumberInput extends Field
{
    use Concerns\CanBeAutocompleted;
    use Concerns\CanBeLengthConstrained;
    use Concerns\HasAffixes;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasPlaceholder;
    use HasExtraAlpineAttributes;

    protected string $view = 'filament::forms.components.phone-number-input';

    protected array | Arrayable | Closure | null $allowedCountries = ['NG', 'US'];

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

    public function allowedCountries(array | Arrayable | Closure | null $countries): static
    {
        $this->allowedCountries = $countries;

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

    public function getType(): string
    {
        return 'tel';
    }
}
