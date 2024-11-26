<div class="account-body">
    <h2 class="account-title">@lang('site.heading.transactions')</h2>
    <div class="accout-transactions">
        <div class="nav account-tabs">
            <button
                type="button"
                class="active"
                data-bs-toggle="tab"
                data-bs-target="#tab-1"
            >
                @lang('site.heading.payments')
            </button>
            <button
                type="button"
                data-bs-toggle="tab"
                data-bs-target="#tab-2"
            >
                @lang('site.heading.refunds')
            </button>
        </div>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-1">
                <div class="payments-tab">
                    <div class="table-filters">
                        <div class="search-input">
                          <span class="search-icon">
                            <i class="fa-regular fa-magnifying-glass"></i>
                          </span>
                            <input
                                type="search"
                                placeholder="@lang('site.fields.search_by_transaction_number')"
                                wire:model.live.debounce="filters.q"
                            />

                        </div>
                        <div class="search-dates">
                            <div class="search-date">
                                <label class="date-label">@lang('site.fields.date_from')</label>
                                <livewire:flat-picker-date-input
                                    :inputOnly="true"
                                    wire:model.live="filters.date_from"

                                />
                            </div>
                            <div class="search-date">
                                <label class="date-label">@lang('site.fields.date_to')</label>
                                <livewire:flat-picker-date-input
                                    :inputOnly="true"
                                    wire:model.live="filters.date_to"

                                />
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table transactions-table">
                            <tr>
                                <th colspan="1">#</th>
                                <th colspan="3">@lang('site.fields.id')</th>
                                <th colspan="3">@lang('site.fields.reservation_number')</th>
                                <th colspan="4">@lang('site.heading.paid_at')</th>
                                <th colspan="3">@lang('site.fields.price')</th>
                                <th colspan="3">@lang('site.heading.payment_method')</th>
                                <th colspan="2">@lang('site.heading.invoice')</th>
                            </tr>
                            @foreach($payments as $transaction)
                                <tr>
                                    <td colspan="1">{{$loop->iteration}}</td>
                                    <td colspan="3">{{$transaction->id}}</td>
                                    <td colspan="3">{{$transaction?->transactionable?->reservation_number}}</td>
                                    {{--                                    <td colspan="3">12087</td>--}}
                                    <td colspan="4">{{$transaction->created_at->translatedFormat("d M Y h:i a")}}</td>
                                    <td colspan="3">{{$transaction->price}}</td>
                                    <td colspan="3">
                                        <div class="method">
                                            <span class="text"> @lang('site.heading.mastercard') </span>
                                            <span class="img">
                                  <img src="images/payments/4.png" alt="@lang('site.heading.mastercard')"
                                  />
                                </span>
                                        </div>
                                    </td>
                                    <td colspan="2">
                                        <a
                                            href="{{route('reservations.invoice',$transaction->transactionable->id)}}"
                                            target="_blank"
                                            class="invoice-links"
                                        >
                                            <i class="fa-regular fa-file-arrow-down"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tab-2">
                <div class="refunds-tab">
                    <div class="table-filters">
                        <div class="search-input">
                          <span class="search-icon">
                            <i class="fa-regular fa-magnifying-glass"></i>
                          </span>
                            <input
                                type="search"
                                placeholder="@lang('site.fields.search_by_transaction_number')"
                                wire:model.live.debounce="filters.q"

                            />

                        </div>
                        <div class="search-dates">
                            <div class="search-date">
                                <label class="date-label">@lang('site.fields.date_from')</label>
                                <livewire:flat-picker-date-input
                                    :inputOnly="true"
                                    wire:model.live="filters.date_from"

                                />
                            </div>
                            <div class="search-date">
                                <label class="date-label">@lang('site.fields.date_to')</label>
                                <livewire:flat-picker-date-input
                                    :inputOnly="true"
                                    wire:model.live="filters.date_to"

                                />
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table transactions-table">
                            <tr>
                                <th colspan="1">#</th>
                                <th colspan="3">@lang('site.fields.id')</th>
                                <th colspan="3">@lang('site.fields.reservation_number')</th>
                                <th colspan="4">@lang('site.heading.paid_at')</th>
                                <th colspan="3">@lang('site.fields.price')</th>
                                <th colspan="3">@lang('site.heading.payment_method')</th>
                                <th colspan="2">@lang('site.heading.invoice')</th>
                            </tr>
                            @foreach([] as $transaction)
                                <tr>
                                    <td colspan="1">{{$loop->iteration}}</td>
                                    <td colspan="3">{{$transaction->id}}</td>
                                    <td colspan="3">{{$transaction?->transactionable?->reservation_number}}</td>
                                    {{--                                    <td colspan="3">12087</td>--}}
                                    <td colspan="4">{{$transaction->created_at->translatedFormat("d M Y h:i a")}}</td>
                                    <td colspan="3">{{$transaction->price}}</td>
                                    <td colspan="3">
                                        <div class="method">
                                            <span class="text"> @lang('site.heading.mastercard') </span>
                                            <span class="img">
                                  <img src="images/payments/4.png" alt="@lang('site.heading.mastercard')"
                                  />
                                </span>
                                        </div>
                                    </td>
                                    <td colspan="2">
                                        <a
                                            href="{{route('reservations.invoice',$transaction->transactionable->id)}}"
                                            target="_blank"
                                            class="invoice-links"
                                        >
                                            <i class="fa-regular fa-file-arrow-down"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
