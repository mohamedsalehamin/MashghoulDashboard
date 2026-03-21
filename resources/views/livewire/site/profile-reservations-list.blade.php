<div>
<div class="filter-by d-flex justify-content-end mb-4">
    <select class="form-select custom-select w-auto" wire:model.live="status" wire:key="status-filter">
        <option value="">{{ __('site.heading.reservations_sort_label_by_status') }}</option>
        <option value="pending">{{ __('panel.enums.pending') }}</option>
        <option value="processing">{{ __('panel.enums.processing') }}</option>
        <option value="completed">{{ __('panel.enums.completed') }}</option>
        <option value="canceled">{{ __('panel.enums.canceled') }}</option>
        <option value="not_performed">{{ __('panel.enums.not_performed') }}</option>
    </select>
    <select class="form-select custom-select w-auto ms-2" wire:model.live="sort" wire:key="sort-filter">
        <option value="">{{ __('site.heading.reservations_sort_label_by_date') }}</option>
        <option value="newest">{{ __('site.heading.reservations_sort_newest') }}</option>
        <option value="oldest">{{ __('site.heading.reservations_sort_oldest') }}</option>
    </select>
</div>
<div class="my-bookings-wrapper">
    
    @if($reservations->isEmpty())
        <p class="text-muted text-center py-5">{{ __('site.no_data') }}</p>
    @else
        @foreach($reservations as $r)
        <div class="booking-card justify-content-between">
            <div class="booking-col-right">
                <div class="booking-meta-badges">
                    <span class="gray-badge">#{{ $reservations->firstItem() + $loop->index }}</span>
                    <span class="gray-badge">#{{ $r->reservation_number }}</span>
                    <span class="gray-badge">{{ $r->reservable?->getTranslation('name', app()->getLocale()) ?? $r->reservable?->name ?? '—' }}</span>
                    <span class="gray-badge">{{ $r->date ? \Carbon\Carbon::parse($r->date)->format('Y/n/j') : '—' }} - {{ $r->from ? \Carbon\Carbon::parse($r->from)->format('H:i') : '—' }}</span>
                    @if($r->reservable?->city)
                    <span class="gray-badge">{{ $r->reservable->city->getTranslation('name', app()->getLocale()) ?? $r->reservable->city->name ?? '—' }}</span>
                    @endif
                </div>
            </div>
            <div class="booking-col-center">
                <div class="booking-price">
                    @php
                        $priceVal = $r->price instanceof \Cknow\Money\Money ? $r->price->formatByDecimal() : $r->price;
                    @endphp
                    {{ number_format((float) $priceVal, 0) }} <i class="icon-saudi_riyal"></i>
                </div>
            </div>
            <div class="booking-col-left">
                @php
                    $statusClass = match($r->status?->value ?? '') {
                        'pending' => 'pending',
                        'processing' => 'processing',
                        'completed' => 'completed',
                        'canceled', 'not_performed' => 'canceled',
                        default => 'pending',
                    };
                @endphp
                <span class="status-badge {{ $statusClass }}">{{ $r->status?->getLabel() ?? $r->status }}</span>
                <a href="{{ route('site.booking.show', $r->id) }}" class="view-details-link">{{ __('site.buttons.view_booking_details') }}</a>
            </div>
        </div>
        @endforeach

        @if($reservations->hasPages())
        <div class="mt-4">{{ $reservations->links('vendor.pagination.categories') }}</div>
        @endif
    @endif
</div>
</div>
