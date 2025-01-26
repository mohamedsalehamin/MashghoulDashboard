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
use App\DefaultPanel\Resources\Api\Customer\WalletTransactionResource;
use App\DefaultPanel\Resources\Api\Provider\User\TransactionResources;

use App\Models\PointsExchange;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tasawk\Api\Core;
use Tasawk\Api\Facade\Api;

class WalletService {
    public function index() {

        $query = auth()->user()->transactions()
            ->when(request()->filled('type'), fn($q) => $q->where('type', request('type')))
            ->latest()->paginate();
        return Api::isOk(__("customer information"))->setData(WalletTransactionResource::collection($query));
    }


}
