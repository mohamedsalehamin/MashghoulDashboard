<?php

namespace App\ContentModule\Resources;

use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\RichEditor;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;
use App\ContentModule\Resources\PageResource\Pages\CreatePage;
use App\ContentModule\Resources\PageResource\Pages\EditPage;
use App\ContentModule\Resources\PageResource\Pages\ListPages;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Support\Enums\Width;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use App\ContentModule\Models\Page;
use App\DefaultPanel\Enum\ModelStatus;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;


class PageResource extends Resource {
    use Translatable;
    use HasTranslationLabel;

    protected static ?string $model = Page::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema {
        return $schema
            ->components([
                Section::make(__("sections.basic_information"))
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->columnSpan([
                                'xl' => 2,
                            ])
                            ->translateLabel(),
                        TextInput::make('slug')
                            ->label(__('forms.fields.slug'))
                            ->placeholder(__('forms.placeholders.slug_auto'))
                            ->columnSpan([
                                'xl' => 2,
                            ])
                            ->translateLabel(),
                        RichEditor::make('description')
                            ->required()
                            ->columnSpan([
                                'xl' => 2,
                            ])
                            ->translateLabel(),

                        Toggle::make('status')->default(1)
                            ->onColor('success')
                            ->offColor('danger')

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
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                TextColumn::make('id')->searchable(),
                TextColumn::make('title')->searchable(),
                TextColumn::make('slug')->searchable()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('status')
                    ->boolean()
                    ->action(
                        Action::make('Active')
                            ->label(fn(Page $record): string => $record->status ? __('panel.messages.deactivate') : __('panel.messages.activate'))
                            ->disabled(fn(Model $record): bool => !auth()->user()->can('update', $record))
                            ->requiresConfirmation()
                            ->action(fn(Page $record) => $record->toggleStatus())


                    )
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
            ])
            ->emptyStateActions([
                CreateAction::make(),
            ]);
    }

    public static function getRelations(): array {
        return [
            //
        ];
    }

    public static function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    public static function getPages(): array {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string {
        return __('menu.content');
    }
}
