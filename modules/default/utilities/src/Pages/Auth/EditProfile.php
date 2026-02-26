<?php

namespace App\UtilitiesModule\Pages\Auth;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Concerns\HasRoutes;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Support\Enums\Width;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Password;

class EditProfile extends Page implements HasForms {
    use HasRoutes;
    use InteractsWithForms;
    use HasPageShield;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.auth.edit-profile';

    public ?string $name = '';
    public ?string $email = '';
    public ?string $password = '';
    public ?string $passwordConfirmation = '';

    public static function getLabel(): string {
        return __('menu.edit_profile');
    }

    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    public static function routes(Panel $panel): void {
        $slug = static::getSlug();

        Route::get("/{$slug}", static::class)
            ->middleware(static::getRouteMiddleware($panel))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($panel))
            ->name('profile');
    }

    public static function getRouteMiddleware(Panel $panel): string|array {
        return [
            ...(static::isEmailVerificationRequired($panel) ? [static::getEmailVerifiedMiddleware($panel)] : []),
            ...Arr::wrap(static::$routeMiddleware),
        ];
    }

    public function mount(): void {
        $user = $this->getUser();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function getUser(): Authenticatable & Model {
        $user = Filament::auth()->user();

        if (!$user instanceof Model) {
            throw new Exception('The authenticated user object must be an Eloquent model to allow the profile page to update it.');
        }

        return $user;
    }

    protected function getFormSchema(): array {
        return [
            Section::make(__('menu.edit_profile'))
                ->schema([
                    TextInput::make('name')
                        ->label(__('filament-panels::pages/auth/edit-profile.form.name.label'))
                        ->required()
                        ->maxLength(255)
                        ->autofocus(),

                    TextInput::make('email')
                        ->label(__('filament-panels::pages/auth/edit-profile.form.email.label'))
                        ->email()
                        ->required()
                        ->maxLength(255),

                    TextInput::make('password')
                        ->label(__('filament-panels::pages/auth/edit-profile.form.password.label'))
                        ->password()
                        ->rule(Password::default())
                        ->autocomplete('new-password')
                        ->dehydrated(fn($state): bool => filled($state))
                        ->dehydrateStateUsing(fn($state): string => Hash::make($state))
                        ->live(debounce: 500)
                        ->same('passwordConfirmation'),

                    TextInput::make('passwordConfirmation')
                        ->label(__('filament-panels::pages/auth/edit-profile.form.password_confirmation.label'))
                        ->password()
                        ->requiredWith('password')
                        ->visible(fn(Get $get): bool => filled($get('password')))
                        ->dehydrated(false),
                ]),
        ];
    }

    public function save(): void {
        try {
            $data = $this->form->getState();

            $updateData = [
                'name' => $data['name'],
                'email' => $data['email'],
            ];

            if (!empty($data['password'])) {
                $updateData['password'] = $data['password'];
            }

            $this->getUser()->update($updateData);
        } catch (Halt $exception) {
            return;
        }

        if (request()->hasSession() && !empty($data['password'])) {
            request()->session()->put([
                'password_hash_' . Filament::getAuthGuard() => $data['password'],
            ]);
        }

        $this->password = '';
        $this->passwordConfirmation = '';

        Notification::make()
            ->success()
            ->title(__('filament-panels::pages/auth/edit-profile.notifications.saved.title'))
            ->send();
    }

    public function getTitle(): string|Htmlable {
        return static::getLabel();
    }

    public static function getSlug(?Panel $panel = null): string {
        return static::$slug ?? 'profile';
    }

    public function hasLogo(): bool {
        return false;
    }
}
