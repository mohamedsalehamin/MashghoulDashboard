<?php

namespace App\ContentModule\Resources;

use App\ContentModule\Models\ProviderActivity;
use App\ContentModule\Resources\ProviderActivityResource\Pages\CreateProviderActivity;
use App\ContentModule\Resources\ProviderActivityResource\Pages\EditProviderActivity;
use App\ContentModule\Resources\ProviderActivityResource\Pages\ListProviderActivities;
use App\DefaultPanel\Enum\ModelStatus;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class ProviderActivityResource extends Resource implements HasShieldPermissions
{
    use HasTranslationLabel;
    use Translatable;

    protected static ?string $model = ProviderActivity::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?int $navigationSort = 24;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('sections.basic_information'))
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->translateLabel(),
                        Toggle::make('status')->default(1)
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->orderBy('sort'))
            ->reorderable('sort', true)
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('name'),
                IconColumn::make('status')
                    ->boolean()
                    ->action(
                        Action::make('Active')
                            ->label(fn (ProviderActivity $record): string => $record->status ? __('panel.messages.deactivate') : __('panel.messages.activate'))
                            ->disabled(fn (Model $record): bool => ! auth()->user()->can('update', $record))
                            ->requiresConfirmation()
                            ->action(fn (ProviderActivity $record) => $record->toggleStatus())
                    ),
            ])
            ->filters([
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
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProviderActivities::route('/'),
            'create' => CreateProviderActivity::route('/create'),
            'edit' => EditProviderActivity::route('/{record}/edit'),
        ];
    }

    public static function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('menu.settings');
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'view',
            'create',
            'update',
            'reorder',
            'delete',
            'delete_any',
        ];
    }
}
