<?php

namespace App\UsersModule\Resources\ProviderResource\Pages;

use App\LabPanel\Filament\Resources\WalletResource\Widgets\WalletSummary;
use App\Notifications\AdminSendEntitlementsNotification;
use App\UsersModule\Resources\DoctorResource;
use App\UsersModule\Resources\LabResource;
use App\UsersModule\Resources\ProviderResource;
use App\UsersModule\Resources\ProviderResource\Widgets\WalletStats;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Concerns\HasTabs;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Theamostafa\Wallet\Models\Transaction;
use Theamostafa\Wallet\Models\Wallet;

class WalletPage extends Page implements HasTable {
    use InteractsWithRecord;
    use HasTabs;
    use InteractsWithTable;

//    public ?array $tableFilters = null;
    public string|int|null|\Illuminate\Database\Eloquent\Model $record;


    protected static string $view = 'filament-panels::resources.pages.list-records';

    protected static string $resource = ProviderResource::class;

    public function mount(int|string $record): void {
        $this->record = $this->resolveRecord($record);
    }

    public function table(Table $table): Table {
        return $table->query($this->getTableQuery())
            ->emptyStateHeading(__("site.no_data"))
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'deposit' => __('panel.enums.deposit'),
                        'withdraw' => __('panel.enums.withdraw'),
                    ]),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('date_from'),
                        DatePicker::make('date_to'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['date_to'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                Action::make('pay')
                    ->visible(fn() => $this->record->provider?->wallet?->balance > 0)
                    ->label(__('forms.actions.reduction'))
                    ->model($this->record->provider?->wallet)
                    ->record($this->record->provider)
                    ->form([
                        TextInput::make('amount')
                            ->rules(['lte:' . $this->record->provider?->wallet?->balance])
                            ->label(__('forms.fields.amount'))
                            ->required(),
                        SpatieMediaLibraryFileUpload::make('receipt')
                            ->afterStateUpdated(fn($get, $set) => $set('image', $get('receipt')))
                            ->columnSpan(2),
                        Hidden::make('image')
                    ])
                    ->action(function ($data) {
                        $operation = $this->record->provider->withdraw(amount: $data['amount'], meta: [

                            'description' => [
                                'ar' => __('panel.messages.admin_withdraw_balance_from_wallet_text', ['AMOUNT' => $data['amount']], 'ar'),
                                'en' => __('forms.fields.admin_withdraw_balance_from_wallet_text', ['AMOUNT' => $data['amount']], 'en')
                            ]
                        ]);
                        $operation->addMedia(array_values($data['image'])[0])->toMediaCollection();
                    })
            ])
            ->columns([
                TextColumn::make('id')->translateLabel()
                    ->searchable(false),

                TextColumn::make('type')
                    ->label(__('forms.fields.transaction_type'))
                    ->formatStateUsing(fn($state) => __("panel.enums.$state"))
                    ->searchable(false),
                TextColumn::make('amount')->searchable(false),
                TextColumn::make('meta.description')
                    ->formatStateUsing(fn($record) => $record->meta['description'][app()->getLocale()])
                    ->searchable(false),
                TextColumn::make('created_at')
                    ->date()
                    ->searchable(false)
            ])
            ->actions([
                Action::make('receipt')
                    ->label(__("forms.actions.show_receipt"))
                    ->visible(fn($record) => $record->getFirstMediaUrl())
                    ->url(fn($record) => $record->getFirstMediaUrl(), true)
            ])
            ->striped();

    }

//    protected function getHeaderWidgets(): array {
//        return [
//            LabResource\Widgets\WalletSummary::make(['record' => $this->record->lab])
//        ];
//    }
//
//    protected function getWidgets(): array {
//        return [
//            LabResource\Widgets\WalletSummary::make(['record' => $this->record->lab])
//        ];
//    }


    protected function getTableQuery(): ?Builder {

        return Transaction::whereHas('wallet', fn($query) => $query->where('holder_id', $this->record?->provider?->id));
    }

    public function getHeading(): string|Htmlable {
        return __('menu.wallet');
    }

    /**
     * @return string|null
     */
    public function getBreadcrumbs(): array {
        return [
            __('menu.dashboard'),
            $this->record?->provider?->title,
            __('menu.wallet')
        ];
    }

    protected function getHeaderWidgets(): array {
        return [
            WalletStats::make(['record' => $this->record->provider])
        ];
    }
}
