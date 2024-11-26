<?php

namespace App\ProviderPanel\Filament\Pages;

use App\ContentModule\Models\Contact;
use App\DefaultPanel\Enum\ContactSourceEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Attributes\Validate;

class ContactPage extends Page {
    #[Validate('required')]
    public $name;
    #[Validate('required')]
    public $phone;
    #[Validate('required')]
    public $email;
    #[Validate('required')]
    public $subject;
    #[Validate('required')]
    public $message;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.content.contact';

    protected function getFormSchema(): array {
        return [


            TextInput::make('name')
                ->required(),

            TextInput::make('email')
                ->required(),

            TextInput::make('phone')
                ->required(),

            TextInput::make('subject')
                ->required(),

            Textarea::make('message')
                ->required(),

        ];
    }

    public function submit() {
        $this->validate();
        Contact::create([...$this->except(''), 'title' => $this->subject, 'source' => ContactSourceEnum::PROVIDER]);
        $this->reset();
        Notification::make()
            ->title(__('panel.messages.success'))
            ->success()
            ->send();
    }

    public function getTitle(): string {
        return __('menu.contact_us');
    }

    public function get(): \Illuminate\Contracts\Support\Htmlable|string {
        return __('menu.contact_us');
    }

    public static function getNavigationGroup(): ?string {
        return __('menu.pages');
    }

    public static function getNavigationLabel(): string {
        return __('menu.contact_us');
    }


}
