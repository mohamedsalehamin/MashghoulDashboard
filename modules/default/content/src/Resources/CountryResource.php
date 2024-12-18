<?php

namespace App\ContentModule\Resources;

use App\ContentModule\Models\Country;
use App\ContentModule\Resources\CountryResource\Pages\CreateCountry;
use App\ContentModule\Resources\CountryResource\Pages\EditCountry;
use App\ContentModule\Resources\CountryResource\Pages\ListCountries;
use App\DefaultPanel\Enum\ModelStatus;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CountryResource extends Resource {
    use Translatable;
    use HasTranslationLabel;

    protected static ?string $model = Country::class;
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Section::make('basic_information')
                    ->schema([
                        TextInput::make('name')->required(),
                        TextInput::make('phone_code')->numeric()->required(),

                        Toggle::make('status')->default(1)
                            ->onColor('success')
                            ->offColor('danger')
                    ])
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                TextColumn::make('id')->searchable(),
                TextColumn::make('name')->searchable(['name->ar', 'name->en']),
                IconColumn::make('status')
                    ->boolean()
                    ->action(
                        Action::make('Active')
                            ->label(fn(Country $record): string => $record->status ? __('panel.messages.deactivate') : __('panel.messages.activate'))
//                            ->disabled(fn(Model $record): bool => !filament()->auth()->user()->can('update', $record))
                            ->requiresConfirmation()
                            ->action(fn(Country $record) => $record->toggleStatus())

                    )

            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ModelStatus::class)
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => ListCountries::route('/'),
            'create' => CreateCountry::route('/create'),
            'edit' => EditCountry::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string {
        return __('menu.locations');
    }
}
