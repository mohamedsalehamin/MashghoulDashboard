<?php

namespace App\DefaultPanel\Api\V1\Customer\Profile;

use Exception;
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
use App\DefaultPanel\Resources\Api\Customer\WithdrawalRequestResource;
use App\DefaultPanel\Resources\Api\Customer\User\TransactionResources;

use App\DefaultPanel\Enum\WalletWithdrawEnum;
use App\Models\PointsExchange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function withdrawalRequests()
    {
        $withdrawalRequests = auth()->user()->withdrawalRequests()
            ->latest()
            ->paginate();

        return Api::isOk(__("withdrawal requests"))->setData(WithdrawalRequestResource::collection($withdrawalRequests));
    }

    public function requestWithdrawal(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        // Validate request
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'bank_details' => ['required', 'array'],
            'bank_details.account_name' => ['required', 'string'],
            'bank_details.account_number' => ['required', 'string', 'min:8', 'max:15'],
            'bank_details.bank_name' => ['required', 'string'],
            'bank_details.iban' => ['required', 'string', 'min:14', 'max:18'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Check if user has pending withdrawal request
        if ($user->hasPendingWithdrawalRequest()) {
            return Api::isError(__('panel.messages.you_already_have_pending_withdrawal_request'));
        }

        // Check if amount is more than wallet balance
        if ($validated['amount'] > $user->wallet->balance) {
            return Api::isError(__('panel.messages.insufficient_balance'));
        }

        // Get the withdrawable entity by checking which relationship exists
        $withdrawable = null;
        if ($user->toCustomer()->exists()) {
            $withdrawable = $user->toCustomer()->first();
        } elseif ($user->provider()->exists()) {
            $withdrawable = $user->provider()->first();
        }

        if (!$withdrawable) {
            return Api::isError(__('Invalid user type'));
        }

        // Create withdrawal request in a transaction
        try {
            $withdrawalRequest = DB::transaction(function () use ($validated, $user, $withdrawable) {
                $withdrawalRequest = $user->withdrawalRequests()->create([
                    'amount' => $validated['amount'],
                    'bank_details' => $validated['bank_details'],
                    'status' => WalletWithdrawEnum::PENDING->value,
                    'withdrawable_type' => get_class($withdrawable),
                    'withdrawable_id' => $withdrawable->id
                ]);
                $withdrawalRequest->addTimeLine([
                    'ar' => __('panel.messages.withdrawal_request_created', [], 'ar'),
                    'en' => __('panel.messages.withdrawal_request_created', [], 'en')
                ], WalletWithdrawEnum::PENDING);
                return $withdrawalRequest;
            });

            return Api::isOk(__('Withdrawal request created successfully'))
                ->setData(new WithdrawalRequestResource($withdrawalRequest));
        } catch (Exception $e) {
            return Api::isError(__('Failed to create withdrawal request'));
        }
    }
}
