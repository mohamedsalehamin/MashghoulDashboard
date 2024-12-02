<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void {
        $this->migrator->add('general.reservations_fess', "10");
        $this->migrator->add('general.app_percentage', "10");
        $this->migrator->add('general.reservation_flow', 'all');
        $this->migrator->add('general.enabled_free_fees_in_first_reservation', false);
        $this->migrator->add('general.points', []);
    }
};
