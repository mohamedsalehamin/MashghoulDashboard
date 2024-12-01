<?php

namespace App\DefaultPanel\Api\V1\Customer\Profile;

use App\ContentModule\Models\Point;
use App\DefaultPanel\Actions\Shared\Authentication\ChangeUserPhone;
use App\DefaultPanel\Actions\Shared\Authentication\RemoveVerficationCodes;
use App\DefaultPanel\Actions\Shared\Authentication\UpdateCustomerProfile;
use App\DefaultPanel\Actions\Shared\Authentication\UpdateUserToken;
use App\DefaultPanel\Requests\Api\Customer\Profile\ProfileSettingRequest;
use App\DefaultPanel\Requests\Api\Customer\Profile\UpdateCustomerProfileRequest;
use App\DefaultPanel\Requests\Api\Customer\Profile\VerifyAltPhoneRequest;
use App\DefaultPanel\Resources\Api\Customer\MyPointResource;
use App\DefaultPanel\Resources\Api\Customer\CustomerResource;
use App\DefaultPanel\Resources\Api\Provider\User\TransactionResources;

use App\Models\PointsExchange;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tasawk\Api\Core;
use Tasawk\Api\Facade\Api;

class ProfileService {
    public function index() {
        return Api::isOk(__("customer information"))->setData( CustomerResource::make(auth()->user()));
    }

    public function update(UpdateCustomerProfileRequest $request): Core {
        UpdateCustomerProfile::run($request);

        if (auth()->user()->phone !== $request->get('phone')) {
            ChangeUserPhone::run(auth()->user(), $request->get('phone'));
        }
        return Api::isOk(__("Account information updated"))->setData(new CustomerResource(auth()->user()));
    }



    public function settings(ProfileSettingRequest $request): Core {
        auth()->user()->update(['settings' => $request->validated()]);
        return Api::isOk(__("User settings updated"));
    }


    public function verifyAltPhone(VerifyAltPhoneRequest $request): Core {
        auth()->user()->update(['phone' => $request->get('phone')]);
        RemoveVerficationCodes::run(auth()->user());
        UpdateUserToken::run(auth()->user());
        return Api::isOk(__("Phone verified, try to login"));
    }

    public function deleteAnalysisDelete($id) {
        Media::where('id',$id)->delete();
        return Api::isOk(__("Deleted"));
    }



    public function transactions() {

        return Api::isOk(__("Transactions list"), TransactionResources::collection(auth()->user()
            ->reservations()
            ->whereHas('transaction')
            ->where('status', "!=", 'pending')
            ->when(request()->has('type'), function ($q) {
                $type = request()->get('type') == 'pay' ? 'paid' : 'refunded';
                return $q->whereHas('transaction', fn($q2) => $q2->where('status', $type));
            })
            ->latest()->paginate()));
    }




}
