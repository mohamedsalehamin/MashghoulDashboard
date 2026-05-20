<?php

namespace App\CatalogModule\Resources;

use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Livewire\Component;
use Filament\Actions\ImportAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;
use App\CatalogModule\Models\Product;
use App\CatalogModule\Models\Seat;
use App\CatalogModule\Models\SeatGroup;
use App\CatalogModule\Models\Service;
use App\CatalogModule\Resources\SeatResource\Pages\CreateSeat;
use App\CatalogModule\Resources\SeatResource\Pages\EditSeat;
use App\CatalogModule\Resources\SeatResource\Pages\ListSeats;
use App\CatalogModule\Resources\SeatResource\Pages\ListSeatsActivities;
use App\CatalogModule\Resources\SeatResource\Pages\ViewSeat;
use App\CatalogModule\Resources\ServiceResource\Pages\ListServiceActivities;
use App\DefaultPanel\Settings\GeneralSettings;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\Filament\Imports\SeatsImporter;
use App\Filament\Imports\ServicesImporter;
use App\UsersModule\Models\Provider;
use Closure;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\HtmlString;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Tasawk\Models\Catalog\Category;
use Filament\Support\Enums\Width;

class SeatResource extends Resource {
    use HasTranslationLabel, Translatable;

    protected static ?string $model = Seat::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema {
        return $schema
            ->components([
                
                Select::make('provider_id')
                    ->live()
                    ->label(__('forms.fields.provider_name'))
                    ->relationship(
                        'provider',
                        'name',
                        fn (Builder $query) => $query->latest('id'),
                    )
                    // Provider name is Spatie JSON: search both locales (plain `name` LIKE misses Arabic).
                    ->searchable(['name->ar', 'name->en'])
                    ->getOptionLabelFromRecordUsing(function (Model $record): string {
                        if (! $record instanceof Provider) {
                            return '';
                        }

                        return (string) ($record->getTranslation('name', app()->getLocale())
                            ?: $record->getTranslation('name', 'ar')
                            ?: $record->getTranslation('name', 'en')
                            ?: '');
                    })
                    ->preload()
                    ->required()
                    ->afterStateUpdated(function ($state, $set, Get $get): void {
                        if (! $state) {
                            $set('meta_data.days_list', []);

                            return;
                        }
                        $provider = Provider::find($state);
                        if (! $provider) {
                            $set('meta_data.days_list', []);

                            return;
                        }
                        $expected = GeneralSettings::defaultSeatDaysListFromProvider($provider);
                        $expectedNames = collect($expected)->pluck('day_name')->sort()->values()->all();
                        $existing = $get('meta_data.days_list') ?? [];
                        $existingNames = collect($existing)->pluck('day_name')->filter()->sort()->values()->all();
                        if ($existingNames !== $expectedNames) {
                            $set('meta_data.days_list', $expected);
                        }
                    }),
                TextInput::make('title')
                    ->label(__('forms.fields.title'))
                    ->required(),

                Section::make(__('forms.sections.service_groups'))->schema([
                    Repeater::make('serviceGroups')
                        ->label('')
                        ->defaultItems(0)
                        ->addActionLabel(__('panel.actions.add'))
                        ->reorderable(false)
                        ->schema([
                            TextInput::make('title.ar')
                                ->label(__('forms.fields.title_ar'))
                                ->required(),
                            TextInput::make('title.en')
                                ->label(__('forms.fields.title_en'))
                                ->required(),
                            Select::make('services')
                                ->label(__('forms.fields.services'))
                                ->multiple()
                                ->searchable()
                                ->options(fn($get) => Service::where('provider_id', $get('../../provider_id'))->pluck('title', 'id'))
                                ->getOptionLabelFromRecordUsing(fn(Model $record): string => $record->title),
                        ]),
                ])->collapsible(),

                Section::make(__('sections.working_times'))
                    ->schema(fn (Get $get) => static::adminSeatWorkingDaysSchema($get))
                    ->statePath('meta_data.days_list'),

                Toggle::make('status')->default(1)
                    ->onColor('success')
                    ->offColor('danger')

            ])->columns(1);
    }

    /**
     * Admin seat form: only weekdays that are active on the selected provider profile.
     *
     * @return array<int, mixed>
     */
    protected static function adminSeatWorkingDaysSchema(Get $get): array
    {
        $pid = $get('provider_id');
        if (! $pid) {
            return [
                Placeholder::make('select_provider_for_hours')
                    ->label('')
                    ->content(new HtmlString(
                        '<p class="text-sm text-gray-600 dark:text-gray-400">'
                        . e(__('panel.messages.select_provider_for_seat_hours'))
                        . '</p>'
                    )),
            ];
        }

        $provider = Provider::find($pid);
        $active = $provider ? GeneralSettings::activeProviderDayNames($provider) : [];
        if ($active === []) {
            return [
                Placeholder::make('no_active_profile_working_days')
                    ->label('')
                    ->content(new HtmlString(
                        '<p class="text-sm text-gray-600 dark:text-gray-400">'
                        . e(__('panel.messages.no_active_profile_working_days'))
                        . '</p>'
                    )),
            ];
        }

        return GeneralSettings::daysListSchema($active);
    }

    public static function table(Table $table): Table {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn($query) => $query->whereHas("provider"))
            ->columns([
                TextColumn::make('id')
                    ->translateLabel()
                    ->searchable(),

                TextColumn::make('provider.name')
                    ->label(__('forms.fields.provider_name'))
                    ->searchable(true, fn(Builder $query, $search) => $query->whereHas('provider', fn($q) => $q->where('name->ar', 'like', "%$search%")->orWhere('name->en', 'like', "%$search%"))),
                TextColumn::make('title')->searchable(),
                TextColumn::make('services_count')
                    ->state(fn(Model $record) => $record->services()->where('provider_id',$record->provider->id)->count())
                    ->searchable(false),
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
                SelectFilter::make('provider_id')
                    ->options(fn() => Provider::pluck('name', 'id'))
                    ->label(__('forms.fields.provider_name'))
                    ->searchable(),
                TrashedFilter::make()
            ])
            ->headerActions([
                ExportAction::make()
                    ->modalHeading('')
                    ->modalDescription('')
                    ->exports([static::getSeatsExcelExport()]),
                ImportAction::make('importServices')
                    ->visible(true)
                    ->importer(SeatsImporter::class),
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
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exports([static::getSeatsExcelExport()]),

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
            ->components(fn($record) => [
                TextEntry::make('id'),
                TextEntry::make('provider.name'),
                TextEntry::make('title'),
                TextEntry::make('services_count')->state(fn($record) => $record->services()->count()),
                ...self::getWorkingDaysShift($record),

            ]);
    }

    public static function getWorkingDaysShift($record): array {
        $schema = [];
        $shifts = GeneralSettings::normalizeSeatDaysListToShifts($record->meta_data['days_list'] ?? []);

        foreach ($shifts as $index => $shiftDays) {
            $schema[] = RepeatableEntry::make('working_days_shift_' . $index)
                ->label(__("sections.shift_no", ['no' => $index + 1]))
                ->state($shiftDays)
                ->schema([
                    TextEntry::make('day_name')
                        ->formatStateUsing(fn ($state) => __("forms.fields.weekdays." . $state))
                        ->label(__("forms.fields.day_name")),
                    TextEntry::make('from'),
                    TextEntry::make('to'),
                ])
                ->columns(3);
        }

        return $schema;
    }

    public static function getSeatsExcelExport(): ExcelExport {
        return ExcelExport::make()
            ->label(__("forms.fields.export_services"))
            ->modifyQueryUsing(function ($query, $livewire) {
                $provider_id = null;
                if ($livewire && method_exists($livewire, 'getTable')) {
                    $filters = $livewire->getTable()->getFilters();
                    $provider_id = isset($filters['provider_id']) ? ($filters['provider_id']->getState()['value'] ?? null) : null;
                }
                return Seat::query()
                    ->with(['serviceGroups', 'services'])
                    ->when($provider_id, fn($q) => $q->where('provider_id', $provider_id));
            })
            ->withColumns([
                Column::make('id')->heading(__("forms.fields.db_row_id")),
                Column::make('provider_id')->heading(__("forms.fields.provider_id")),
                Column::make('meta_data.import_id')->heading(__("forms.fields.id")),
                Column::make('title.ar')
                    ->heading(__("forms.fields.name_ar"))
                    ->getStateUsing(fn($record) => $record->title)
                    ->formatStateUsing(fn($record) => $record->getOriginal('title')['ar'] ?? ''),
                Column::make('title.en')
                    ->heading(__("forms.fields.title_en"))
                    ->getStateUsing(fn($record) => $record->title)
                    ->formatStateUsing(fn($record) => $record->getOriginal('title')['en'] ?? ''),
                Column::make('services')
                    ->heading(__("forms.fields.services"))
                    ->getStateUsing(fn($record) => $record->services->map(fn($s) => $s->meta_data['import_id'] ?? $s->id)->join(',')),
                Column::make('service_groups')
                    ->heading(__("forms.fields.service_groups"))
                    ->getStateUsing(fn($record) => $record->serviceGroups->pluck('title')->map(fn($t) => is_array($t) ? ($t['ar'] ?? $t['en'] ?? '') : $t)->filter()->unique()->values()->join('|')),
                Column::make('service_group_names')
                    ->heading(__("forms.fields.service_group_names"))
                    ->getStateUsing(function ($record) {
                        return $record->services->map(function ($s) use ($record) {
                            $groupId = $s->pivot->service_group_id ?? null;
                            if (!$groupId) {
                                return '';
                            }
                            $g = $record->serviceGroups->firstWhere('id', $groupId);
                            return $g ? (is_array($g->title) ? ($g->title['ar'] ?? $g->title['en'] ?? '') : (string) $g->title) : '';
                        })->join(',');
                    }),
            ])->withFilename(fn() => 'seats' . now()->format('Y-m-d'));
    }

    public static function getRelations(): array {
        return [
        ];
    }
    public static function getMaxContentWidth(): Width
    {
        return Width::Full;
    }
    public static function getPages(): array {
        return [
            'index' => ListSeats::route('/'),
            'create' => CreateSeat::route('/create'),
            'edit' => EditSeat::route('/{record}/edit'),
            'view' => ViewSeat::route('/{record}/view'),
            'activities' => ListSeatsActivities::route('/{record}/activities'),
        ];
    }


    public static function getNavigationBadge(): ?string {
        return static::getModel()::whereHas("provider")->count();
    }

    public static function getGlobalSearchResultTitle(Model $record): string {
        return $record->name;
    }


}
