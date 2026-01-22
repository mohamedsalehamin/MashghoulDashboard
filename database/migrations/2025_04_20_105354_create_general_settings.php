<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void 
    {
        $key = 'general.enabled_whatsapp_icon';
        $value = false;
        
        // Check if setting exists before adding
        if (!$this->settingExists($key)) {
            $this->validate($key, $value);
            $this->migrator->add($key, $value);
        } else {
            // Optional: Update existing setting or just skip
            echo "Setting '{$key}' already exists, skipping...\n";
        }
    }
    
    public function down(): void
    {
        $key = 'general.enabled_whatsapp_icon';
        
        if ($this->settingExists($key)) {
            $this->migrator->delete($key);
        }
    }
    
    /**
     * Check if setting exists
     */
    private function settingExists(string $key): bool
    {
        try {
            // Check if setting exists in the database
            $exists = \Illuminate\Support\Facades\DB::table('settings')
                ->where('group', $this->getGroupFromKey($key))
                ->where('name', $this->getNameFromKey($key))
                ->exists();
            
            return $exists;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Extract group from setting key
     */
    private function getGroupFromKey(string $key): string
    {
        return explode('.', $key)[0];
    }
    
    /**
     * Extract name from setting key
     */
    private function getNameFromKey(string $key): string
    {
        $parts = explode('.', $key);
        return implode('.', array_slice($parts, 1));
    }
    
    /**
     * Simple validation method
     */
    private function validate(string $key, $value): void
    {
        if (empty($key) || !str_contains($key, '.')) {
            throw new \InvalidArgumentException("Invalid setting key format: {$key}");
        }
        
        if (!is_bool($value)) {
            throw new \InvalidArgumentException("Setting '{$key}' must be a boolean value");
        }
    }
};