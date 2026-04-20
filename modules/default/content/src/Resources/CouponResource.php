<?php

namespace App\ContentModule\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Hidden;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\ContentModule\Resources\CouponResource\Pages\ListCoupons;
use App\ContentModule\Resources\CouponResource\Pages\CreateCoupon;
use App\ContentModule\Resources\CouponResource\Pages\EditCoupon;
use App\ContentModule\Resources\CouponResource\Pages\ViewCoupon;
use App\ContentModule\Models\Coupon;
use App\ContentModule\Resources;
use App\ContentModule\Resources\CouponResource\RelationManagers\ServicesRelationManager;
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
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Support\Enums\Width;
use Filament\Tables;
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
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tag';

    public static function form(Schema $schema): Schema {
        return $schema
            ->components([
                Section::make('general')->schema([
                    Hidden::make('name')->default('name'),

                    TextInput::make('code')
                        ->label(__('forms.fields.coupon_code'))
                        ->required(),

                    Select::make('scope')
                        ->label(__('forms.fields.coupon_scope'))
                        ->options([
                            Coupon::SCOPE_GENERAL => __('forms.fields.coupon_scope_general'),
                            Coupon::SCOPE_PROVIDERS => __('forms.fields.coupon_scope_providers'),
                        ])
                        ->default(Coupon::SCOPE_GENERAL)
                        ->live(),

                    Select::make('requested_by')
                        ->label(__('forms.fields.coupon_requested_by'))
                        ->options([
                            Coupon::REQUESTED_BY_ADMIN => __('forms.fields.coupon_requested_by_admin'),
                            Coupon::REQUESTED_BY_PROVIDER => __('forms.fields.coupon_requested_by_provider'),
                        ])
                        ->default(Coupon::REQUESTED_BY_ADMIN)
                        ->live(),

                    Select::make('provider_id')
                        ->label(__('forms.fields.provider'))
                        ->searchable()
                        ->preload()
                        ->options(fn () => Provider::query()->pluck('name', 'id'))
                        ->visible(fn ($get) => $get('requested_by') === Coupon::REQUESTED_BY_PROVIDER)
                        ->required(fn ($get) => $get('requested_by') === Coupon::REQUESTED_BY_PROVIDER),

                    Select::make('apply_target')
                        ->label(__('forms.fields.coupon_apply_target'))
                        ->options([
                            Coupon::APPLY_TARGET_ALL_ITEMS => __('forms.fields.coupon_apply_target_all_items'),
                            Coupon::APPLY_TARGET_ALL_ITEMS_WITHOUT_DISCOUNT => __('forms.fields.coupon_apply_target_all_items_without_discount'),
                            Coupon::APPLY_TARGET_SERVICES_ONLY => __('forms.fields.coupon_apply_target_services_only'),
                            Coupon::APPLY_TARGET_SERVICES_WITHOUT_DISCOUNT => __('forms.fields.coupon_apply_target_services_without_discount'),
                            Coupon::APPLY_TARGET_PRODUCTS_ONLY => __('forms.fields.coupon_apply_target_products_only'),
                            Coupon::APPLY_TARGET_PRODUCTS_WITHOUT_DISCOUNT => __('forms.fields.coupon_apply_target_products_without_discount'),
                        ])
                        ->visible(fn ($get) => $get('requested_by') === Coupon::REQUESTED_BY_PROVIDER)
                        ->required(fn ($get) => $get('requested_by') === Coupon::REQUESTED_BY_PROVIDER),

                    Select::make('discount_type')
                        ->live()
                        ->options(CouponTypes::class)
                        ->default(CouponTypes::FIXED),

                    TextInput::make('discount_value')
                        ->numeric()
                        ->required()
                         ->rules([
                            fn($get) => $get('discount_type') == CouponTypes::FIXED ? 'max:' . $get('meta_data.min_order_value') : '',
                            fn($get) => $get('discount_type') == CouponTypes::PERCENTAGE ? 'max:100' : '',
                        ]),
                    DatePicker::make('start_date')
                        ->date()
                        ->rule(fn($operation) => $operation == 'create' ? 'after_or_equal:' . today()->format('Y-m-d') : ['required'])
                        ->required(),

                    DatePicker::make('end_date')
                        ->rules(['after:start_date'])
                        ->required(),

                    TextInput::make('usages')
                        ->numeric()
                        ->minLength(1)
                        ->required(),

                    TextInput::make('usage_per_user')
                        ->numeric()
                        ->required()
                        ->default(1),
                    TextInput::make('meta_data.min_order_value')
                        ->label(__('forms.fields.min_order_value'))
                        ->numeric()
                        ->required()
                        ->default(1),

                    Select::make('meta_data.min_order_value_type')
                        ->label(__('forms.fields.min_order_value_type'))
                        ->options([
                            'cart_total' => __('forms.fields.min_order_value_type_cart_total'),
                            'eligible_base' => __('forms.fields.min_order_value_type_eligible_base'),
                        ])
                        ->default('cart_total')
                        ->live(),
                    TextInput::make('meta_data.max_discount')
                        ->label(__('forms.fields.max_discount'))
                        ->visible(fn($get) => $get('discount_type') == CouponTypes::PERCENTAGE)
                        ->numeric()
                        ->required()
                        ->default(1),
//
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
                    ->schema([
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
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema {
        return $schema->components([
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
            RelationGroup::make(__("sections.usages"),[
                UsersRelationManager::class,
            ]),
            // RelationGroup::make(__("sections.providers"),[
            //     ServicesRelationManager::class,
            // ]),

        ];
    }

    public static function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    public static function getPages(): array {
        return [
            'index' => ListCoupons::route('/'),
            'create' => CreateCoupon::route('/create'),
            'edit' => EditCoupon::route('/{record}/edit'),
            'view' => ViewCoupon::route('/{record}/view'),
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
