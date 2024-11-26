<?php

namespace App\CatalogModule\Resources;

use App\CatalogModule\Models\Specialization;
use App\CatalogModule\Resources\SpecializationResource\Pages\CreateSpecialization;
use App\CatalogModule\Resources\SpecializationResource\Pages\EditSpecialization;
use App\CatalogModule\Resources\SpecializationResource\Pages\ListSpecialties;
use App\CatalogModule\Resources\SpecializationResource\Pages\ViewSpecialization;
use App\CatalogModule\Resources\SpecializationResource\RelationManagers\ChildrenRelationManager;
use App\ContentModule\Models\Category;
use App\DefaultPanel\Enum\ModelStatus;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SpecializationResource extends Resource implements HasShieldPermissions {
    use Translatable;
    use HasTranslationLabel;

    protected static ?string $model = Specialization::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?int $navigationSort = 3;
    public static function form(Form $form): Form {
        return $form
            ->schema([
                Section::make("basic_information")
                    ->schema([
                        TextInput::make('name')
                            ->required(),

                        SpatieMediaLibraryFileUpload::make('image')
                            ->image()
                            ->required(),

                        Forms\Components\Select::make("parent_id")
                            ->label(__('forms.fields.main_specialization'))
                            ->options(fn(Get $get): Collection => Specialization::parent()->where('id', "!=", $get('id'))
                                ->pluck('name', 'id')),


                        Toggle::make('status')
                            ->default(1)
                            ->onColor('success')
                            ->offColor('danger')
                    ])
            ])->columns(1);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('name'),
                IconColumn::make('status')
                    ->boolean()
                    ->action(
                        Action::make('Active')
                            ->label(fn(Specialization $record): string => $record->status ? __('panel.messages.deactivate') : __('panel.messages.activate'))
                            ->disabled(fn(Model $record): bool => !auth()->user()->can('update', $record))
                            ->requiresConfirmation()
                            ->action(fn(Specialization $record) => $record->toggleStatus())

                    ),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ModelStatus::class)
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    static public function infolist(Infolist $infolist): Infolist {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name'),

                        TextEntry::make('status')
                            ->formatStateUsing(fn(string $state): string => $state ? 'Yes' : 'No')
                            ->color(fn(string $state): string => match ($state) {
                                '1' => 'success',
                                '0' => 'danger',
                            })
                    ]),

            ]);
    }

    public static function getRelations(): array {
        return [
            ChildrenRelationManager::class
        ];
    }

    public static function getPages(): array {
        return [
            'index' =>ListSpecialties::route('/'),
            'create' => CreateSpecialization::route('/create'),
            'edit' => EditSpecialization::route('/{record}/edit'),
            'view' =>ViewSpecialization::route('/{record}'),
        ];
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
    public static function getNavigationGroup(): ?string {
        return __('menu.crew');
    }
}
