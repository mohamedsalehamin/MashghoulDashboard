<?php

namespace App\ContentModule\Resources;

use App\ContentModule\Models\Banner;
use App\ContentModule\Models\Category;
use App\ContentModule\Resources\BannerResource\Pages\CreateBanner;
use App\ContentModule\Resources\BannerResource\Pages\EditBanner;
use App\ContentModule\Resources\BannerResource\Pages\ListBanners;
use App\DefaultPanel\Enum\ModelStatus;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\UsersModule\Models\Provider;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class BannerResource extends Resource implements HasShieldPermissions
{
    use HasTranslationLabel;
    use Translatable;

    protected static ?string $model = Banner::class;

    protected static ?int $navigationSort = 1;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    public static function form(Schema $schema): Schema
    {

        return $schema->components([
            TextInput::make('name')
                ->required()
                ->translateLabel(),

            SpatieMediaLibraryFileUpload::make('image_ar')
                ->label(__('forms.fields.image_en'))
                ->disk('public')
                ->collection('en')
                ->required(),

            SpatieMediaLibraryFileUpload::make('image_en')
                ->disk('public')
                ->label(__('forms.fields.image_ar'))
                ->collection('ar')
                ->required(),
            Select::make('placement')
                ->label(__('sections.banner_placement'))
                ->options([
                    'website' => __('sections.banner_placement_website'),
                    'app' => __('sections.banner_placement_app'),
                ])
                ->default('app')
                ->required(),
            Select::make('object_type')
                ->live()
                ->options([
                    'link' => __('forms.fields.external_link'),
                    'category' => __('forms.fields.category'),
                    'provider' => __('forms.fields.provider'),
                ]),

            Select::make('object_id')
                ->visible(fn ($get) => $get('object_type') == 'category')
                ->label(__('forms.fields.category'))
                ->options(Category::pluck('name', 'id'))
                ->required(),
            Select::make('object_id')
                ->visible(fn ($get) => $get('object_type') == 'provider')
                ->label(__('forms.fields.provider'))
                ->options(fn () => Provider::enabled()->withoutTrashed()->orderBy('id')->pluck('name', 'id'))
                ->required(),
            TextInput::make('object_id')
                ->visible(fn ($get) => $get('object_type') == 'link')
                ->label(__('forms.fields.link'))
                ->url(),

            Toggle::make('status')->default(1)
                ->onColor('success')
                ->offColor('danger')
                ->translateLabel(),

        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->orderBy('sort'))
            ->reorderable('sort', true)
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('placement')
                    ->label(__('sections.banner_placement'))
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'website' => __('sections.banner_placement_website'),
                        'app' => __('sections.banner_placement_app'),
                        default => $state ?? '—',
                    }),
                TextColumn::make('name'),
                IconColumn::make('status')
                    ->boolean()
                    ->action(
                        Action::make('Active')
                            ->label(fn (Banner $record): string => $record->status ? __('panel.messages.deactivate') : __('panel.messages.activate'))
                            ->disabled(fn (Model $record): bool => ! filament()->auth()->user()->can('update', $record))
                            ->requiresConfirmation()
                            ->action(fn (Banner $record) => $record->toggleStatus())

                    ),

            ])
            ->filters([
                SelectFilter::make('placement')
                    ->label(__('sections.banner_placement'))
                    ->options([
                        'website' => __('sections.banner_placement_website'),
                        'app' => __('sections.banner_placement_app'),
                    ]),
                SelectFilter::make('status')
                    ->options(ModelStatus::class),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBanners::route('/'),
            'create' => CreateBanner::route('/create'),
            'edit' => EditBanner::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('menu.content');
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
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
