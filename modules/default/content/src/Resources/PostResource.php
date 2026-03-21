<?php

namespace App\ContentModule\Resources;

use App\ContentModule\Models\Category;
use App\ContentModule\Models\Post;
use App\ContentModule\Resources\PostResource\Pages\CreatePost;
use App\ContentModule\Resources\PostResource\Pages\EditPost;
use App\ContentModule\Resources\PostResource\Pages\ListPosts;
use App\DefaultPanel\Enum\ModelStatus;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class PostResource extends Resource implements HasShieldPermissions
{
    use HasTranslationLabel;
    use Translatable;

    protected static ?string $model = Post::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('sections.basic_information'))
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->columnSpan(['xl' => 2])
                            ->translateLabel(),
                        TextInput::make('slug')
                            ->label(__('forms.fields.slug'))
                            ->placeholder(__('forms.placeholders.slug_auto'))
                            ->columnSpan(['xl' => 2])
                            ->translateLabel(),
                        SpatieMediaLibraryFileUpload::make('default')
                            ->collection('default')
                            ->image()
                            ->nullable(),
                        RichEditor::make('description')
                            ->required()
                            ->columnSpan(['xl' => 2])
                            ->extraInputAttributes(['style' => 'min-height: 20rem;'])
                            ->translateLabel(),
                        DateTimePicker::make('publish_date')
                            ->label(__('forms.fields.publish_date'))
                            ->required()
                            ->default(now()),
                        Toggle::make('status')
                            ->default(1)
                            ->onColor('success')
                            ->offColor('danger')
                            ->translateLabel(),
                    ])
                    ->columns(2),
                Section::make(__('sections.seo_meta'))
                    ->schema([
                        Textarea::make('meta_description')
                            ->label(__('forms.fields.meta_description'))
                            ->rows(3)
                            ->columnSpan(['xl' => 2])
                            ->translateLabel(),
                        TagsInput::make('meta_keywords')
                            ->label(__('forms.fields.meta_keywords'))
                            ->separator(',')
                            ->splitKeys([',', 'Enter'])
                            ->placeholder(__('forms.placeholders.meta_keywords_tags'))
                            ->columnSpan(['xl' => 2])
                            ->translateLabel(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                SpatieMediaLibraryImageColumn::make('default')
                    ->collection('default')
                    ->circular()
                    ->defaultImageUrl(fn () => asset('assets/site/images/product-demo_optmized.webp')),
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('publish_date')
                    ->label(__('forms.fields.publish_date'))
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('status')
                    ->boolean()
                    ->action(
                        Action::make('toggleStatus')
                            ->label(fn (Post $record): string => $record->status ? __('panel.messages.deactivate') : __('panel.messages.activate'))
                            ->disabled(fn (Model $record): bool => ! auth()->user()->can('update', $record))
                            ->requiresConfirmation()
                            ->action(fn (Post $record) => $record->toggleStatus())
                    ),
            ])
            ->defaultSort('publish_date', 'desc')
            ->filters([
                SelectFilter::make('status')->options(ModelStatus::class),
                SelectFilter::make('category_id')
                    ->label(__('forms.fields.category'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
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
        return [];
    }

    public static function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
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
            'delete',
            'delete_any',
        ];
    }
}
