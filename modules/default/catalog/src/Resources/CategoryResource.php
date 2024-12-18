<?php

namespace App\CatalogModule\Resources;

use App\CatalogModule\Resources\CategoryResource\Pages\CreateCategory;
use App\CatalogModule\Resources\CategoryResource\Pages\EditCategory;
use App\CatalogModule\Resources\CategoryResource\Pages\ListCategories;
use App\CatalogModule\Resources\CategoryResource\Pages\ViewCategory;
use App\CatalogModule\Resources\CategoryResource\RelationManagers\ChildrenRelationManager;
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

class CategoryResource extends Resource  {
    use Translatable;
    use HasTranslationLabel;

    protected static ?string $model = Category::class;
//    protected static string $view = 'filament.pages.listing.categories';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 3;
    public static function form(Form $form): Form {
        return $form
            ->schema([
                Section::make("basic_information")
                    ->schema([
                        TextInput::make('name')
                            ->required(),

                        SpatieMediaLibraryFileUpload::make('image_ar')->collection('ar')->image()->required(),
                        SpatieMediaLibraryFileUpload::make('image_en')->collection('en')->image()->required(),

                        Forms\Components\Select::make("parent_id")
                            ->label(__('forms.fields.category_parent_id'))
                            ->options(fn(Get $get): Collection => Category::where('id', "!=", $get('id'))
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
                            ->label(fn(Category $record): string => $record->status ? __('panel.messages.deactivate') : __('panel.messages.activate'))
                            ->disabled(fn(Model $record): bool => !auth()->user()->can('update', $record))
                            ->requiresConfirmation()
                            ->action(fn(Category $record) => $record->toggleStatus())

                    ),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                SelectFilter::make('status')
                    ->options(ModelStatus::class)
            ])
            ->actions([
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->emptyStateActions([
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
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
            'view' => ViewCategory::route('/{record}'),
        ];
    }


}
