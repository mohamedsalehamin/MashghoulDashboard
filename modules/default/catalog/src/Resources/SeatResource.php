<?php

namespace App\CatalogModule\Resources;

use App\CatalogModule\Models\Seat;
use App\CatalogModule\Models\Service;
use App\CatalogModule\Resources\SeatResource\Pages\CreateSeat;
use App\CatalogModule\Resources\SeatResource\Pages\EditSeat;
use App\CatalogModule\Resources\SeatResource\Pages\ListSeats;
use App\CatalogModule\Resources\SeatResource\Pages\ListSeatsActivities;
use App\CatalogModule\Resources\SeatResource\Pages\ViewSeat;
use App\CatalogModule\Resources\ServiceResource\Pages\ListServiceActivities;
use App\DefaultPanel\Settings\GeneralSettings;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\UsersModule\Models\Provider;
use Closure;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Tasawk\Models\Catalog\Category;


class SeatResource extends Resource {
    use HasTranslationLabel, Translatable;

    protected static ?string $model = Seat::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Select::make('provider_id')
                    ->live()
                    ->label(__('forms.fields.provider_name'))
                    ->options(fn() => Provider::pluck('name', 'id'))
                    ->required(),
                TextInput::make('title')
                    ->label(__('forms.fields.title'))
                    ->required(),
                Select::make('services')
                    ->label(__('forms.fields.services'))
                    ->required()
                    ->multiple()
                    ->searchable(false)
                    ->preload()
                    ->relationship('services', 'title', fn($query, $get) => $query->where("provider_id", $get("provider_id")))
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->getTranslation('title','en')} - {$record->getTranslation('title','ar')}")

                ,
                Section::make("working_times")->schema([
                    Repeater::make('working_times')
                        ->statePath('meta_data.days_list')
                        ->label('')
                        ->minItems(1)
                        ->maxItems(2)
                        ->schema(GeneralSettings::daysListSchema())

                ]),

                Toggle::make('status')->default(1)
                    ->onColor('success')
                    ->offColor('danger')

            ])->columns(1);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->translateLabel()
                    ->searchable(),

                TextColumn::make('provider.name')
                    ->label(__('forms.fields.provider_name'))
                    ->searchable(true, fn(Builder $query, $search) => $query->whereHas('provider', fn($q) => $q->where('name->ar', 'like', "%$search%")->orWhere('name->en', 'like', "%$search%"))),
                TextColumn::make('title')->searchable(),
                TextColumn::make('services_count')->counts("services")->searchable(false),
                TextColumn::make('created_at')->date(),


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
                Tables\Filters\TrashedFilter::make()
            ])
            ->actions([
                Action::make('activities')
                    ->label(__("forms.actions.activities"))
                    ->url(fn($record) => static::getUrl('activities', ['record' => $record])),

                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
//            ->checkIfRecordIsSelectableUsing(fn(Model $record): bool => !$record->orders()->count())
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->striped();
    }

    static public function infolist(Infolist $infolist): Infolist {
        return $infolist
            ->schema(fn($record) => [
                TextEntry::make('id'),
                TextEntry::make('provider.name'),
                TextEntry::make('title'),
                TextEntry::make('services_count')->state(fn($record) => $record->services()->count()),
                ...self::getWorkingDaysShift($record),

            ]);
    }

    public static function getWorkingDaysShift($record): array {
        $schema = [];
        foreach ($record->meta_data['days_list'] ?? [] as $index=> $slot) {

            $schema[] = RepeatableEntry::make('meta_data.days_list')
                ->label(__("sections.shift_no",['no'=>$index+1]))
                ->state(fn($record) => collect($slot)->where('status', true)->toArray())
                ->statePath('meta_data.days_list.'.$index)
                ->schema([
                    TextEntry::make('day_name')
                        ->formatStateUsing(fn($record, $state) => __("forms.fields.weekdays." . $state))
                        ->label(__("forms.fields.day_name")),
                    TextEntry::make('from'),
                    TextEntry::make('to'),
                ])
                ->columns(3);
        }
        return $schema;
    }

    public static function getRelations(): array {
        return [
        ];
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
        return static::getModel()::count();
    }

    public static function getGlobalSearchResultTitle(Model $record): string {
        return $record->name;
    }


}
