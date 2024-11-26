<?php

namespace App\DefaultPanel\Forms\Components;

use Filament\Forms\Components\Select;
use App\DefaultPanel\Lib\FontawesomeIcons;

class SelectFontAwesomeIcon extends Select {
    protected string $view = 'filament.forms.components.select-font-awesome-icon';

    public function getOptions(): array {
        return FontawesomeIcons::toSelect();
    }
}
