<?php

namespace App\UsersModule\Resources\CustomerResource\Pages;

use Illuminate\Database\Eloquent\Model;
use Filament\Actions\Action;
use App\UsersModule\Resources\CustomerResource;
use App\UsersModule\Resources\CustomerResource\Widgets\WalletStats;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Concerns\HasTabs;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Theamostafa\Wallet\Models\Transaction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
class WalletPage extends Page implements HasTable {
    use InteractsWithRecord;
    use HasTabs;
    use InteractsWithTable;

//    public ?array $tableFilters = null;
    public string|int|null|Model $record;


    protected string $view = 'filament-panels::resources.pages.list-records';

    protected static string $resource = CustomerResource::class;

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
                    ->schema([
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
                    ->visible(fn() => $this->record->wallet->balance > 0)
                    ->label(__('panel.enums.withdraw'))
                    ->schema([
                        TextInput::make('amount')
                            // ->rules(['lte:' . $this->record->wallet->balance])
                            ->label(__('forms.fields.amount'))
                            ->required(),
                        TextInput::make('description.ar')
                            ->label(__("forms.fields.reason_ar"))->required(),
                        TextInput::make('description.en')->label(__("forms.fields.reason_en"))
                            ->required(),
                        SpatieMediaLibraryFileUpload::make('receipt')
                            ->columnSpan(2),
                    ])->action(function ($data) {
                        $this->record->withdraw(amount: $data['amount'], meta: [

                            'description' => $data['description']
                        ]);

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

        return Transaction::whereHas('wallet', fn($query) => $query
            ->where('holder_id', $this->record?->id)
            ->where('holder_type', $this->record?->getMorphClass())
        );
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
            WalletStats::make(['record' => $this->record])
        ];
    }
}
