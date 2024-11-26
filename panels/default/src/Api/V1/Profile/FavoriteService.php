<?php

namespace App\DefaultPanel\Api\V1\Profile;

use App\DefaultPanel\Actions\Shared\Authentication\ChangeUserPhone;
use App\DefaultPanel\Actions\Shared\Authentication\RemoveVerficationCodes;
use App\DefaultPanel\Actions\Shared\Authentication\UpdateCustomerProfile;
use App\DefaultPanel\Actions\Shared\Authentication\UpdateUserPassword;
use App\DefaultPanel\Actions\Shared\Authentication\UpdateUserToken;
use App\DefaultPanel\Api\V1\Customer\Profile\Product;
use App\DefaultPanel\Enum\OrderStatus;
use App\DefaultPanel\Requests\Api\Profile\ProfileSettingRequest;
use App\DefaultPanel\Requests\Api\Profile\UpdateCustomerProfileRequest;
use App\DefaultPanel\Requests\Api\Profile\UpdatePasswordRequest;
use App\DefaultPanel\Requests\Api\Profile\VerifyAltPhoneRequest;
use App\DefaultPanel\Resources\Api\LightDoctorResource;
use App\DefaultPanel\Resources\Api\LightLabResource;
use App\DefaultPanel\Resources\Api\PatientDataResource;
use App\DefaultPanel\Resources\Api\PatientResource;
use App\DefaultPanel\Resources\Api\Products\LightProductResource;
use App\DefaultPanel\Resources\Api\User\ReportResources;
use App\DefaultPanel\Resources\Api\User\TransactionResources;
use App\UsersModule\Models\Doctor;
use App\UsersModule\Models\Lab;
use Tasawk\Api\Core;
use Tasawk\Api\Facade\Api;

class FavoriteService {

    public function doctors(): Core {
        return Api::isOk(__("fav list"), LightDoctorResource::collection(auth()->user()->favorite(Doctor::class)->paginate(15)));

    }

    public function labs(): Core {
        return Api::isOk(__("fav list"), LightLabResource::collection(auth()->user()->favorite(Lab::class)->paginate(15)));

    }
}
