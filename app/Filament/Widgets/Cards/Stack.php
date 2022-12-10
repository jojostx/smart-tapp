<?php

namespace App\Filament\Widgets\Cards;

use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Support\Concerns\HasExtraAttributes;
use Filament\Tables\Columns\Concerns\HasSpace;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\View\Component;

class Stack extends Component implements Htmlable
{
  use HasSpace;
  use EvaluatesClosures;
  use HasExtraAttributes;

  protected array $cards = [];

  protected ?array $cachedData = null;

  final public function __construct(array $cards)
  {
      $this->cards($cards);
  }

  public static function make(array $cards = []): static
  {
      return app(static::class, ['cards' => $cards]);
  }

  public function cards(array $cards): static
  {
    $this->cards = array_merge($this->cards, $cards);

    return $this;
  }

  public function getCards(): array
  {
    return $this->evaluate($this->cards);
  }

  public function getCachedData(): array
  {
    return $this->cachedData ??= $this->getData();
  }

  protected function getData(): array
  {
    return $this->getCards();
  }

  public function toHtml(): string
  {
      return $this->render()->render();
  }

  public function render(): View
  {
    return view('filament::widgets.cards.stack', $this->data());
  }
}
