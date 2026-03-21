<?php

namespace App\Livewire\Site;

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ProfileWalletList extends Component
{
    use WithPagination;

    #[On('refresh-wallet-list')]
    public function refreshList(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = site()->user();
        $wallet = $user->wallet;
        $balance = $wallet ? (float) $wallet->balance : 0;

        $depositTransactions = $wallet
            ? $wallet->transactions()->where('type', 'deposit')->latest()->paginate(15, ['*'], 'deposit_page')
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
        $depositTransactions->withPath(parse_url(route('site.account.wallet'), PHP_URL_PATH));

        $withdrawTransactions = $wallet
            ? $wallet->transactions()->where('type', 'withdraw')->latest()->paginate(15, ['*'], 'withdraw_page')
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
        $withdrawTransactions->withPath(parse_url(route('site.account.wallet'), PHP_URL_PATH));

        $withdrawalRequests = $user->withdrawalRequests()->latest()->paginate(15, ['*'], 'requests_page');
        $withdrawalRequests->withPath(parse_url(route('site.account.wallet'), PHP_URL_PATH));

        $hasDeposits = $depositTransactions->isNotEmpty();
        $hasWithdraws = $withdrawTransactions->isNotEmpty();
        $hasRequests = $withdrawalRequests->isNotEmpty();

        return view('livewire.site.profile-wallet-list', [
            'balance' => $balance,
            'depositTransactions' => $depositTransactions,
            'withdrawTransactions' => $withdrawTransactions,
            'withdrawalRequests' => $withdrawalRequests,
            'hasDeposits' => $hasDeposits,
            'hasWithdraws' => $hasWithdraws,
            'hasRequests' => $hasRequests,
        ]);
    }
}
