<?php

namespace App\ContentModule\Resources;

use App\ContentModule\Models\JoinRequest;
use App\ContentModule\Resources\JoinRequestResource\Pages;
use App\DefaultPanel\Enum\JoinRequestEnum;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\UsersModule\Models\Users\Provider;
use App\UsersModule\Resources\ProviderResource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use libphonenumber\PhoneNumberType;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class JoinRequestResource extends Resource {
    use HasTranslationLabel;

    protected static ?string $model = JoinRequest::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-bars-arrow-up';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                SpatieMediaLibraryFileUpload::make('avatar')
                    ->columnSpan(2)
                    ->nullable(),
                SpatieMediaLibraryFileUpload::make('attachments')
                    ->collection('attachments')
                    ->columnSpan(2)
                    ->nullable(),
                TextInput::make('first_name')
                    ->columnSpan(1)
                    ->required(),
                TextInput::make('last_name')
                    ->columnSpan(1)
                    ->required(),
                TextInput::make('email')
                    ->required()
                    ->email()
                    ->autocomplete('off')
                    ->unique(ignoreRecord: true),
                TextInput::make('phone')
                    ->prefix('+966')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->autocomplete('off'),
                Select::make('zone_id')
                    ->options(Zone::get()->pluck('name', 'id')),
                // TextInput::make('address'),
                TextInput::make('password')
                    ->password()
                    ->required(fn(string $operation): bool => $operation === 'create')
                    ->confirmed()
                    ->autocomplete('new-password'),

                TextInput::make('password_confirmation')
                    ->password()
                    ->required(fn(string $operation): bool => $operation === 'create')
                    ->autocomplete('off'),

            ]);
    }

    public static function table(Table $table): Table {

        return $table
            ->columns([
                TextColumn::make('id')->searchable(),
                TextColumn::make('first_name')->searchable(),
                TextColumn::make('last_name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('phone')->searchable(),
                TextColumn::make('gender')
                    ->formatStateUsing(fn($record) => __("panel.enums.{$record->gender}"))
                    ->searchable(),

                TextColumn::make('created_at')
                    ->dateTime(),
                TextColumn::make('status')
                    ->badge()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('accept')
                    ->visible(fn($record) => $record->status === JoinRequestEnum::PENDING)
                    ->form([
                        TextInput::make('data.first_name')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($set, $get) => $set('name', $get('data.first_name') . ' ' . $get('data.last_name')))
                            ->required()
                            ->minLength(3),

                        TextInput::make('data.last_name')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($set, $get) => $set('name', $get('data.first_name') . ' ' . $get('data.last_name')))
                            ->minLength(3)
                            ->required(),

                        Hidden::make('name'),

                        PhoneInput::make('phone')
                            ->required()
                            ->onlyCountries(['SA', 'EG'])
                            ->validateFor(
                                type: PhoneNumberType::MOBILE,
                                lenient: true
                            )
                            ->unique(ignoreRecord: true)
                            ->displayNumberFormat(PhoneInputNumberType::E164),


                        TextInput::make('email')
//                        ->required()
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->autocomplete("off"),

                        TextInput::make('password')
                            ->password()
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->confirmed()
                            ->autocomplete("new-password"),

                        TextInput::make('password_confirmation')
                            ->password()
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->autocomplete("off"),

                        Select::make('gender')
                            ->required()
                            ->options([
                                'male' => __("panel.enums.male"),
                                'female' => __("panel.enums.female"),
                            ]),


                    ])
                    ->fillForm(fn($record) => [
                        'data' => [
                            'first_name' => $record->first_name,
                            'last_name' => $record->last_name,
                        ],
                        'email' => $record->email,
                        'phone' => $record->phone,
                        'gender' => $record->gender
                    ])
                    ->label(__('forms.actions.accept'))
                    ->icon('heroicon-o-check')
                    ->action(function ($record, $data) {
                        $data['password'] = $record->password;
                        $provider = Provider::create([
                            ...$data,
                            'name'=>data_get($data['data'],'first_name')." ".data_get($data['data'],'last_name'),
                        ]);
                        $record->update(['status' => JoinRequestEnum::ACCEPTED]);
                        FilamentNotification::make()
                            ->success()
                            ->title(__('panel.messages.success'))
                            ->body(__('panel.messages.accept_request'))
                            ->persistent()
                            ->send();
                        // $designer->addMediaFromUrl($record->getFirstMediaUrl())->toMediaCollection();

                    }),
                Tables\Actions\Action::make('reject')
                    ->visible(fn($record) => $record->status === JoinRequestEnum::PENDING)
                    ->requiresConfirmation()
                    ->label(__('forms.actions.reject'))
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->action(fn($record) => $record->update(['status' => JoinRequestEnum::REJECTED])),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()->exports([
                        ExcelExport::make('CSV')
                            ->fromTable()
                            ->withFilename(fn() => static::getPluralLabel() . '-' . now()->format('Y-m-d'))
                            ->withWriterType(\Maatwebsite\Excel\Excel::XLSX),
                    ]),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist {
        return $infolist->schema([
            \Filament\Infolists\Components\Section::make('basic_data')->schema([
                TextEntry::make('first_name'),
                TextEntry::make('last_name'),
                TextEntry::make('phone'),
                TextEntry::make('email'),
                TextEntry::make('created_at'),
                TextEntry::make('status')->columnSpan(1)->badge(),

            ])->columns(3),
        ]);
    }

    public static function getRelations(): array {
        return [
            //
        ];
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListJoinRequests::route('/'),
            //            'create' => Pages\CreateJoinRequest::route('/create'),
            //            'edit' => Pages\EditJoinRequest::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string {
        return static::getModel()::count();
    }

    public static function getNavigationGroup(): ?string {
        return __('menu.crew');
    }
}
