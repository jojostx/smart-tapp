<?php

namespace App\Filament\Forms\Components;

use Phpsa\FilamentPasswordReveal\Password as FilamentPasswordRevealPassword;

class Password extends FilamentPasswordRevealPassword
{
    protected string $view = 'filament::forms.components.password';
}
