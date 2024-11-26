<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void {
        $this->migrator->add('general.app_logo', '');
        $this->migrator->add('general.app_name', 'John Doe');
        $this->migrator->add('general.app_email', 'john_doe@example.com');
        $this->migrator->add('general.app_address', 'Riyadh, Saudi Arabia');
        $this->migrator->add('general.app_phone', '5123456789');
        $this->migrator->add('general.app_whatsapp', '5123456789');
        $this->migrator->add('general.taxes', 14);

        $this->migrator->add('general.working_days', []);

        $this->migrator->add('general.applications_links', [
            'google_play_link' => 'https://www.google.com',
            'apple_store_link' => 'https://www.apple.com',
        ]);
        $this->migrator->add('general.app_pages', [
            'about_us' => 1,
            'terms_and_conditions' => 2,
            'privacy_policy' => 3,
        ]);
        $this->migrator->add('general.provider_pages', [
            'about_us' => 1,
            'terms_and_conditions' => 2,
            'privacy_policy' => 3,
        ]);
        $this->migrator->add('general.social_links', []);
    }
};
