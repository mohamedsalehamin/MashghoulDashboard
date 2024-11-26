<?php

namespace App\DefaultPanel\Lib;

use Iterator;

class Breadcrumbs implements Iterator {

    private int $key = 0;

    private array $breadcrumbs = [];

    public function add($title, $url=null): static {
        $this->breadcrumbs[] = [
            'title' => $title,
            'route' => $url,
        ];
        return $this;
    }

    public function current() {
        return $this->breadcrumbs[$this->key];
    }

    public function next() {
        ++$this->key;
    }

    public function key() {
        return $this->key;
    }

    public function valid() {
        return isset($this->breadcrumbs[$this->key]);
    }

    public function rewind() {
        $this->key = 0;
    }

    public function isLast(): bool {
        return $this->key == count($this->breadcrumbs)-1;
    }

}
