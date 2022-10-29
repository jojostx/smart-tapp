<?php

namespace App\Filament\Actions\Tables;

use Closure;
use Filament\Support\Actions\Concerns\HasKeyBindings;
use Filament\Tables\Actions\Action;

class CopyAction extends Action
{
  use HasKeyBindings;

  protected string $view = 'filament::tables.actions.copy-action';
  protected string $type = 'button';
  protected array $allowedTypes = ['button', 'iconButton', 'grouped'];
  protected string | Closure | null $content = '';

  protected function setUp(): void
  {
    parent::setUp();

    $this->label(__('Copy'));

    $this->successNotificationTitle(__('Copied!'));

    $this->color('primary');

    $this->icon('heroicon-o-clipboard');
  }

  public function content(string | Closure | null $content): static
  {
    $this->content = $content;

    return $this;
  }

  public function view(string $view): static
  {
    return $this;
  }

  public function button(): static
  {
    $this->type = 'button';

    return $this;
  }

  public function link(): static
  {
    $this->type = 'button';

    return $this;
  }

  public function grouped(): static
  {
    $this->type = 'grouped';

    return $this;
  }

  public function iconButton(): static
  {
    $this->type = 'iconButton';

    return $this;
  }

  public static function getDefaultName(): ?string
  {
    return 'copy';
  }

  public function getContent(): string
  {
    return $this->evaluate($this->content) ?? '';
  }

  public function getType(): string
  {
    return in_array($this->type, $this->allowedTypes) ? $this->type : 'button';
  }

  public function getSuccessMessage(): string
  {
    return $this->evaluate($this->successNotificationTitle) ?? __('Copied!');
  }
}
