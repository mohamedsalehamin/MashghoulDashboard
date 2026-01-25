<?php

namespace App\ProviderPanel\Filament\Resources;

use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Grid;
use App\CatalogModule\Models\Seat;
use App\CatalogModule\Models\Service;

use App\DefaultPanel\Settings\GeneralSettings;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\ProviderPanel\Filament\Resources\SeatResource\Pages\CreateSeat;
use App\ProviderPanel\Filament\Resources\SeatResource\Pages\EditSeat;
use App\ProviderPanel\Filament\Resources\SeatResource\Pages\ListSeats;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use ParagonIE\Sodium\Core\Curve25519\H;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class SeatResource extends Resource {
    use HasTranslationLabel, Translatable;

    protected static ?string $model = Seat::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema {
        return $schema
            ->components([
                Hidden::make('provider_id')->default(provider()?->id),

                TextInput::make('title')
                    ->label(__('forms.fields.title'))
                    ->required(),
                Select::make('services')
                    ->required()
                    ->multiple()
                    ->searchable(false)
                    ->relationship('services','title')
                    ->label(__('forms.fields.services'))
                    ->options(fn($get) => Service::where("provider_id", $get("provider_id"))->pluck('title', 'id'))
                ->getOptionLabelFromRecordUsing(fn(Model $record): string => $record->title),

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
            ->modifyQueryUsing(fn($query) => $query->where('provider_id', provider()->id))
            ->columns([
                TextColumn::make('id')
                    ->translateLabel()
                    ->searchable(),

                TextColumn::make('provider.name')
                    ->label(__('forms.fields.provider_name'))
                    ->searchable(),
                TextColumn::make('title')->searchable(),
                TextColumn::make('services_count')
                    ->state(fn(Model $record) => $record->services()->where('provider_id',$record->provider?->id)->count())
                    ->searchable(false),

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
                EditAction::make(),
                DeleteAction::make(),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportBulkAction::make(),

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
                Grid::make()->schema([
                    Section::make("basic_information")
                        ->schema([
                            TextEntry::make('id'),
                            TextEntry::make('name'),
                            TextEntry::make('status')
                                ->formatStateUsing(fn(string $state): string => $state ? __('panel.enums.ACTIVE') : __('panel.enums.INACTIVE'))
                                ->badge(),
                        ])->columns(1),

                ])->columns(2)
            ]);
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
        ];
    }


    public static function getNavigationBadge(): ?string {
        return static::getModel()::where('provider_id', provider()->id)->count();
    }

    public static function getGlobalSearchResultTitle(Model $record): string {
        return $record->name;
    }


}
