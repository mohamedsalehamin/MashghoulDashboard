<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('third_party.firebase_server_key', '');
        $this->migrator->add('third_party.firebase_server_id', '');
        $this->migrator->add('third_party.google_map_key', '');
    }
};
