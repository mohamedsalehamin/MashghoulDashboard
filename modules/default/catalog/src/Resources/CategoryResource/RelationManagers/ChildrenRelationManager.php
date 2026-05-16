<?php

namespace App\CatalogModule\Resources\CategoryResource\RelationManagers;

use App\ContentModule\Models\Category;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ChildrenRelationManager extends RelationManager
{
    protected static string $relationship = 'children';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id')
                    ->required()
                    ->maxLength(255),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->heading(__('menu.categories'))
            ->columns([
                TextColumn::make('id')->toggleable(false),
                TextColumn::make('name')->toggleable(false),
                IconColumn::make('status')
                    ->boolean()
                    ->action(
                        Action::make('Active')
                            ->label(fn (Model $record): string => $record->status ? __('panel.messages.deactivate') : __('panel.messages.activate'))
                            ->disabled(fn (Model $record): bool => ! auth()->user()->can('update', $record))
                            ->requiresConfirmation()
                            ->action(fn (Model $record) => $record->toggleStatus())

                    )->toggleable(false),
            ])
            ->filters([

            ])
            ->headerActions([
            ])
            ->recordActions([

                Action::make('edit')
                    ->label(__('forms.fields.edit'))
                    ->url(fn (Category $record): string => route('filament.admin.resources.categories.edit', $record->id)),
                Action::make('delete')
                    ->label(__('panel.actions.delete'))
                    ->color('danger')
                    ->before(function ($action, Category $category) {
                        $label = $category->getTranslation('name', app()->getLocale()) ?: ('#'.$category->getKey());

                        if ($category->children()->exists()) {
                            Notification::make()
                                ->warning()
                                ->title(__('panel.messages.warning'))
                                ->body(__('panel.messages.category_has_many_subcategories', ['category' => $label]))
                                ->persistent()
                                ->send();
                            $action->cancel();

                            return;
                        }

                        if ($category->posts()->exists()) {
                            Notification::make()
                                ->warning()
                                ->title(__('panel.messages.warning'))
                                ->body(__('panel.messages.category_has_many_posts', ['category' => $label]))
                                ->persistent()
                                ->send();
                            $action->cancel();
                        }
                    })
                    ->action(fn (Category $record) => $record->delete()),
            ])
            ->toolbarActions([

            ])
            ->emptyStateActions([

            ]);

    }

    protected function canEdit(Model $record): bool
    {
        return true;
    }

    protected function canDelete(Model $record): bool
    {
        return true;
    }
}
