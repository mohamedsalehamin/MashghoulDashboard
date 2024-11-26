<?php

namespace App\DefaultPanel\Actions\Shared\Authentication;

use Lorisleiva\Actions\Concerns\AsAction;

class UpdateCustomerProfile {
    use AsAction;

    public function handle($request) {
        auth()->user()->update([
            'name' => $request->get('first_name') . ' ' . $request->get('last_name'),
            'email' => $request->get('email'),
            'city_id' => $request->get('city_id'),
            'id_number' => $request->get('id_number'),
            'data' => [
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),

            ]
        ]);
        if ($request->hasFile("avatar")) {
            auth()->user()->clearMediaCollection();
            auth()->user()->addMediaFromRequest("avatar")->toMediaCollection();
        }
    }
}
