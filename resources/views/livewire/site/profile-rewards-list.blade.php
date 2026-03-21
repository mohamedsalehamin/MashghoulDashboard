<div>
    {{-- Not enough points warning --}}
    @if(!$canExchange && $totalPoints > 0)
    <div class="alert alert-warning custom-alert d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="fa-solid fa-circle-info"></i>
        <div>{{ __('site.heading.not_enough_points') }}</div>
    </div>
    @endif

    {{-- Rewards card: show when can exchange --}}
    @if($canExchange && $bestOption)
    <div class="rewards-card mb-4">
        <div class="rewards-card-content">
            <div class="tier-name">{{ $bestOption->title }}</div>
            <div class="points-info">
                <div class="points-display">{{ number_format($totalPoints) }}</div>
                <div class="points-value">{{ __('site.heading.exchanged_value') }} <i class="icon-saudi_riyal"></i> {{ $bestOption->price instanceof \Cknow\Money\Money ? number_format((float) $bestOption->price->formatByDecimal(), 0) : number_format((float) $bestOption->price, 0) }}</div>
            </div>
            <button class="btn btn-green redeem-btn" type="button" data-bs-toggle="modal" data-bs-target="#redeem-modal">
                {{ __('site.heading.redeem_points') }}
            </button>
        </div>
    </div>
    @endif

    {{-- Empty state when no data at all --}}
    @if(!$hasEarned && !$hasExchanges && !$hasUsages)
    <div class="alert alert-light text-center py-5">
        {{ __('site.heading.rewards_text') }}
    </div>
    @else
    {{-- Tabs: only show tabs that have data --}}
    <ul class="rewards-tabs list-unstyled nav nav-tabs border-0" role="tablist">
        @if($hasEarned)
        <li class="nav-item" role="presentation">
            <button class="nav-link tab-btn {{ $firstActiveTab === 'earned' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#tab-earned" type="button" role="tab">
                {{ __('site.heading.earned_points') }}
            </button>
        </li>
        @endif
        @if($hasExchanges)
        <li class="nav-item" role="presentation">
            <button class="nav-link tab-btn {{ $firstActiveTab === 'exchange' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#tab-redeem" type="button" role="tab">
                {{ __('site.heading.redeem_points') }}
            </button>
        </li>
        @endif
        @if($hasUsages)
        <li class="nav-item" role="presentation">
            <button class="nav-link tab-btn {{ $firstActiveTab === 'usage' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#tab-used" type="button" role="tab">
                {{ __('site.heading.points_usage') }}
            </button>
        </li>
        @endif
    </ul>

    <div class="tab-content custom-tab-wrapper">
        @if($hasEarned)
        <div class="tab-pane fade {{ $firstActiveTab === 'earned' ? 'show active' : '' }}" id="tab-earned" role="tabpanel">
            <div class="rewards-history">
                @foreach($winningPoints as $index => $point)
                <div class="reward-item">
                    <div class="reward-item-header">
                        <span class="badge badge-number">#{{ $winningPoints->firstItem() + $index }}</span>
                        <span class="badge badge-number">#{{ $point->id }}</span>
                        @if(!empty($point->meta_data['provider_name'] ?? null))
                        <span class="badge badge-location">{{ $point->meta_data['provider_name'] }}</span>
                        @endif
                        @if(!empty($point->meta_data['city'] ?? null))
                        <span class="badge badge-status">{{ $point->meta_data['city'] }}</span>
                        @endif
                    </div>
                    <div class="reward-item-footer">
                        <div class="reward-description">{{ $point->meta_data['description'][app()->getLocale()] ?? $point->meta_data['description']['ar'] ?? '-' }}</div>
                        <div class="badge reward-date">{{ $point->created_at->format('Y/n/j - H:i') }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @if($winningPoints->hasPages())
            <div class="mt-4">{{ $winningPoints->links('vendor.pagination.categories') }}</div>
            @endif
        </div>
        @endif

        @if($hasExchanges)
        <div class="tab-pane fade {{ $firstActiveTab === 'exchange' ? 'show active' : '' }}" id="tab-redeem" role="tabpanel">
            <div class="rewards-history">
                @foreach($exchanges as $index => $exchange)
                <div class="reward-item">
                    <div class="reward-item-header">
                        <span class="badge badge-number">#{{ $exchanges->firstItem() + $index }}</span>
                        <span class="badge badge-number">#{{ $exchange->id }}</span>
                        <span class="badge badge-number">{{ $exchange->expired_at?->format('Y/n/j - H:i') ?? '-' }}</span>
                    </div>
                    <div class="reward-details">
                        <div class="detail-row">
                            <div class="detail-label">{{ __('site.heading.tier') }}</div>
                            <div class="detail-value">{{ $exchange->plan?->title ?? '-' }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">{{ __('site.heading.points_value') }}</div>
                            <div class="detail-value">{{ $exchange->points ?? $exchange->value ?? '-' }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">{{ __('site.heading.exchanged_value') }}</div>
                            <div class="detail-value">{{ $exchange->price instanceof \Cknow\Money\Money ? number_format((float) $exchange->price->formatByDecimal(), 0) : number_format((float) $exchange->price, 0) }} <i class="icon-saudi_riyal"></i></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">{{ __('site.heading.expiry_date') }}</div>
                            <div class="detail-value">{{ $exchange->expired_at?->format('Y/n/j') ?? '-' }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @if($exchanges->hasPages())
            <div class="mt-4">{{ $exchanges->links('vendor.pagination.categories') }}</div>
            @endif
        </div>
        @endif

        @if($hasUsages)
        <div class="tab-pane fade {{ $firstActiveTab === 'usage' ? 'show active' : '' }}" id="tab-used" role="tabpanel">
            <div class="rewards-history">
                @foreach($usages as $index => $usage)
                <div class="reward-item">
                    <div class="reward-item-header">
                        <span class="badge badge-number">#{{ $usages->firstItem() + $index }}</span>
                        <span class="badge badge-number">#{{ $usage->id }}</span>
                        <span class="badge badge-number">{{ $usage->created_at->format('Y/n/j - H:i') }}</span>
                    </div>
                    <div class="reward-details">
                        <div class="detail-row">
                            <div class="detail-label">{{ __('site.heading.points_used') }}</div>
                            <div class="detail-value">{{ $usage->price ?? '-' }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">{{ __('site.heading.reservation_number') }}</div>
                            <div class="detail-value">#{{ $usage->reservation?->reservation_number ?? $usage->reservation_id ?? '-' }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @if($usages->hasPages())
            <div class="mt-4">{{ $usages->links('vendor.pagination.categories') }}</div>
            @endif
        </div>
        @endif
    </div>
    @endif
</div>

@livewire('site.redeem-modal')
