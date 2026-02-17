<?php

namespace App\UtilitiesModule\Pages;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;

trait NotificationChannels {
    public string $notification_type = 'all';
    public ?string $user_type = null;
    public ?string $target_type = null;
    public ?string $date_from = null;
    public ?string $date_to = null;
    public array $notifiable = [];

    public function getFormComponents(): array {
        return [
            Select::make('user_type')
                ->live()
                ->required()
                ->options([
                    'provider' => __('sections.providers'),
                    'customer' => __('sections.customers'),
                ]),

            Select::make('target_type')
                ->helperText(__("forms.fields.notified_users_count") . $this->getUsers()->count())
                ->live()
                ->required()
                ->options([
                    'customer_of_current_month' => __('forms.fields.registered_in_last_current_month'),
                    'active_customers' => __('forms.fields.active'),
                    'in_specific_period' => __('forms.fields.in_specific_period'),
                ]),
            DatePicker::make('date_from')
                ->live()
                ->required(fn($get) => $get('target_type') == 'in_specific_period')
                ->visible(fn($get) => $get('target_type') == 'in_specific_period')
                ->label(__('forms.fields.from')),
            DatePicker::make('date_to')
                ->live()
                ->required(fn($get) => $get('target_type') == 'in_specific_period')
                ->visible(fn($get) => $get('target_type') == 'in_specific_period')
                ->label(__('forms.fields.to')),
            Select::make('notification_type')
                ->live()
                ->options([
                    'all' => __('forms.options.all'),
                    'specific' => __('forms.options.specific'),
                ])
                ->default('all'),


        ];
    }


    public function getUsers() {
        return User::whereHas('roles', fn($builder) => $builder->where('name', $this->user_type))
            ->when($this->notification_type == 'specific' && count($this->notifiable), fn($builder) => $builder->whereIn('id', $this->notifiable))
            ->when($this->target_type == 'customer_of_current_month', fn($builder) => $builder->whereMonth('created_at', now()->month))
            ->when($this->target_type == 'active_customers', fn($builder) => $builder->where('active', 1))
            ->when($this->target_type == 'in_specific_period', fn($builder) => $builder->whereBetween('created_at', [$this->date_from, $this->date_to]))
            ->get();
    }
    
}
