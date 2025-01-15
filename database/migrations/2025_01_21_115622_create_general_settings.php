<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.tax_number', '1234567890');
        $this->migrator->add('general.commercial_register', '1234567890');
    }
};
