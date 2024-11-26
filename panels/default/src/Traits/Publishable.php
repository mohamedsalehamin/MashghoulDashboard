<?php

namespace App\DefaultPanel\Traits;

trait Publishable {
    public function toggleStatus(): bool {
        if ($this->isActive()) {
            return $this->inActive();
        }
        return $this->active();
    }

    public function isActive() {
        return $this->status;
    }

    public function active() {
        return $this->update(['status' => 1]);
    }

    public function inActive() {
        return $this->update(['status' => 0]);
    }

    public function scopeEnabled($query) {
        return $query->where('status', 1);
    }
}
