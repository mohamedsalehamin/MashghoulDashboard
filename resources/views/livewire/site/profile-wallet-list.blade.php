{{-- Empty state: show when balance is 0 --}}
<div class="alert alert-danger custom-alert {{ $balance > 0 ? 'd-none' : '' }} d-flex align-items-center gap-2 mb-4"
    role="alert" style="background-color: #fce8e8; border-color: #f7c6c6; color: #d9534f;">
    <i class="fa-solid fa-circle-info"></i>
    <div>{{ __('site.messages.insufficient_wallet_balance') }}</div>
</div>

{{-- Balance card: show when balance > 0 --}}
@if($balance > 0)
<div class="bg-blue rounded-4 p-4 mb-4 d-flex justify-content-between align-items-center text-white flex-wrap gap-3">
    <div class="d-flex align-items-center gap-2">
        <span class="fz20">{{ __('site.heading.wallet_balance') }}</span>
    </div>
    <span class="fz32 fw-bold ms-2">{{ number_format($balance, 2) }} <i class="icon-saudi_riyal"></i></span>
    <div>
        <button class="btn btn-green" type="button" data-bs-toggle="modal" data-bs-target="#withdrawModal">
            {{ __('site.buttons.withdrawal_request') }}
        </button>
    </div>
</div>
@endif

{{-- Tabs: only show tabs that have data --}}
@if($hasDeposits || $hasWithdraws || $hasRequests)
<ul class="rewards-tabs list-unstyled nav nav-tabs border-0 justify-content-center mb-4 gap-2" role="tablist">
    @if($hasDeposits)
    <li class="nav-item" role="presentation">
        <button class="nav-link tab-btn active" data-bs-toggle="tab" data-bs-target="#tab-deposit" type="button" role="tab">
            {{ __('panel.enums.deposit') }}
        </button>
    </li>
    @endif
    @if($hasWithdraws)
    <li class="nav-item" role="presentation">
        <button class="nav-link tab-btn {{ !$hasDeposits ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#tab-withdraw" type="button" role="tab">
            {{ __('panel.enums.withdraw') }}
        </button>
    </li>
    @endif
    @if($hasRequests)
    <li class="nav-item" role="presentation">
        <button class="nav-link tab-btn {{ !$hasDeposits && !$hasWithdraws ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#tab-requests" type="button" role="tab">
            {{ __('panel.enums.withdraw_request') }}
        </button>
    </li>
    @endif
</ul>

<div class="tab-content custom-tab-wrapper">
    {{-- Deposit tab --}}
    @if($hasDeposits)
    <div class="tab-pane fade {{ $hasDeposits ? 'show active' : '' }}" id="tab-deposit" role="tabpanel">
        @foreach($depositTransactions as $index => $transaction)
        <div class="bg-white p-3 rounded-4 mb-3 d-flex justify-content-between align-items-center shadow-sm flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge badge-number">#{{ $transaction->id }}</span>
                <span class="badge badge-number">#{{ $transaction->uuid ?? $transaction->id }}</span>
                <span class="badge badge-number">{{ number_format((float) $transaction->amount, 2) }} <i class="icon-saudi_riyal"></i></span>
            </div>
            <div class="fw-bold text-dark">
                {{ __('panel.enums.deposit') }}
            </div>
        </div>
        @endforeach
        @if($depositTransactions->hasPages())
        <div class="mt-4">{{ $depositTransactions->links('vendor.pagination.categories') }}</div>
        @endif
    </div>
    @endif

    {{-- Withdraw tab --}}
    @if($hasWithdraws)
    <div class="tab-pane fade {{ !$hasDeposits && $hasWithdraws ? 'show active' : '' }}" id="tab-withdraw" role="tabpanel">
        @foreach($withdrawTransactions as $transaction)
        <div class="bg-white p-3 rounded-4 mb-3 d-flex justify-content-between align-items-center shadow-sm flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge badge-number">#{{ $transaction->id }}</span>
                <span class="badge badge-number">#{{ $transaction->uuid ?? $transaction->id }}</span>
                <span class="badge badge-number">{{ number_format((float) $transaction->amount, 2) }} <i class="icon-saudi_riyal"></i></span>
            </div>
            <div class="fw-bold text-dark">
                {{ __('panel.enums.withdraw') }}
            </div>
        </div>
        @endforeach
        @if($withdrawTransactions->hasPages())
        <div class="mt-4">{{ $withdrawTransactions->links('vendor.pagination.categories') }}</div>
        @endif
    </div>
    @endif

    {{-- Withdrawal requests tab --}}
    @if($hasRequests)
    <div class="tab-pane fade {{ !$hasDeposits && !$hasWithdraws && $hasRequests ? 'show active' : '' }}" id="tab-requests" role="tabpanel">
        @foreach($withdrawalRequests as $request)
        <div class="bg-white p-3 rounded-4 mb-3 d-flex justify-content-between align-items-center shadow-sm flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge badge-number">#{{ $request->id }}</span>
                <span class="badge badge-number">#{{ $request->id }}</span>
            </div>
            <div class="fw-bold text-muted d-flex align-items-center gap-2">
                <span class="text-dark">{{ __('forms.fields.amount') }}:</span>
                {{ number_format((float) $request->amount, 2) }} <i class="icon-saudi_riyal"></i>
            </div>
            <div>
                @php
                    $statusLabel = $request->status?->getLabel() ?? $request->status;
                    $statusColor = match($request->status?->value ?? '') {
                        'pending', 'waiting_transfer' => 'warning',
                        'transferred' => 'success',
                        'rejected' => 'danger',
                        default => 'secondary',
                    };
                @endphp
                <span class="badge bg-{{ $statusColor }} text-dark px-3 py-2 rounded-pill">{{ $statusLabel }}</span>
            </div>
        </div>
        @endforeach
        @if($withdrawalRequests->hasPages())
        <div class="mt-4">{{ $withdrawalRequests->links('vendor.pagination.categories') }}</div>
        @endif
    </div>
    @endif
</div>
@endif

@livewire('site.withdraw-modal')
