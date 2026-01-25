<?php

namespace App\ContentModule\Resources;

use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\ContentModule\Models\Country;
use App\ContentModule\Models\State;
use App\ContentModule\Resources\StateResource\Pages\CreateState;
use App\ContentModule\Resources\StateResource\Pages\EditState;
use App\ContentModule\Resources\StateResource\Pages\ListStates;
use App\DefaultPanel\Enum\ModelStatus;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\DefaultPanel\Rules\TranslatableRequired;
class StateResource extends Resource {
    use Translatable;
    use HasTranslationLabel;

    protected static ?string $model = State::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-map-pin';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema {
        return $schema
            ->components([
                Section::make('basic_information')
                    ->schema([
                        TextInput::make('name')->required()
                        ->rules([new TranslatableRequired()]), 

                        Select::make("country_id")
                            ->searchable()
                            ->options(fn() => Country::pluck('name', 'id'))
                            ->required(),




                        Toggle::make('status')->default(1)
                            ->onColor('success')
                            ->offColor('danger')
                    ])

            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('name'),
                TextColumn::make('country.name')->label(__("forms.fields.country_name")),
                IconColumn::make('status')
                    ->boolean()
                    ->action(
                        Action::make('Active')
                            ->label(fn(State $record): string => $record->status ? __('panel.messages.deactivate') : __('panel.messages.activate'))
                            ->disabled(fn(State $record): bool => !auth()->user()->can('update', $record))
                            ->requiresConfirmation()
                            ->action(fn(State $record) => $record->toggleStatus())

                    ),

            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ModelStatus::class)
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array {
        return [
            //
        ];
    }

    public static function getPages(): array {
        return [
            'index' => ListStates::route('/'),
            'create' => CreateState::route('/create'),
            'edit' => EditState::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string {
        return __('menu.locations');
    }
}
