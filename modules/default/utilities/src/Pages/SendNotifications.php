<?php

namespace App\UtilitiesModule\Pages;

use App\DefaultPanel\Notifications\SendAdminMessagesNotification;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Notification;

class SendNotifications extends Page implements HasForms {
    use InteractsWithForms, HasPageShield, HasTranslationLabel, NotificationChannels;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.send-notifications';
//    protected static bool $shouldRegisterNavigation = false;
    public array $notification_title = [];
    public array $notification_body = [];

    public int $users_count = 0;


    protected function getFormSchema(): array {
        return [
            Section::make('send_notifications')
                ->description(__('panel.messages.send_notifications_description'))
                ->schema([
                    Tabs::make('Label')
                        ->tabs([
                            Tabs\Tab::make(__('panel.languages.arabic'))
                                ->schema([
                                    TextInput::make('notification_title.ar')
                                        ->label(__('forms.fields.message_title'))
                                        ->required(),
                                    Textarea::make('notification_body.ar')
                                        ->label(__('forms.fields.message_body'))
                                        ->required()
                                        ->rows(10)
                                        ->translateLabel(),
                                ]),
                            Tabs\Tab::make(__('panel.languages.english'))
                                ->schema([
                                    TextInput::make('notification_title.en')
                                        ->label(__('forms.fields.message_title'))
                                        ->required(),

                                    Textarea::make('notification_body.en')
                                        ->label(__('forms.fields.message_body'))
                                        ->required()
                                        ->rows(10),
                                ]),
                        ]),


                    ...$this->getFormComponents(),
                    Select::make("notifiable")
                        ->live()
                        ->multiple()
                        ->visible(fn($get) => $get('notification_type') == 'specific')
                        ->options(fn($get) => $this->getUsers()->pluck('name', 'id')->toArray()),
                ])
        ];
    }


    public function submit() {
        $this->validate();
        $users = $this->getUsers();
        Notification::send($users, new SendAdminMessagesNotification($this->notification_title, $this->notification_body));
        $this->reset('notification_title', 'notification_body', 'notification_type', 'notifiable');
        \Filament\Notifications\Notification::make()->title(__('panel.messages.success'))
            ->body(__('panel.messages.notifications_sent_successfully'))
            ->success()
            ->send();

    }


    public function getHeading(): string|Htmlable {
        return __('sections.send_notifications');
    }

    public function getTitle(): string|Htmlable {
        return __('sections.send_notifications');
    }

    public static function getNavigationGroup(): ?string {
        return __('menu.notifications');
    }

    public static function getNavigationLabel(): string {
        return __('menu.send_notifications');
    }

    public function getBreadcrumbs(): array {
        return [
            null => static::getNavigationGroup(),
            static::getUrl() => __('menu.send_notifications'),
        ];
    }

}
