<?php

namespace App\CatalogModule\Resources\CategoryResource\RelationManagers;

use App\ContentModule\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ChildrenRelationManager extends RelationManager {
    protected static string $relationship = 'children';

    public function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table {
        return $table
            ->recordTitleAttribute('id')
            ->heading(__('menu.categories'))
            ->columns([
                Tables\Columns\TextColumn::make('id')->toggleable(false),
                Tables\Columns\TextColumn::make('name')->toggleable(false),
                IconColumn::make('status')
                    ->boolean()
                    ->action(
                        Action::make('Active')
                            ->label(fn(Model $record): string => $record->status ? __('panel.messages.deactivate') : __('panel.messages.activate'))
                            ->disabled(fn(Model $record): bool => !auth()->user()->can('update', $record))
                            ->requiresConfirmation()
                            ->action(fn(Model $record) => $record->toggleStatus())

                    )->toggleable(false),
            ])
            ->filters([

            ])
            ->headerActions([
            ])
            ->actions([

                Tables\Actions\Action::make('edit')
                    ->label(__('forms.fields.edit'))
                    ->url(fn(Category $record): string => route('filament.admin.resources.categories.edit', $record->id)),
                Tables\Actions\Action::make('delete')
                    ->label(__('panel.actions.delete'))
                    ->color('danger')
                    ->before(function ($action, Category $category) {
                        if ($category->products()->count()) {
                            Notification::make()
                                ->warning()
                                ->title(__('panel.messages.warning'))
                                ->body(__('panel.messages.category_has_many_products', ['category' => $category->name]))
                                ->persistent()
                                ->send();
                            $action->cancel();
                        }
                    })
                    ->action(fn(Category $record) => $record->delete()),
            ])
            ->bulkActions([

            ])
            ->emptyStateActions([

            ]);

    }

    protected function canEdit(Model $record): bool {
        return true;
    }

    protected function canDelete(Model $record): bool {
        return true;
    }
}
