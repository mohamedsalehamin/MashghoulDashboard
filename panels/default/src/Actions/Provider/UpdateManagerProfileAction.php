<?php

namespace App\DefaultPanel\Actions\Provider;

use Lorisleiva\Actions\Concerns\AsAction;

class UpdateProviderProfileAction {
    use AsAction;

    public function handle($request) {
        auth()->user()->update($request->except("phone"));
        if ($request->hasFile("avatar")) {
            auth()->user()->clearMediaCollection();
            auth()->user()->addMediaFromRequest('avatar')->toMediaCollection();
        }
    }
}
