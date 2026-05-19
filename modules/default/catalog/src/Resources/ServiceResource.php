<?php

namespace App\CatalogModule\Resources;

use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Livewire\Component;
use Filament\Actions\ImportAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\CreateAction;
use App\CatalogModule\Models\Product;
use App\CatalogModule\Models\Seat;
use App\CatalogModule\Models\Service;
use App\CatalogModule\Resources\ServiceResource\Pages\CreateService;
use App\CatalogModule\Resources\ServiceResource\Pages\EditService;
use App\CatalogModule\Resources\ServiceResource\Pages\ListServiceActivities;
use App\CatalogModule\Resources\ServiceResource\Pages\ListServices;
use App\CatalogModule\Resources\ServiceResource\Pages\ViewService;
use App\CatalogModule\Resources\ServiceResource\RelationManagers\ProductsRelationManager;
use App\DefaultPanel\Settings\GeneralSettings;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\Filament\Imports\ProductsImporter;
use App\Filament\Imports\ServicesImporter;
use App\UsersModule\Models\Provider;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Support\Enums\Width;
use Filament\Tables;
use pxlrbt\FilamentExcel\Columns\Column;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Illuminate\Database\Eloquent\Builder;

class ServiceResource extends Resource {
    use HasTranslationLabel, Translatable;

    protected static ?string $model = Service::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema {
        return $schema
            ->components([
                Section::make(__("sections.basic_information"))->schema([
                    Select::make('provider_id')
                    ->live()
                    ->label(__('forms.fields.provider_name'))
                    ->relationship(
                        'provider',
                        'name',
                        fn (Builder $query) => $query->latest('id'),
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
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
                        ->formatStateUsing(fn($record) => $record ? $record->price?->formatByDecimal() : 0)
                        ->required(),
                    TextInput::make('sale_price')
                        ->suffix(__("forms.suffixes.sar"))
                        ->formatStateUsing(fn($record) => $record ? $record?->sale_price?->formatByDecimal() : 0)
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(fn($get) => $get('price')),

                    Toggle::make('status')->default(1)
                        ->onColor('success')
                        ->offColor('danger')
                ])->columnSpan(2),
                Section::make(__('forms.sections.seat_assignments'))->schema([
                    Repeater::make('seat_assignments')
                        ->label(__('forms.sections.seat_assignments'))
                        ->defaultItems(0)
                        ->addActionLabel(__('panel.actions.add'))
                        ->schema([
                            Select::make('seat_id')
                                ->label(__('forms.fields.seat'))
                                ->options(fn($get) => Seat::where('provider_id', $get('../../provider_id'))->pluck('title', 'id'))
                                ->getOptionLabelFromRecordUsing(fn(Model $record): string => $record->title)
                                ->required()
                                ->searchable()
                                ->live(),
                            Select::make('service_group_id')
                                ->label(__('forms.fields.service_group'))
                                ->options(function ($get) {
                                    $seatId = $get('seat_id');
                                    if (!$seatId) {
                                        return collect();
                                    }
                                    return Seat::find($seatId)?->serviceGroups()->orderBy('sort')->orderBy('id')->get()->pluck('title', 'id') ?? collect();
                                })
                                ->getOptionLabelFromRecordUsing(fn(Model $record): string => $record->title)
                                ->searchable()
                                ->nullable(),
                        ]),
                ])->columnSpan(1)->collapsible(),
                Section::make(__("forms.sections.products"))->schema([
                    Repeater::make('products')
                        ->defaultItems(0)
                        ->addActionLabel(__('panel.actions.add'))
                        ->label(__("forms.sections.products"))
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('avatar')
                                ->nullable(),
                            TextInput::make('title.ar')
                                ->formatStateUsing(fn($record) => $record->title['ar'] ?? '')
                                ->label(__("forms.fields.title_ar"))->required(),
                            TextInput::make('title.en')->label(__("forms.fields.title_en"))
                                ->formatStateUsing(fn($record) => $record->title['en'] ?? '')
                                ->required(),
                             TextInput::make('price')->required()
                                ->numeric()
                                ->formatStateUsing(fn($record) => $record?->price?->formatByDecimal()),
                                TextInput::make('sale_price')
                            ->suffix(__("forms.suffixes.sar"))
                            ->formatStateUsing(fn($record) => $record ? $record?->sale_price?->formatByDecimal() : 0)
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(fn($get) => $get('price')),
                        ])
                        ->relationship('products'),
                ])
                
                ->columnSpan(1),

            ])->columns(3);
    }

    public static function table(Table $table): Table {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->translateLabel()
                    ->searchable(),
                TextColumn::make('provider.name')
                    ->label(__('forms.fields.provider_name'))
                    ->searchable(true, fn($query, $search) => $query->whereHas('provider', fn($q) => $q
                        ->where('name->ar', 'like', "%$search%")
                        ->orWhere('name->en', 'like', "%$search%")
                    )),
                TextColumn::make('title')->searchable(),
                TextColumn::make('price')->searchable(),
                TextColumn::make('sale_price')->money('SAR'),
                TextColumn::make('products_count')->counts("products")->searchable(false),
                TextColumn::make('created_at')
                    ->label(__('forms.fields.created_at'))
                    ->dateTime()
                    ->sortable(),

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
                TrashedFilter::make(),
                SelectFilter::make('provider_id')
                    ->options(fn() => Provider::pluck('name', 'id'))
                    ->label(__('forms.fields.provider_name'))
                    ->searchable(),
            ])
            ->recordActions([

                Action::make('activities')
                    ->label(__("forms.actions.activities"))
                    ->url(fn($record) => static::getUrl('activities', ['record' => $record])),

                RestoreAction::make(),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),

            ])
            ->headerActions([
                ExportAction::make("exportServices")
                    ->label(__('forms.fields.export_services'))
                    ->modalHeading('')
                    ->modalDescription('')
                    ->exports([

                        ExcelExport::make("export_services")
                            ->label(__("forms.fields.export_services"))
                            ->modifyQueryUsing(function ($query, Component $livewire) {
                                $filters = $livewire->getTable()->getFilters();

                                $provider_id = $filters['provider_id']->getState()['value'] ?? null;

                                return Service::query()->when($provider_id, fn($q) => $q->where('provider_id', $provider_id));
                            })
                            ->withColumns([
                                Column::make('id')->heading(__("forms.fields.db_row_id")),
                                Column::make('meta_data.import_id')->heading(__("forms.fields.id")),
                                Column::make('provider_id')->heading(__("forms.fields.provider_id")),
                                Column::make('title.ar')
                                    ->heading(__("forms.fields.title_ar"))
                                    ->getStateUsing(fn($record) => $record->price)
                                    ->formatStateUsing(fn($record) => $record->getOriginal('title')['ar'] ?? ''),

                                Column::make('title.en')
                                    ->heading(__("forms.fields.title_en"))
                                    ->getStateUsing(fn($record) => $record->price)
                                    ->formatStateUsing(fn($record) => $record->getOriginal('title')['en'] ?? ''),

                                Column::make('description.ar')
                                    ->heading(__("forms.fields.description_ar"))
                                    ->getStateUsing(fn($record) => $record->price)
                                    ->formatStateUsing(fn($record) => $record->getOriginal('description')['ar'] ?? ''),

                                Column::make('description.en')
                                    ->heading(__("forms.fields.description_en"))
                                    ->getStateUsing(fn($record) => $record->price)
                                    ->formatStateUsing(fn($record) => $record->getOriginal('description')['en'] ?? ''),

                                Column::make('duration')->heading(__("forms.fields.duration")),
                                Column::make('price')
                                    ->getStateUsing(fn($record) => $record->title)
                                    ->formatStateUsing(fn($record) => $record->price->formatByDecimal())
                                    ->heading(__("forms.fields.price")),
                                Column::make('sale_price')
                                    ->getStateUsing(fn($record) => $record->title)
                                    ->formatStateUsing(fn($record) => $record->sale_price->formatByDecimal())
                                    ->heading(__("forms.fields.sale_price")),
                                Column::make('image')
                                    ->heading(__("forms.fields.image"))
                                    ->getStateUsing(fn($record) => $record->title)
                                    ->formatStateUsing(fn($record) => url($record->getFirstMediaUrl())),
                            ])
                            ->withFilename(fn() => 'services-' . now()->format('Y-m-d')),


                    ]),

                ExportAction::make("exportProducts")
                    ->label(__('forms.fields.export_products'))
                    ->modalHeading('')
                    ->modalDescription('')

                    ->exports([

                        ExcelExport::make("export_products")
                            ->label(__("forms.fields.export_products"))

                            ->modifyQueryUsing(function ($query, Component $livewire) {
                                $filters = $livewire->getTable()->getFilters();
                                $provider_id = $filters['provider_id']->getState()['value'] ?? null;
                                return Product::query()->whereHas('service')->whereHas('service',fn($query2)=>$query2->when($provider_id, fn($q) => $q->where('provider_id', $provider_id)));
                            })

                            ->withColumns([
                                Column::make('id')->heading(__("forms.fields.db_row_id")),
                                Column::make('meta_data.import_id')->heading(__("forms.fields.id")),

                                Column::make('service_id')
                                    ->heading(__("forms.fields.service_id"))
                                    ->formatStateUsing(fn($record) => data_get(Service::find($record->service_id)?->meta_data, ['import_id'])),
                                Column::make('title.ar')
                                    ->heading(__("forms.fields.title_ar"))
                                    ->getStateUsing(fn($record) => $record->price)
                                    ->formatStateUsing(fn($record) => $record->getOriginal('title')['ar']),

                                Column::make('title.en')
                                    ->heading(__("forms.fields.title_en"))
                                    ->getStateUsing(fn($record) => $record->price)
                                    ->formatStateUsing(fn($record) => $record->getOriginal('title')['en']),

                                Column::make('price')
                                    ->formatStateUsing(fn($record) => $record->price->formatByDecimal())
                                    ->heading(__("forms.fields.price")),
                                Column::make('sale_price')
                                    ->formatStateUsing(fn($record) => $record->sale_price->formatByDecimal())
                                    ->heading(__("forms.fields.sale_price")),


                                Column::make('image')
                                    ->heading(__("forms.fields.image"))
                                    ->getStateUsing(fn($record) => $record->price)
                                    ->formatStateUsing(fn($record) => url($record->getFirstMediaUrl())),
                            ])->withFilename(fn() => 'products-' . now()->format('Y-m-d'))
                    ]),
                ImportAction::make('importServices')
                    ->visible(true)
                    ->importer(ServicesImporter::class),

                ImportAction::make('importProducts')
                    ->label(__('forms.actions.import_products'))
                    ->modalHeading(__('forms.actions.import_products'))
                    ->visible(true)
                    ->importer(ProductsImporter::class),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
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
            ProductsRelationManager::class,
        ];
    }

    public static function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    public static function getPages(): array {
        return [
            'index' => ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'edit' => EditService::route('/{record}/edit'),
            'view' => ViewService::route('/{record}/view'),
            'activities' => ListServiceActivities::route('/{record}/activities'),

        ];
    }


    public static function getNavigationBadge(): ?string {
        return static::getModel()::count();
    }

    public static function getGlobalSearchResultTitle(Model $record): string {
        return $record->name;
    }


}
