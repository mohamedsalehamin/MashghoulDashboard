<?php

namespace App\UsersModule\Resources\RoleResource\Pages;

use BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles as ShieldListRoles;
use Filament\Actions\CreateAction;
use Filament\Tables\Table;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Builder;

class ListRoles extends ShieldListRoles
{
    public function table(Table $table): Table
    {
        $table = parent::table($table);

        // Hide specific roles from the list
        $excludedRoles = ['panel_user', 'customer', 'provider'];

        return $table->modifyQueryUsing(function (Builder $query) use ($excludedRoles) {
            $query->whereNotIn('name', $excludedRoles);
        });
    }
    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }
}

