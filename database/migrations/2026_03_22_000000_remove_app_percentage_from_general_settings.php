<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->delete('general.app_percentage');
    }

    public function down(): void
    {
        $this->migrator->add('general.app_percentage', '10');
    }
};
