<?php

namespace App\ProviderPanel\Filament\Pages;

use Illuminate\Contracts\Support\Htmlable;
use App\ContentModule\Models\Contact;
use App\ContentModule\Models\ContactType;
use App\DefaultPanel\Enum\ContactSourceEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;

class ContactPage extends Page implements HasForms {
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.pages.content.contact';

    public ?array $data = [];

    protected function getFormSchema(): array {
        return [
            Section::make(__('menu.contact_us'))
                ->schema([
                    TextInput::make('name')
                        ->label(__('forms.fields.name'))
                        ->required(),

                    TextInput::make('email')
                        ->label(__('forms.fields.email'))
                        ->email()
                        ->required(),

                    TextInput::make('phone')
                        ->label(__('forms.fields.phone'))
                        ->required(),

                    Select::make('contact_type_id')
                        ->label(__('forms.fields.message_type'))
                        ->options(ContactType::enabled()->pluck('name', 'id')),

                    TextInput::make('subject')
                        ->label(__('forms.fields.subject'))
                        ->required(),

                    Textarea::make('message')
                        ->label(__('forms.fields.message'))
                        ->required()
                        ->rows(5),
                ]),
        ];
    }

    public function submit(): void {
        $data = $this->form->getState();
        
        Contact::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'contact_type_id' => $data['contact_type_id'],
            'title' => $data['subject'],
            'message' => $data['message'],
            'source' => ContactSourceEnum::PROVIDER
        ]);
        
        $this->form->fill();
        
        Notification::make()
            ->title(__('panel.messages.success'))
            ->success()
            ->send();
    }

    public function getTitle(): string|Htmlable {
        return __('menu.contact_us');
    }

    public static function getNavigationGroup(): ?string {
        return __('menu.mashghoul_pages');
    }

    public static function getNavigationLabel(): string {
        return __('menu.contact_us');
    }
}
