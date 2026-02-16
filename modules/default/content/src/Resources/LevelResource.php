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
use Filament\Actions\CreateAction;
use App\ContentModule\Models\Level;
use App\ContentModule\Resources\LevelResource\Pages\CreateLevel;
use App\ContentModule\Resources\LevelResource\Pages\EditLevel;
use App\ContentModule\Resources\LevelResource\Pages\ListLevels;
use App\ContentModule\Resources\PageResource\Pages\CreatePage;
use App\ContentModule\Resources\PageResource\Pages\EditPage;
use App\ContentModule\Resources\PageResource\Pages\ListPages;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
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


class LevelResource extends Resource {
    use Translatable;
    use HasTranslationLabel;

    protected static ?string $model = Level::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema {
        return $schema
            ->components([
                Section::make(__("sections.basic_information"))
                    ->schema([
                        TextInput::make('title')
                            ->label(__('forms.fields.slide_name'))
                            ->required(),
                        TextInput::make('value')
                            ->label(__('forms.fields.points'))
                            ->numeric()->minValue(1)->required(),
                        TextInput::make('price')->minValue(1)->numeric()->required()->suffix(__('forms.suffixes.sar')),
                        TextInput::make('duration')
                            ->label(__('forms.fields.expire_after'))
                            ->numeric()->required()
                            ->suffix(__('forms.suffixes.days')),

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
                TextColumn::make('title')
                    ->label(__('forms.fields.slide_name'))
                    ->searchable(),
                TextColumn::make('value')
                    ->label(__('forms.fields.points'))
                    ->searchable(),
                TextColumn::make('price')->searchable(),
                IconColumn::make('status')
                    ->boolean()
                    ->action(
                        Action::make('Active')
                            ->label(fn( $record): string => $record->status ? __('panel.messages.deactivate') : __('panel.messages.activate'))
                            ->disabled(fn(Model $record): bool => !auth()->user()->can('update', $record))
                            ->requiresConfirmation()
                            ->action(fn( $record) => $record->toggleStatus())


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
            'index' => ListLevels::route('/'),
            'create' => CreateLevel::route('/create'),
            'edit' => EditLevel::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string {
        return __('menu.content');
    }
}
