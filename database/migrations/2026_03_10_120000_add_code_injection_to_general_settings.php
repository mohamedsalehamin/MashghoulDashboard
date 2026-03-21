<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.code_before_end_head_tag', null);
        $this->migrator->add('general.code_after_body_tag', null);
        $this->migrator->add('general.code_before_end_body_tag', null);
    }
};
