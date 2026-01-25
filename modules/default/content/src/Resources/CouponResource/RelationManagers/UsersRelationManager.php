<?php

namespace App\ContentModule\Resources\CouponResource\RelationManagers;

use Filament\Schemas\Schema;
use App\UsersModule\Resources\CustomerResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersRelationManager extends RelationManager {
    protected static string $relationship = 'users';

    public function form(Schema $schema): Schema {
        return $schema
            ->components([

            ]);
    }

    public function table(Table $table): Table {
        return $table

            ->heading(__('sections.usages'))
            ->columns([
                TextColumn::make('id')->searchable()
                    ->toggleable(false),

                TextColumn::make('name')
                    ->url(fn ($record) =>CustomerResource::getUrl('edit',[$record->user_id]),true)
                    ->label(__('forms.fields.customer_name'))
                    ->searchable()
                ->toggleable(false),


                TextColumn::make('created_at')
                    ->label(__("forms.fields.used_at"))
                    ->dateTime()->searchable()->toggleable(false),
            ])
            ->emptyStateHeading(__('site.no_data'))

            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->recordActions([
            ])
            ->toolbarActions([
            ]);
    }



}
