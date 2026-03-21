<?php

namespace App\CatalogModule\Resources;

use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Actions\Action;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;
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
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Support\Enums\Width;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class CategoryResource extends Resource implements HasShieldPermissions {
    use Translatable;
    use HasTranslationLabel;

    protected static ?string $model = Category::class;
//    protected static string $view = 'filament.pages.listing.categories';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 3;
    public static function form(Schema $schema): Schema {
        return $schema
            ->components([
                Section::make(__("sections.basic_information"))
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->translateLabel(),
                        TextInput::make('slug')
                            ->label(__('forms.fields.slug'))
                            ->placeholder(__('forms.placeholders.slug_auto'))
                            ->translateLabel(),

                        SpatieMediaLibraryFileUpload::make('image_ar')->collection('ar')->image()->required(),
                        SpatieMediaLibraryFileUpload::make('image_en')->collection('en')->image()->required(),
                        SpatieMediaLibraryFileUpload::make('icon')->collection('icon')->label(__('forms.fields.icon'))->image(),

                        Select::make("parent_id")
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
            ->modifyQueryUsing(fn($query)=>$query->orderBy("sort"))
            ->reorderable('sort', true)
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('name'),
                SpatieMediaLibraryImageColumn::make('image')->collection(app()->getLocale()),
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
                TrashedFilter::make(),
                SelectFilter::make('status')
                    ->options(ModelStatus::class)
            ])
            ->recordActions([
                RestoreAction::make(),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])->emptyStateActions([
                CreateAction::make(),
            ]);
    }

    static public function infolist(Schema $schema): Schema {
        return $schema
            ->components([
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

    public static function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    public static function getPages(): array {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
            'view' => ViewCategory::route('/{record}'),
        ];
    }


    public static function getPermissionPrefixes(): array {
        return [
            'view_any',
            'view',
            'create',
            'update',
            'restore',
            'restore_any',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
        ];
    }
}
