<?php

namespace App\DefaultPanel\Actions\Manager;

use Lorisleiva\Actions\Concerns\AsAction;

class UpdateManagerProfileAction {
    use AsAction;

    public function handle($request) {
        auth()->user()->update($request->except("phone"));
        if ($request->hasFile("avatar")) {
            auth()->user()->clearMediaCollection();
            auth()->user()->addMediaFromRequest('avatar')->toMediaCollection();
        }
    }
}
