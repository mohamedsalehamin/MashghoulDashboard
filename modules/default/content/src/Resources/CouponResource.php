<?php

namespace App\ContentModule\Resources;

use App\ContentModule\Models\Coupon;
use App\ContentModule\Resources;
use App\ContentModule\Resources\CouponResource\RelationManagers\UsersRelationManager;
use App\DefaultPanel\Enum\CouponTypes;
use App\DefaultPanel\Enum\ModelStatus;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\UsersModule\Models\Provider;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CouponResource extends Resource implements HasShieldPermissions {
    use HasTranslationLabel;

    protected static ?int $navigationSort = 3;
    protected static ?string $model = Coupon::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\Section::make("general")->schema([
                    Forms\Components\Hidden::make('name')->default('name'),

                    TextInput::make('code')
                        ->label(__('forms.fields.coupon_code'))
                        ->required(),

                    Forms\Components\Select::make('discount_type')
                        ->options(CouponTypes::class)
                        ->default(CouponTypes::FIXED),

                    TextInput::make('discount_value')
                        ->numeric()
                        ->required(),
                    Forms\Components\DatePicker::make('start_date')
                        ->date()
                        ->rule(fn($operation) => $operation == 'create' ? 'after_or_equal:' . today()->format('Y-m-d') : ['required'])
                        ->required(),

                    Forms\Components\DatePicker::make('end_date')
                        ->rules(['after:start_date'])
                        ->required(),

                    TextInput::make('usages')
                        ->numeric()
                        ->minLength(1)
                        ->required(),

                    Forms\Components\TextInput::make('usage_per_user')
                        ->numeric()
                        ->required()
                        ->default(1),
                    Select::make('providers')
                        ->multiple()
                        ->label(__("forms.fields.provider_name"))
                        ->relationship('providers')
                        ->options(Provider::pluck("name", "id")),
                    Toggle::make('status')
                        ->default(1)
                        ->onColor('success')
                        ->offColor('danger')
                        ->translateLabel(),


                ]),
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                TextColumn::make('id'),

                TextColumn::make('code')->label(__("forms.fields.coupon_code")),
                TextColumn::make('discount_value'),
                TextColumn::make('usages'),
                TextColumn::make('used_times')
                    ->state(fn($record) => $record->users()->count()),
                TextColumn::make('usage_status')
                    ->default(fn($record) => $record->end_date > now()
                        ? __('panel.enums.running')
                        : __('panel.enums.exipred')),
                TextColumn::make('start_date')->date(),
                TextColumn::make('end_date')->date(),
                IconColumn::make('status')
                    ->boolean()
                    ->action(
                        Action::make('Active')
                            ->label(fn(Coupon $record): string => $record->status ? __('panel.messages.deactivate') : __('panel.messages.activate'))
//                            ->disabled(fn(Model $record): bool => !filament()->auth()->user()->can('update', $record))
                            ->requiresConfirmation()
                            ->action(fn(Coupon $record) => $record->toggleStatus())

                    ),

            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ModelStatus::class),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('start_date'),
                        DatePicker::make('end_date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date'] ?? '',
                                fn(Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['end_date'] ?? '',
                                fn(Builder $query, $date): Builder => $query->whereDate('end_date', '<=', $date),
                            );
                    })

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist {
        return $infolist->schema([
            Section::make('basic_data')->schema([
                TextEntry::make('id'),
                TextEntry::make('code')->label(__("forms.fields.coupon_code")),
                TextEntry::make('discount_value'),
                TextEntry::make('usages'),
                TextEntry::make('used_times')
                    ->state(fn($record) => $record->users()->count()),
                TextEntry::make('start_date')->date(),
                TextEntry::make('end_date')->date(),
                TextEntry::make('status')->state(fn($record) => $record->status ? __('panel.enums.ACTIVE') : __('panel.enums.INACTIVE')),
                TextEntry::make('usage_status')
                    ->state(fn($record) => $record->end_date > now()
                        ? __('panel.enums.running')
                        : __('panel.enums.exipred')),
            ])


        ]);
    }

    public static function getRelations(): array {
        return [
            UsersRelationManager::class
        ];
    }

    public static function getPages(): array {
        return [
            'index' => Resources\CouponResource\Pages\ListCoupons::route('/'),
            'create' => Resources\CouponResource\Pages\CreateCoupon::route('/create'),
            'edit' => Resources\CouponResource\Pages\EditCoupon::route('/{record}/edit'),
            'view' => Resources\CouponResource\Pages\ViewCoupon::route('/{record}/view'),
        ];
    }

    public static function getNavigationGroup(): ?string {
        return __('menu.content');
    }

    public static function getNavigationBadge(): ?string {
        return static::getModel()::count();
    }


    public static function getPermissionPrefixes(): array {
        return [
            'view_any',
            'view',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }
}
