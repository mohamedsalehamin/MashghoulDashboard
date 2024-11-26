<?php

namespace App\DefaultPanel;

use Str;

class PluginManager {
    static public function make() {
        return new static();
    }

    public function getPlugins(): array {
        $plugins = [];
        $classes = collect(get_declared_classes())
            ->filter(fn($className) => Str::startsWith($className, "App\\") && in_array('Filament\Contracts\Plugin', class_implements($className)))
            ->filter(fn($className) => Str::contains(explode("\\", $className)[1], "Plugin"));

        foreach ($classes as $class) {
            $plugin = new $class();
            $plugins[] = $plugin;
        }
        return $plugins;
    }
}
