<?php

namespace App\UsersModule\Resources;

use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Components\Fieldset;
use App\UsersModule\Resources\WithdrawalRequestResource\Pages\ListWithdrawalRequests;
use App\UsersModule\Resources\WithdrawalRequestResource\Pages\ViewWithdrawalRequest;
use App\UsersModule\Models\WithdrawalRequest;
use App\UsersModule\Resources\WithdrawalRequestResource\Pages;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Model;
use App\DefaultPanel\Enum\WalletWithdrawEnum;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use LaraZeus\ActivityTimeline\Components\ActivityDate;
use LaraZeus\ActivityTimeline\Components\ActivityIcon;
use LaraZeus\ActivityTimeline\Components\ActivitySection;
use LaraZeus\ActivityTimeline\Components\ActivityTitle;
use App\DefaultPanel\Filters\DateFilter;
class WithdrawalRequestResource extends Resource
{
    use HasTranslationLabel;
    protected static ?string $model = WithdrawalRequest::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';



    protected static ?int $navigationSort = 5;



    public static function getNavigationGroup(): ?string
    {
        return __('menu.crew');
    }

    public static function getNavigationLabel(): string
    {
        return __('menu.withdrawal_requests');
    }

    

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', WalletWithdrawEnum::PENDING)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(false),
                TextColumn::make('user.name'),
                TextColumn::make('user.phone')
                    ->searchable(),
                TextColumn::make('amount')
                    ->money('SAR')
                    ->sortable(),
                TextColumn::make('transfer_amount')
                    ->money('SAR')
                    ->sortable(),
                
                TextColumn::make('status')
                ->color(fn($record) => $record?->status?->getColor())
                ->badge(),
                
            ])
            ->filters([
                DateFilter::make(),
                SelectFilter::make('status')
                    ->options(WalletWithdrawEnum::toArray())
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('accept')
                    ->icon('heroicon-o-check')
                    ->visible(fn($record) => $record->status == WalletWithdrawEnum::PENDING)
                    ->requiresConfirmation()
                    ->action(function (WithdrawalRequest $record) {
                        $record->update(['status' => WalletWithdrawEnum::WAITING_TRANSFER]);
                    })
                    ->label(__('forms.actions.accept')),
                Action::make('reject')
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn($record) => $record->status != WalletWithdrawEnum::TRANSFERRED  && $record->status != WalletWithdrawEnum::WAITING_TRANSFER && $record->status != WalletWithdrawEnum::REJECTED)
                    ->requiresConfirmation()
                    ->schema([
                        Textarea::make('rejection_reason.ar')
                            ->label(__("forms.fields.reason_ar"))
                            ->required()
                            ->rows(5)
                            ->maxLength(150)
                            ->minLength(10),
                        Textarea::make('rejection_reason.en')
                            ->label(__("forms.fields.reason_en"))
                            ->required()
                            ->rows(5)
                            ->maxLength(150)
                        ->minLength(10),
                        
                        
                    ])
                    ->action(function (WithdrawalRequest $record, array $data) {
                        $rejection_reason = $data['rejection_reason'];
                        $record->update([
                            'rejection_reason' => $rejection_reason,
                            'status' => WalletWithdrawEnum::REJECTED
                        ]);

                    })
                    ->label(__('forms.actions.reject')),
                Action::make('transfer')
                    ->icon('heroicon-o-currency-dollar')
                    ->visible(fn($record) => $record->status == WalletWithdrawEnum::WAITING_TRANSFER)
                    ->schema([
                        TextInput::make('transfer_amount')
                            ->live(onBlur: true)
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(fn ($record) => $record->user->wallet->balance)
                            ->required(),
                        
                        Textarea::make('notes.ar')
                            ->label(__("forms.fields.reason_ar"))
                            ->rows(5)
                            ->maxLength(150)
                            ->minLength(10),
                        Textarea::make('notes.en')
                            ->label(__("forms.fields.reason_en"))
                            ->rows(5)
                            ->maxLength(150)
                        ->minLength(10),
                        SpatieMediaLibraryFileUpload::make('receipt')
                    ])
                    ->modalSubmitActionLabel(__('forms.actions.transfer'))
                    ->action(function (WithdrawalRequest $record, array $data) {
                        // Get amount from form data
                        $transfer_amount = $data['transfer_amount'];
                        $notes = $data['notes'];
                        
                        $record->user->withdraw(
                            amount: $transfer_amount,
                            meta: [
                                'description' => $notes ?? 
                                [
                                    'ar' => __('panel.messages.admin_transfer_lab_commission', ['AMOUNT' => $amount, 'ID' => $record->id], 'ar'),
                                    'en' => __('panel.messages.admin_transfer_lab_commission', ['AMOUNT' => $amount, 'ID' => $record->id], 'en'),
                                ],
                            ]
                        );
                        $record->update([
                            'status' => WalletWithdrawEnum::TRANSFERRED->value,
                            'transfer_amount' => $transfer_amount,
                            'notes' => $notes ??  [
                                'ar' => __('panel.messages.admin_transfer_lab_commission', ['AMOUNT' => $amount, 'ID' => $record->id], 'ar'),
                                'en' => __('panel.messages.admin_transfer_lab_commission', ['AMOUNT' => $amount, 'ID' => $record->id], 'en'),
                            ]
                        ]);

                        // Handle receipt file upload if present
                        if (isset($data['receipt'])) {
                            $record->addMedia($data['receipt'])->toMediaCollection('receipt');
                        }
                    })
                    ->label(__('forms.actions.transfer'))
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
    static public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id'),
                TextEntry::make('created_at'),
                TextEntry::make('user.name')->label(__('forms.fields.name')),
                TextEntry::make('user.phone')->label(__('forms.fields.phone')),
                TextEntry::make('amount'),
                TextEntry::make('status')
                ->color(fn($record) => $record?->status?->getColor())
                ->badge(),
                TextEntry::make('rejection_reason.' . app()->getLocale())
                ->label(__('forms.fields.rejection_reason'))
                ->visible(fn($record) => $record->status == WalletWithdrawEnum::REJECTED),
                SpatieMediaLibraryImageEntry::make('receipt')
                ->label(__('forms.fields.receipt'))
                ->visible(fn($record) => $record->status == WalletWithdrawEnum::TRANSFERRED),
                Fieldset::make('Rate limiting')
                ->label(__('sections.bank_account_information'))
                ->relationship('bank_details')
                ->schema([
                    TextEntry::make('bank_name'),
                    TextEntry::make('account_name'),
                    TextEntry::make('account_number'),
                    TextEntry::make('iban')
                ]),
                ActivitySection::make('timeline')
                            ->label(__('sections.timeline'))
                            ->schema(components: [
                                ActivityTitle::make('title.' . app()->getLocale())
                                    ->getStateUsing(fn ($record) => $record->title[app()->getLocale()] . ' '.__('panel.messages.by').' ' . $record->changedBy->name),
                                ActivityDate::make('created_at')
                                    ->date('F j, Y h:i a'),
                                ActivityIcon::make('status')
                                    // ->icon(fn(string $state) => WalletWithdrawEnum::tryFrom($state)?->getIcon())
                                    ->color(fn(string|null $state): string|null => WalletWithdrawEnum::tryFrom($state)->getColor()),
                            ])
                            ->columnSpan(3)
            ])->columns(3);
    }
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWithdrawalRequests::route('/'),
            'view' => ViewWithdrawalRequest::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return Gate::check('create_withdrawal::request');
    }

    public static function canEdit(Model $record): bool
    {
        return Gate::check('update_withdrawal::request', $record);
    }

    public static function canDelete(Model $record): bool
    {
        return Gate::check('delete_withdrawal::request', $record);
    }

    public static function getMaxContentWidth(): Width
    {
        return Width::Full;
    }
}