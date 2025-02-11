<?php

namespace App\ContentModule\Widgets;
use App\ContentModule\Models\Contact;
use App\ContentModule\Models\ContactType;
use App\DefaultPanel\Filters\DateFilter;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class Contacts extends BaseWidget {
    use HasWidgetShield;

    protected static ?int $sort = 11;

    public function table(Table $table): Table {
        return $table
            ->heading(__('sections.latest_contacts'))
            ->query(
                Contact::where('seen', 0)->orderBy('created_at', 'desc')->limit(10)
            )
            ->filters([
                Tables\Filters\SelectFilter::make('contact_type_id')
                    ->label(__('forms.fields.message_type'))
                    ->searchable()
                    ->options(ContactType::pluck('name', 'id')),
                DateFilter::make()
            ])
            ->columns([
                TextColumn::make('id')->rowIndex()->toggleable(false),

                TextColumn::make('name')
                    ->state(fn(Model $record): string => $record->user->name ?? $record->name)
                    ->searchable(),
                TextColumn::make('email')
                    ->state(fn(Model $record): string => $record->user->email ?? $record->email)
                    ->copyable()
                    ->copyMessage('Email address copied')
                    ->copyMessageDuration(1500)
                    ->searchable(),

                TextColumn::make('phone')
                    ->state(fn(Model $record): string => $record->user->phone ?? $record->phone??'')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Phone address copied')
                    ->copyMessageDuration(1500),


            ])->actions([
                Action::make('seen')
                    ->visible(fn(Model $record) => !$record->seen)
                    ->label(__('forms.fields.mark_as_seen'))
                    ->action(fn(Model $record) => $record->update(['seen' => 1])),

                Tables\Actions\ViewAction::make()
                    ->modalHeading(__('menu.contact'))
                    ->form([
                            TextInput::make('title')
                                ->required()
                                ->autocomplete("off"),

                            Textarea::make('message')
                                ->rows(10),


                            TextInput::make('name')
                                ->required(),

                            TextInput::make('email')
                                ->required()
                                ->email()
                                ->autocomplete("off")
                                ->unique(ignoreRecord: true),

                            TextInput::make('phone')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->autocomplete("off"),

                            Toggle::make('seen')->default(1)
                                ->onColor('success')
                                ->offColor('danger')
                        ]
                    ),
            ]);
    }


    public function getTableHeading(): ?string {
        return __('sections.latest_contacts');
    }
}
