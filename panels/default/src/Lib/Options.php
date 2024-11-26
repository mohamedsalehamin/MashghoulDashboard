<?php

namespace App\DefaultPanel\Lib;

use Illuminate\Contracts\Support\Arrayable;

class Options implements Arrayable {

    public function __construct(protected $enum) {
    }

    static public function forEnum($enum) {
        return new static($enum::cases());

    }

    public function toSelect(): array {
        $array = [];
        foreach ($this->enum as $value) {
            $array[$value->name] = $value->value;
        }
        return $array;
    }

    public function toArray() {
        $array = [];
        foreach ($this->enum as $value) {
            $array[] = [
                'name' => $value->name,
                'label' => $value->value,
            ];
        }
        return $array;
    }

}
