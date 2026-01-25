<?php

namespace App\ProviderPanel\Filament\Pages;

use Illuminate\Contracts\Support\Htmlable;
use App\ContentModule\Models\Contact;
use App\ContentModule\Models\ContactType;
use App\DefaultPanel\Enum\ContactSourceEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Actions\Action;

class ContactPage extends Page {
    use InteractsWithFormActions;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.pages.content.contact';

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('data');
    }

    public function form(Schema $schema): Schema {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),

                TextInput::make('email')
                    ->required(),

                TextInput::make('phone')
                    ->required(),
                Select::make('contact_type_id')
                    ->options(ContactType::enabled()->pluck('name', 'id'))
                    ->label(__("forms.fields.message_type")),
                TextInput::make('subject')
                    ->required(),

                Textarea::make('message')
                    ->required(),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('submit')
                ->label(__('forms.actions.send'))
                ->submit('submit'),
        ];
    }

    public function submit() {
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

    public function getTitle(): string {
        return __('menu.contact_us');
    }

    public function get(): Htmlable|string {
        return __('menu.contact_us');
    }

    public static function getNavigationGroup(): ?string {
        return __('menu.mashghoul_pages');
    }

    public static function getNavigationLabel(): string {
        return __('menu.contact_us');
    }


}
