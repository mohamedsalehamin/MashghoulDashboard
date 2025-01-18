<?php

namespace App\UtilitiesModule\Pages;

use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\Mail\SendEmailNotification;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Mail;


class SendEmail extends Page implements HasForms {
    use HasPageShield, HasTranslationLabel, InteractsWithForms, NotificationChannels;

    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-at-symbol';

    protected static string $view = 'filament.pages.send-email';

    public string $titlee = '';
    public string $messagee = '';

    public array $notifiable = [];


    protected function getFormSchema(): array {
        return [
            Section::make('send_email')
//                ->description(__('panel.messages.send_email_description'))
                ->schema([
                    TextInput::make('titlee')
                        ->label(__('forms.fields.address'))
                        ->required(),
                    RichEditor::make('messagee')
                        ->label(__('forms.fields.message_body'))
                        ->required()
                        ->translateLabel(),
                    ...$this->getFormComponents(),
                    Select::make('notifiable')
                        ->multiple()
                        ->required()
                        ->visible(fn($get) => $get('notification_type') == 'specific')
                        ->options(fn() => $this->getUsers()->mapWithKeys(fn($record) => [$record->id => $record->name . '-' . $record->email])),
                ]),
        ];
    }

    public function submit() {
        $this->validate();
        Mail::to(User::findMany($this->notifiable)->pluck("email")->toArray())
            ->send(new SendEmailNotification($this->titlee, $this->messagee));

        $this->resetExcept('');
        \Filament\Notifications\Notification::make()->title(__('panel.messages.success'))
            ->body(__('panel.messages.sms_email_successfully'))
            ->success()
            ->send();

    }

    public function getHeading(): string|Htmlable {
        return __('sections.send_email');
    }

    public function getTitle(): string|Htmlable {
        return __('sections.send_email');
    }

    public static function getNavigationGroup(): ?string {
        return __('menu.notifications');
    }

    public static function getNavigationLabel(): string {
        return __('menu.send_email');
    }

    public function getBreadcrumbs(): array {
        return [
            null => static::getNavigationGroup(),
            static::getUrl() => __('menu.send_email'),
        ];
    }


}
