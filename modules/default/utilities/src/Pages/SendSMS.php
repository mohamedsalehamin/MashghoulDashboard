<?php

namespace App\UtilitiesModule\Pages;

use Filament\Schemas\Components\Section;
use Filament\Notifications\Notification;
use App\DefaultPanel\Lib\SMS;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;


class SendSMS extends Page implements HasForms {
    use HasPageShield, HasTranslationLabel, InteractsWithForms, NotificationChannels;

    protected static ?int $navigationSort = 2;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected string $view = 'filament.pages.send-sms';

    public string $sms_body;

    public array $notifiable = [];

    protected array $rules = [
        'sms_body' => 'required',
    ];


    protected function getFormSchema(): array {
        return [
            Section::make('send_sms')
//                ->description(__('panel.messages.send_sms_description'))
                ->schema([
                    Textarea::make('sms_body')
                        ->label(__('forms.fields.message_body'))
                        ->required()
                        ->rows(10)
                        ->translateLabel(),
                    ...$this->getFormComponents(),
                    Select::make('notifiable')
                        ->multiple()
                        ->required()
                        ->visible(fn($get) => $get('notification_type') == 'specific')
                        ->options(fn() => $this->getUsers()->mapWithKeys(fn($record) => [$record->id => $record->name . '-' . $record->phone])),
                ]),
        ];
    }

    public function submit() {
        $this->validate();
        foreach ($this->getUsers() as $user) {
            SMS::make($user->phone, $this->sms_body, 'SmSupMrk-AD')->send();
        }
        // $this->reset($this->except(''));
        $this->resetExcept('');

        Notification::make()->title(__('panel.messages.success'))
            ->body(__('panel.messages.sms_sent_successfully'))
            ->success()
            ->send();

    }

    public function getHeading(): string|Htmlable {
        return __('sections.send_sms');
    }

    public function getTitle(): string|Htmlable {
        return __('sections.send_sms');
    }

    public static function getNavigationGroup(): ?string {
        return __('menu.notifications');
    }

    public static function getNavigationLabel(): string {
        return __('menu.send_sms');
    }

    public function getBreadcrumbs(): array {
        return [
            null => static::getNavigationGroup(),
            static::getUrl() => __('menu.send_sms'),
        ];
    }
}
