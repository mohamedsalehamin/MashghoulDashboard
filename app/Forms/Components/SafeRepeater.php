<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Repeater;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Filament's Repeater::getItemLabel() dereferences child schema state before checking for null schema
 * (see vendor/filament/forms Repeater ~1194). Nested relationship groups can yield a transient null
 * container and crash the page; short-circuit until the item schema exists.
 */
class SafeRepeater extends Repeater
{
    public function getItemLabel(string $key): string|Htmlable|null
    {
        $container = $this->getChildSchema($key);

        if ($container === null) {
            return null;
        }

        return $this->evaluate($this->itemLabel, [
            'container' => $container,
            'item' => $container,
            'key' => $key,
            'schema' => $container,
            'state' => $container->getStateSnapshot(),
            'uuid' => $key,
        ]);
    }
}
