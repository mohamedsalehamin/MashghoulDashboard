<?php

namespace App\ProviderPanel\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\ProviderPanel\Filament\Resources\NotificationResource\Pages\ListNotifications;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\Models\Notification;

class NotificationResource extends Resource {

    use HasTranslationLabel;

    protected static ?string $model = Notification::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-bell-alert';
    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema {
        return $schema;
    }

    public static function table(Table $table): Table {
        return $table
            ->modifyQueryUsing(fn($query) => $query->where('notifiable_id', auth()->id()))
            ->columns([
                TextColumn::make('title')
                    ->label(__('forms.fields.text'))
                    ->searchable(false)
                    ->description(fn(Model $record): string => $record->body)


            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->visible(fn(Model $record) => $record->url !== null)
                    ->url(fn(Model $record) => $record->url),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function getPages(): array {
        return [
            'index' => ListNotifications::route('/'),
        ];
    }



    public static function getNavigationBadge(): ?string {
        return auth()->user()->unreadNotifications()->count();
    }

    public static function getNavigationLabel(): string {
        return __('menu.notifications');
    }


}
