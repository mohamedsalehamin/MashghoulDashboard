<?php
namespace App\UtilitiesModule\Pages;
use Filament\Schemas\Components\Section;
use Exception;
use Filament\Notifications\Notification;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\Mail\SendEmailNotification;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Mail;
use Illuminate\Support\Facades\Log;

class SendEmail extends Page implements HasForms {
    use HasPageShield, HasTranslationLabel, InteractsWithForms, NotificationChannels;
    protected static ?int $navigationSort = 3;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-at-symbol';
    protected string $view = 'filament.pages.send-email';
    public string $titlee = '';
    public string $messagee = '';
    public array $notifiable = [];
    
    protected function getFormSchema(): array {
        return [
            Section::make('send_email')
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
        
        Log::info('=== Email sending process started ===');
        Log::info('Mail Config:', [
            'mailer' => config('mail.default'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'username' => config('mail.mailers.smtp.username'),
            'encryption' => config('mail.mailers.smtp.encryption'),
            'from_address' => config('mail.from.address'),
        ]);
        
        $users = $this->getUsers();
        Log::info('Total users found: ' . $users->count());
        
        $validUsers = $users->filter(function($user) {
            return !empty($user->email);
        });
        
        Log::info('Valid users with email: ' . $validUsers->count());
        
        $successCount = 0;
        $failCount = 0;
        
        foreach ($validUsers as $user) {
            try {
                Log::info('Attempting to send email to: ' . $user->email);
                
                Mail::to($user->email)
                    ->send(new SendEmailNotification($this->titlee, $this->messagee));
                
                Log::info('✓ Email sent successfully to: ' . $user->email);
                $successCount++;
                
            } catch (Exception $e) {
                Log::error('✗ Failed to send email to: ' . $user->email);
                Log::error('Error: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                $failCount++;
            }
        }
        
        Log::info('=== Email sending completed ===');
        Log::info("Success: $successCount, Failed: $failCount");
        
        $this->resetExcept('');
        Notification::make()->title(__('panel.messages.success'))
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