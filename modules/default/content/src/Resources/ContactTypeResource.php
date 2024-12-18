<?php

namespace App\ContentModule\Resources;

use App\ContentModule\Models\ContactType;
use App\ContentModule\Resources\ContactTypeResource\Pages\CreateContactType;
use App\ContentModule\Resources\ContactTypeResource\Pages\EditContactType;
use App\ContentModule\Resources\ContactTypeResource\Pages\ListContactTypes;
use App\DefaultPanel\Enum\ModelStatus;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Filament\Resources\Concerns\Translatable;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use App\ContentModule\Resources\ContactTypeResource\ContactTypeResource\Pages;

class ContactTypeResource extends Resource implements HasShieldPermissions {
    use Translatable;
    use HasTranslationLabel;

    protected static ?string $model = ContactType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Section::make("basic_information")
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->columnSpan(['xl' => 2])
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
                TextColumn::make('id'),
                TextColumn::make('name'),
                IconColumn::make('status')
                    ->boolean()
                    ->action(
                        Action::make('Active')
                            ->label(fn(ContactType $record): string => $record->status ? __('panel.messages.deactivate') : __('panel.messages.activate'))
                            ->disabled(fn(Model $record): bool => !auth()->user()->can('update', $record))
                            ->requiresConfirmation()
                            ->action(fn(ContactType $record) => $record->toggleStatus())

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
            ]);
    }

    public static function getRelations(): array {
        return [
            //
        ];
    }

    public static function getPages(): array {
        return [
            'index' => ListContactTypes::route('/'),
            'create' => CreateContactType::route('/create'),
            'edit' => EditContactType::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string {
        return __('menu.content');
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
}
