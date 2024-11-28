<?php

namespace App\ProviderPanel\Filament\Resources;

use App\DefaultPanel\Traits\Filament\HasTranslationLabel;

use App\ProviderPanel\Filament\Resources\WalletResource\Pages\ListWalletTransactions;
use App\ProviderPanel\Filament\Resources\WalletResource\Widgets\WalletSummary;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Theamostafa\Wallet\Models\Transaction;


class WalletResource extends Resource {
    use HasTranslationLabel, Translatable;

    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form {
        return $form;
    }

    public static function table(Table $table): Table {

        return $table
            ->modifyQueryUsing(fn($query) => $query->whereHas('wallet', fn($query) => $query->where('holder_id', provider()->id)))
            ->filters([
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
            ->columns([
                TextColumn::make('id')
                    ->translateLabel()
                    ->searchable(),

                TextColumn::make('type')
                    ->label(__('forms.fields.transaction_type'))
                    ->formatStateUsing(fn($state) => __("panel.enums.$state"))
                    ->searchable(),
                TextColumn::make('amount')->searchable(),
                TextColumn::make('meta.description')
                    ->formatStateUsing(fn($record) => $record->meta['description'][app()->getLocale()]),
                TextColumn::make('created_at')
                    ->date()
            ])->striped();
    }


    public static function getRelations(): array {
        return [
        ];
    }

    public static function getPages(): array {
        return [
            'index' => ListWalletTransactions::route('/'),

        ];
    }


    public static function getGlobalSearchResultTitle(Model $record): string {
        return $record->name;
    }


    public static function can(string $action, ?Model $record = null): bool {
        return true;
    }

    public static function getNavigationLabel(): string {
        return __('menu.wallet');
    }

    public static function getPluralLabel(): ?string {
        return __('menu.wallet');
    }

    public static function getWidgets(): array {
        return [
            WalletSummary::make(),
        ];
    }
}
