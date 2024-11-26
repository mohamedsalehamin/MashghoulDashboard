<?php

namespace App\ProviderPanel\Filament\Resources;

use App\ProviderPanel\Filament\Resources\NotificationResource\Pages\ListNotifications;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\Models\Notification;

class NotificationResource extends Resource {

    use HasTranslationLabel;

    protected static ?string $model = Notification::class;
    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form {
        return $form;
    }

    public static function table(Table $table): Table {
        return $table
            ->modifyQueryUsing(fn($query) => $query->where('notifiable_id', auth()->id()))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('forms.fields.text'))
                    ->searchable(false)
                    ->description(fn(Model $record): string => $record->body)


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn(Model $record) => $record->url),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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

    public static function can(string $action, ?Model $record = null): bool {

        return true;
    }


}
