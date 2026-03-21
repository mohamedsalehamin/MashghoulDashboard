<?php

namespace App\Livewire\Site;

use App\DefaultPanel\Enum\WalletWithdrawEnum;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class WithdrawModal extends Component
{
    public $amount = '';
    public $bank_name = '';
    public $account_name = '';
    public $account_number = '';
    public $iban = '';

    protected function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:1'],
            'bank_name' => ['required', 'string', 'max:255'],
            'account_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'min:8', 'max:15'],
            'iban' => ['required', 'string', 'min:14', 'max:18'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'amount' => __('site.fields.amount') ?? 'المبلغ',
            'bank_name' => __('site.fields.bank_name') ?? 'اسم البنك',
            'account_name' => __('site.fields.account_name') ?? 'اسم المستفيد',
            'account_number' => __('site.fields.account_number') ?? 'رقم الحساب',
            'iban' => __('site.fields.iban') ?? 'آيبان',
        ];
    }

    public function requestWithdrawal()
    {
        $this->resetErrorBag();
        $this->validate();

        $user = site()->user();

        if ($user->hasPendingWithdrawalRequest()) {
            $this->addError('amount', __('panel.messages.you_already_have_pending_withdrawal_request'));
            return;
        }

        $balance = $user->wallet?->balance ?? 0;
        if ((float) $this->amount > (float) $balance) {
            $this->addError('amount', __('panel.messages.insufficient_balance'));
            return;
        }

        $withdrawable = $user->toCustomer()->first() ?? $user->provider()->first();
        if (! $withdrawable) {
            $this->addError('amount', __('Invalid user type'));
            return;
        }

        try {
            DB::transaction(function () use ($user, $withdrawable) {
                $withdrawalRequest = $user->withdrawalRequests()->create([
                    'amount' => $this->amount,
                    'bank_details' => [
                        'bank_name' => $this->bank_name,
                        'account_name' => $this->account_name,
                        'account_number' => $this->account_number,
                        'iban' => $this->iban,
                    ],
                    'status' => WalletWithdrawEnum::PENDING->value,
                    'withdrawable_type' => get_class($withdrawable),
                    'withdrawable_id' => $withdrawable->id,
                ]);
                $withdrawalRequest->addTimeline([
                    'ar' => __('panel.messages.withdrawal_request_created', [], 'ar'),
                    'en' => __('panel.messages.withdrawal_request_created', [], 'en'),
                ], WalletWithdrawEnum::PENDING);
            });

            $this->amount = '';
            $this->bank_name = '';
            $this->account_name = '';
            $this->account_number = '';
            $this->iban = '';
            $this->dispatch('withdrawal-requested');
            $this->dispatch('refresh-wallet-list');
        } catch (\Throwable $e) {
            $this->addError('amount', __('Failed to create withdrawal request'));
        }
    }

    public function closeWithdrawModal()
    {
        $this->amount = '';
        $this->bank_name = '';
        $this->account_name = '';
        $this->account_number = '';
        $this->iban = '';
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.site.withdraw-modal', [
            'withdrawErrors' => $this->getErrorBag()->getMessages(),
        ]);
    }
}
