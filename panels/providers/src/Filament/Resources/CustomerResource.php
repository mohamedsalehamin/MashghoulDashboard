<?php

namespace App\ProviderPanel\Filament\Resources;

use App\DefaultPanel\Enum\GenderEnum;
use App\DefaultPanel\Enum\ModelStatus;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\ProviderPanel\Filament\Resources\CustomerResource\Pages\ListCustomers;
use App\UsersModule\Models\Users\Customer;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CustomerResource extends Resource {
    use HasTranslationLabel;

    protected static ?string $model = Customer::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form {

        return $form;

    }

    public static function table(Table $table): Table {
        return $table
            ->modifyQueryUsing(fn($query) => $query->whereHas('reservations', fn($q) => $q->where("reservable_id", provider()->id)))
            ->columns([
                TextColumn::make('id')->searchable(),

                TextColumn::make('data.first_name')
                    ->searchable("data->first_name"),
                TextColumn::make('data.last_name')
                    ->searchable("data->last_name"),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('created_at')->searchable()->date(),


            ])
            ->filters([
                Filter::make('ID')
                    ->form([TextInput::make('id'),])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['id'], fn(Builder $query, $date): Builder => $query->where('id', $data['id']));
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['id']) {
                            return null;
                        }

                        return __('forms.fields.id') . " CustomerResource.php" . $data['id'];
                    }),
                SelectFilter::make('gender')
                    ->searchable()
                    ->options(GenderEnum::class),

                SelectFilter::make('active')
                    ->options(ModelStatus::class)
            ]);
    }

    public static function getPages(): array {
        return [
            'index' => ListCustomers::route('/'),

        ];
    }

    public static function getRelations(): array {
        return [


        ];
    }

    public static function getNavigationBadge(): ?string {
        return static::getModel()::whereHas('reservations', fn($q) => $q->where("reservable_id", provider()->id))->count();
    }

    public static function can(string $action, ?Model $record = null): bool {
        return true;
    }

}
