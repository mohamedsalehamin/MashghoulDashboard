<?php

namespace App\ProviderPanel\Filament\Resources;


use App\DefaultPanel\Enum\ServicesTypeEnum;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\ProviderPanel\Filament\Resources\ServiceResource\Pages\ListServices;
use App\UsersModule\Models\Lab\Service;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\CatalogModule\Models\Subscription;
use Illuminate\Database\Eloquent\Model;

class ServiceResource extends Resource {
    use HasTranslationLabel, Translatable;

    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';
    protected static ?int $navigationSort =2;

    public static function form(Form $form): Form {
        return $form->schema([
            Hidden::make('lab_id')->default(lab()?->user->id),
            Tabs::make('Label')
                ->tabs([
                    Tabs\Tab::make(__('panel.languages.arabic'))
                        ->schema([
                            TextInput::make('name.ar')
                                ->formatStateUsing(fn($record) => $record?->name['ar'] ?? '')
                                ->label(__('forms.fields.name'))
                                ->required(),
                            TextInput::make('description.ar')
                                ->formatStateUsing(fn($record) => $record?->description['ar'] ?? '')
                                ->label(__('forms.fields.description'))
                                ->required(),

                        ]),
                    Tabs\Tab::make(__('panel.languages.english'))
                        ->schema([
                            TextInput::make('name.en')
                                ->formatStateUsing(fn($record) => $record?->name['en'] ?? '')
                                ->label(__('forms.fields.name'))
                                ->required(),
                            TextInput::make('description.en')
                                ->formatStateUsing(fn($record) => $record?->description['en'] ?? '')
                                ->label(__('forms.fields.description'))
                                ->required(),
                        ]),
                ]),

            TextInput::make('price')
                ->suffix(__("forms.suffixes.sar"))
                ->helperText(__("forms.hints.price_include_taxes"))

                ->formatStateUsing(fn($record) => $record?->price?->formatByDecimal())
                ->default(0)
                ->numeric()
                ->required(),

            TextInput::make('sale_price')
                ->formatStateUsing(fn($record) => $record?->sale_price?->formatByDecimal())
                ->helperText(__("forms.hints.price_include_taxes"))

                ->suffix(__("forms.suffixes.sar"))
                ->default(0)
                ->lte('price'),
            Hidden::make('status')->default(1),
        ])->columns(1);
    }

    public static function table(Table $table): Table {
        return $table
            ->modifyQueryUsing(fn($query) => $query->belongsToAuthUser())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->formatStateUsing(fn($record) => $record->name[app()->getLocale()] ?? '')
                    ->toggleable(false),

                Tables\Columns\TextColumn::make('price')->toggleable(false),
                Tables\Columns\TextColumn::make('sale_price')->toggleable(false),
                Tables\Columns\TextColumn::make('created_at')->date()->toggleable(false),

            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\EditAction::make(),


            ])
            ->bulkActions([

            ])
//            ->checkIfRecordIsSelectableUsing(fn(Model $record): bool => !$record->orders()->count())
            ->emptyStateActions([
            ])
            ->striped();
    }


    public static function getPages(): array {
        return [
            'index' => ListServices::route('/'),
        ];
    }


    public static function getNavigationBadge(): ?string {
        return static::getModel()::belongsToAuthUser()->count();
    }

    public static function getGlobalSearchResultTitle(Model $record): string {
        return $record->name;
    }

    public static function can(string $action, ?Model $record = null): bool {
        return true;
    }

    public static function getNavigationLabel(): string {
        return __('menu.analysis');
    }
    public static function getPluralLabel(): ?string {
        return __('menu.analysis');
    }
}
