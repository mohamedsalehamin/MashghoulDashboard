<?php

namespace App\ContentModule\Resources;

use App\ContentModule\Resources\PageResource\Pages\CreatePage;
use App\ContentModule\Resources\PageResource\Pages\EditPage;
use App\ContentModule\Resources\PageResource\Pages\ListPages;
use Filament\Forms;
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
use Illuminate\Database\Eloquent\Model;
use App\ContentModule\Models\Page;
use App\DefaultPanel\Enum\ModelStatus;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;


class PageResource extends Resource {
    use Translatable;
    use HasTranslationLabel;

    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\Section::make("basic_information")
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->columnSpan([
                                'xl' => 2,
                            ])
                            ->translateLabel(),
                        Forms\Components\RichEditor::make('description')
                            ->required()
                            ->columnSpan([
                                'xl' => 2,
                            ])
                            ->translateLabel(),

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
                TextColumn::make('title')->searchable(),
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
            ->actions([
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

    public static function getRelations(): array {
        return [
            //
        ];
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
