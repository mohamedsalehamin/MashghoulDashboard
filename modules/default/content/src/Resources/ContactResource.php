<?php

namespace App\ContentModule\Resources;


use Filament\Schemas\Schema;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Maatwebsite\Excel\Excel;
use Filament\Actions\DeleteBulkAction;
use App\ContentModule\Resources\ContactResource\Pages\ListContacts;
use App\ContentModule\Models\ContactType;
use App\DefaultPanel\Enum\ContactSourceEnum;
use App\DefaultPanel\Filters\DateFilter;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Support\Enums\Width;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use App\ContentModule\Models\Contact;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;

class ContactResource extends Resource implements HasShieldPermissions {
    use HasTranslationLabel;

    protected static ?string $model = Contact::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-envelope';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->autocomplete("off"),

                Textarea::make('message')
                    ->rows(10),
                TextInput::make('name')
                    ->required()
                    ->formatStateUsing(fn(Model $record): string => $record->user->name ?? $record->name),


                TextInput::make('email')
                    ->required()
                    ->email()
                    ->autocomplete("off")
                    ->formatStateUsing(fn(Model $record): string => $record->user->email ?? $record->email)
                ,

                TextInput::make('phone')
                    ->required()
                    ->formatStateUsing(fn(Model $record): string => $record->user->phone ?? $record->phone ?? '')
                    ->autocomplete("off"),

                Toggle::make('seen')->default(1)
                    ->onColor('success')
                    ->offColor('danger')
            ])->columns(1);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                TextColumn::make('id')->searchable(),
                TextColumn::make('name')
                    ->state(fn(Model $record): string => $record?->user?->name ?? $record->name ?? '')
                    ->searchable(),


                TextColumn::make('email')
                    ->state(fn(Model $record): string => $record?->user?->email ?? $record->email ?? '')
                    ->copyable()
                    ->copyMessage('Email address copied')
                    ->copyMessageDuration(1500)
                    ->searchable(),

                TextColumn::make('phone')
                    ->state(fn(Model $record): string => $record?->user?->phone ?? $record->phone ?? '')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Phone address copied')
                    ->copyMessageDuration(1500),

                TextColumn::make('type.name')->label(__("forms.fields.message_type")),
                TextColumn::make('title')
                    ->label(__("forms.fields.message_title"))
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('message')
                    ->label(__("forms.fields.message_body"))
                    ->limit(50)
                    ->searchable(),
                IconColumn::make('seen')
                    ->boolean(),


            ])
            ->filters([
                SelectFilter::make('contact_type_id')
                    ->label(__('forms.fields.message_type'))
                    ->searchable()
                    ->options(ContactType::pluck('name', 'id')),
                DateFilter::make()
            ])
            ->recordActions([
                Action::make('seen')
                    ->visible(fn(Model $record) => !$record->seen)
                    ->label(__('forms.fields.mark_as_seen'))
                    ->hidden(fn(Model $record): bool => !auth()->user()->can('update', $record))
                    ->action(fn(Model $record) => $record->update(['seen' => 1])),

                ViewAction::make(),
                DeleteAction::make(),


            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportBulkAction::make()->exports([
                        ExcelExport::make("CSV")
                            ->fromTable()
                            ->withFilename(fn() => static::getPluralLabel() . '-' . now()->format('Y-m-d'))
                            ->withWriterType(Excel::XLSX),


                    ]),


                    DeleteBulkAction::make(),


                ]),

            ])
            ->emptyStateActions([

            ]);
    }

    public static function getRelations(): array {
        return [
            //
        ];
    }

    public static function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    public static function getPages(): array {
        return [
            'index' => ListContacts::route('/'),
        ];
    }

//    public static function getNavigationGroup(): ?string {
//        return __('menu.content');
//    }
    public static function getNavigationBadge(): ?string {
        return static::getModel()::where('seen', 0)->count();
    }

    public static function getNavigationGroup(): ?string {
        return __('menu.content');
    }

    public static function getPermissionPrefixes(): array {
        return [
            'view_any',
            'view',
            'update',
            'delete',
            'delete_any',
        ];
    }
}
