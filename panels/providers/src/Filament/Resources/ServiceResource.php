<?php

namespace App\ProviderPanel\Filament\Resources;

use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;
use App\CatalogModule\Models\Service;
use App\CatalogModule\Resources\ServiceResource\RelationManagers\ProductsRelationManager;
use App\DefaultPanel\Settings\GeneralSettings;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\ProviderPanel\Filament\Resources\ServiceResource\Pages\CreateService;
use App\ProviderPanel\Filament\Resources\ServiceResource\Pages\EditService;
use App\ProviderPanel\Filament\Resources\ServiceResource\Pages\ListServices;
use App\ProviderPanel\Filament\Resources\ServiceResource\Pages\ViewService;
use App\UsersModule\Models\Provider;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class ServiceResource extends Resource {
    use HasTranslationLabel, Translatable;

    protected static ?string $model = Service::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema {
        return $schema
            ->components([
                Section::make('basic_information')->schema([

                    Hidden::make('provider_id')->default(\provider()->id),
                    TextInput::make('title')
                        ->label(__('forms.fields.service_name'))
                        ->required(),
                    Textarea::make('description')
                        ->rows(10)
                        ->required(),
                    SpatieMediaLibraryFileUpload::make('avatar')
                        ->nullable(),
                    Select::make('duration')
                        ->options(GeneralSettings::getDurations())
                        ->required()
                        ->suffix(__('forms.suffixes.minutes')),
                    TextInput::make('price')
                        ->suffix(__("forms.suffixes.sar"))
                        ->formatStateUsing(fn($record) => $record ? $record->price?->formatByDecimal() : null)
                        ->required(),
                    TextInput::make('sale_price')
                        ->suffix(__("forms.suffixes.sar"))
                        ->formatStateUsing(fn($record) => $record ? $record?->sale_price?->formatByDecimal() : null)
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(fn($get) => $get('price')),


                    Toggle::make('status')->default(1)
                        ->onColor('success')
                        ->offColor('danger')
                ])->columnSpan(2),
                Section::make('products')->schema([
                    Repeater::make('products')
                        ->label('')
                        ->defaultItems(0)
                        ->addActionLabel(__('panel.actions.add'))
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('avatar')
                                ->nullable(),
                            TextInput::make('title.ar')
                                ->formatStateUsing(fn($record) => $record?->title['ar'] ?? '')
                                ->label(__("forms.fields.title_ar"))->required(),
                            TextInput::make('title.en')->label(__("forms.fields.title_en"))
                                ->formatStateUsing(fn($record) => $record?->title['en'] ?? '')
                                ->required(),
                            TextInput::make('price')
                        ->suffix(__("forms.suffixes.sar"))
                        ->formatStateUsing(fn($record) => $record ? $record->price?->formatByDecimal() : null)
                        ->required(),
                    TextInput::make('sale_price')
                        ->suffix(__("forms.suffixes.sar"))
                        ->formatStateUsing(fn($record) => $record ? $record?->sale_price?->formatByDecimal() : null)
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(fn($get) => $get('price')),


                        ])->relationship('products'),
                ])->columnSpan(1),

            ])->columns(3);
    }

    public static function table(Table $table): Table {
        return $table
            ->modifyQueryUsing(fn($query) => $query->where('provider_id', provider()->id))
            ->columns([
                TextColumn::make('index')->rowIndex(),
                TextColumn::make('id')->translateLabel()->searchable(),

                TextColumn::make('title')->searchable(),
                TextColumn::make('price')->searchable(),
                TextColumn::make('sale_price')
                    ->money('SAR'),
                TextColumn::make('products_count')->counts("products")->searchable(false),


                IconColumn::make('status')
                    ->boolean()
                    ->action(
                        Action::make('status')
                            ->label(fn(Model $record): string => $record->status ? __('panel.messages.deactivate') : __('panel.messages.activate'))
//                            ->disabled(fn(Model $record): bool => !auth()->user()->can('update', $record))
                            ->requiresConfirmation()
                            ->action(fn(Model $record) => $record->toggleStatus())


                    ),

            ])
            ->filters([

            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // ExportBulkAction::make(),

                    DeleteBulkAction::make(),
                ]),
            ])
//            ->checkIfRecordIsSelectableUsing(fn(Model $record): bool => !$record->orders()->count())
            ->emptyStateActions([
                CreateAction::make(),
            ])
            ->striped();
    }

    static public function infolist(Schema $schema): Schema {
        return $schema
            ->components([
                TextEntry::make('id'),


                TextEntry::make('provider.name'),
                TextEntry::make('title'),
                TextEntry::make('description'),
                TextEntry::make('duration'),
                TextEntry::make('products_count')->state(fn($record) => $record->products()->count()),
                SpatieMediaLibraryImageEntry::make('image')->label(__('forms.fields.image')),
            ]);
    }

    public static function getRelations(): array {
        return [
            ProductsRelationManager::class
        ];
    }

    public static function getPages(): array {
        return [
            'index' => ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'edit' => EditService::route('/{record}/edit'),
            'view' => ViewService::route('/{record}'),
        ];
    }


    public static function getNavigationBadge(): ?string {
        return static::getModel()::where('provider_id', provider()->id)->count();
    }

    public static function getGlobalSearchResultTitle(Model $record): string {
        return $record->name;
    }


}
