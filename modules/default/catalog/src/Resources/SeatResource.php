<?php

namespace App\CatalogModule\Resources;

use App\CatalogModule\Models\Seat;
use App\CatalogModule\Models\Service;
use App\CatalogModule\Resources\SeatResource\Pages\CreateSeat;
use App\CatalogModule\Resources\SeatResource\Pages\EditSeat;
use App\CatalogModule\Resources\SeatResource\Pages\ListSeats;
use App\DefaultPanel\Settings\GeneralSettings;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\UsersModule\Models\Provider;
use Closure;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
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
                    ->required()
                    ->multiple()
                    ->relationship('services','title')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->getTranslation('title','en')} - {$record->getTranslation('title','ar')}")
                    ->label(__('forms.fields.services'))
                    ->options(fn($get) => Service::where("provider_id", $get("provider_id"))->pluck('title', 'id')),
                Section::make("working_times")->schema([...GeneralSettings::daysListSchema(),

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
                    ->searchable(),
                TextColumn::make('title')->searchable(),
                TextColumn::make('services_count')->counts("services")->searchable(),


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
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            ->schema([
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
        return static::getModel()::count();
    }

    public static function getGlobalSearchResultTitle(Model $record): string {
        return $record->name;
    }



}
