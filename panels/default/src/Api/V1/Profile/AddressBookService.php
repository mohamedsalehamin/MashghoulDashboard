<?php

namespace App\DefaultPanel\Api\V1\Profile;


use Api;
use App\CrmModule\Models\AddressBook;
use App\DefaultPanel\Requests\Api\Customer\Profile\AddressBookRequest;
use App\DefaultPanel\Resources\Api\AddressBookResource;

class AddressBookService{
    public function index() {
        return Api::isOk(__("Addresses list"),AddressBookResource::collection(auth()->user()->addresses()->latest()->paginate()));
    }

    public function show(AddressBook $address) {
        return Api::isOk(__("Address data"),new AddressBookResource($address));
    }

    public function store(AddressBookRequest $request) {
        $address = auth()->user()->addresses()->create($request->validated());
        return Api::isOk(__("Address created"),new AddressBookResource($address));
    }

    public function update(AddressBookRequest $request, AddressBook $address) {
        $address->update($request->validated());
        return Api::isOk(__("Address updated"), new AddressBookResource($address));
    }

    public function destroy(AddressBook $address) {
        if (!in_array($address->id, auth()->user()->addresses->pluck('id')->toArray())) {
            return Api::isError(__("Unauthorized action"));
        }
        $address->delete();
        return Api::isOk(__("Address deleted"));
    }
}
