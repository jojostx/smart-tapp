<?php

namespace App\Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class Qrcode extends Component
{
    use Concerns\HasHelperText;
    use Concerns\HasHint;
    use Concerns\HasName;

    protected string $view = 'filament::forms.components.qrcode';

    protected $content = null;
    protected string|Closure| null $downloadName = 'parkinglot_qrcode';

    final public function __construct(string $name)
    {
        $this->name($name);
        $this->statePath($name);
    }

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->dehydrated(false);
    }

    public function content($content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Sets the Download Name for the qrcode
     *
     * @param string|Closure $downloadName
     * @return self
     */
    public function downloadName(string|Closure $downloadName): self
    {
        $this->downloadName = $downloadName;

        return $this;
    }

    protected function shouldEvaluateWithState(): bool
    {
        return false;
    }

    public function getDownloadName(): string
    {
        $downloadName = $this->evaluate($this->downloadName);

        $downloadName = !blank($downloadName) && is_string($downloadName) ? str($downloadName.' parkinglot qrcode')->snake() : 'parkinglot_qrcode';
        
        return $downloadName;
    }

    public function getId(): string
    {
        return parent::getId() ?? $this->getStatePath();
    }

    public function getLabel(): string | Htmlable | null
    {
        return parent::getLabel() ?? (string) Str::of($this->getName())
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();
    }

    public function getContent()
    {
        return $this->evaluate($this->content);
    }
}
