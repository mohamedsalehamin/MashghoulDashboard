<?php

namespace App\DefaultPanel\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

class TranslatableRequired implements Rule
{
    private $missingLocales = [];

    public function passes($attribute, $value)
    {
       

        // Get the components data from the request
        $components = request()->input('components.0.snapshot');
        if (!$components) {
            return false;
        }

        // Decode the snapshot data
        $snapshot = json_decode($components, true);
        if (!isset($snapshot['data'])) {
            return false;
        }

        $data = $snapshot['data'];
        
        // Get the active locale
        $activeLocale = $data['activeLocale'] ?? 'en';
        
        // Get other locale data
        $otherLocaleData = $data['otherLocaleData'] ?? [];
        
        // Build the full translatable array
        $translatableData = [];
        $translatableData[$activeLocale] = $value;
        
        // Add other locale data
        foreach ($otherLocaleData as $localeData) {
            if (is_array($localeData)) {
                foreach ($localeData as $locale => $values) {
                    if (is_array($values) && isset($values[0]['name'])) {
                        $translatableData[$locale] = $values[0]['name'];
                    }
                }
            }
        }
        
        // Get required locales from config
        $requiredLocales =  ['en', 'ar'];
        
        // Check if all required locales are present and not empty
        $this->missingLocales = [];
        foreach ($requiredLocales as $locale) {
            if (!isset($translatableData[$locale]) || empty($translatableData[$locale])) {
                $this->missingLocales[] = $locale;
            }
        }

        return empty($this->missingLocales);
    }

    public function message()
    {
        $locales = implode(', ', array_map(function($locale) {
            return $locale === 'en' ? __('panel.languages.english') : __('panel.languages.arabic');
        }, $this->missingLocales));
        
        // Get the field name from the attribute
        $fieldName = $this->getFieldName();
        
        return __('validation.translatable_required.missing', [
            'attribute' => $fieldName,
            'locales' => $locales
        ]);
    }

    protected function getFieldName() {
        // Get the field name from the components data
        $components = request()->input('components.0.snapshot');
        if ($components) {
            $snapshot = json_decode($components, true);
            if (isset($snapshot['data']['data']) && is_array($snapshot['data']['data'])) {
                foreach ($snapshot['data']['data'] as $item) {
                    if (is_array($item)) {
                        // Get the first key which is the field name
                        $fieldName = key($item);
                        if ($fieldName) {
                            // Try to get the translation from forms.fields
                            $translation = __("forms.fields.{$fieldName}");
                            return $translation === "forms.fields.{$fieldName}" ? $fieldName : $translation;
                        }
                    }
                }
            }
        }
        
        // Fallback to 'name' if no field name is found
        return __('forms.fields.name');
    }
} 