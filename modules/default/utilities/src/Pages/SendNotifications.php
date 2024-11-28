<?php

namespace App\UtilitiesModule\Pages;

use App\UsersModule\Models\User\Patient;
use App\UsersModule\Models\Users\Customer;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Radio;
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
use App\DefaultPanel\Notifications\SendAdminMessagesNotification;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;

class SendNotifications extends Page implements HasForms {
    use InteractsWithForms, HasPageShield, HasTranslationLabel;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.send-notifications';
//    protected static bool $shouldRegisterNavigation = false;
    public array $notification_title = [];
    public array $notification_body = [];
    public string $notification_type = 'all';
    public array $notifiable = [];
    protected array $rules = [
        'notification_title.ar' => 'required',
        'notification_title.en' => 'required',
        'notification_body.ar' => 'required',
        'notification_body.en' => 'required',
    ];

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


                    Radio::make('notification_type')
                        ->live()
                        ->options([
                            'all' => __('forms.options.all'),
                            'specific' => __('forms.options.specific'),
                        ])
                        ->default('all'),
                    Select::make("notifiable")
                        ->multiple()
                        ->visible(fn($get) => $get('notification_type') == 'specific')
                        ->options(Customer::pluck('name', 'id'))
                ])
        ];
    }


    public function submit() {

        $this->validate();
        Notification::send(Customer::when($this->notification_type == 'specific', fn($builder) => $builder->whereIn('id', $this->notifiable))->get(), new SendAdminMessagesNotification($this->notification_title, $this->notification_body));
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
            null =>static::getNavigationGroup(),
            static::getUrl() => __('menu.send_notifications'),
        ];
    }

}
