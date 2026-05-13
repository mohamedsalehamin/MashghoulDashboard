<?php

namespace App\Support;

final class ManualRatingNames
{
    public static function all(): array
    {
        static $cache = null;

        if ($cache === null) {
            $path = __DIR__.'/manual_rating_names.json';
            $json = is_readable($path) ? file_get_contents($path) : '[]';
            $cache = json_decode($json, true) ?: [];
        }

        return $cache;
    }

    /**
     * @return array<string, string> option value (index) => label for current locale
     */
    public static function selectOptions(): array
    {
        $key = app()->getLocale() === 'en' ? 'en' : 'ar';
        $out = [];
        foreach (self::all() as $i => $row) {
            $out[(string) $i] = $row[$key] ?? $row['ar'] ?? $row['en'] ?? '';
        }

        return $out;
    }

    /**
     * @return array{ar: string, en: string}|null
     */
    public static function resolve(int|string|null $index): ?array
    {
        if ($index === null || $index === '') {
            return null;
        }

        $all = self::all();
        $i = (int) $index;

        return $all[$i] ?? null;
    }

    public static function indexForStored(?array $stored): ?int
    {
        if (!$stored) {
            return null;
        }

        foreach (self::all() as $i => $row) {
            if (($row['ar'] ?? '') === ($stored['ar'] ?? '')
                && ($row['en'] ?? '') === ($stored['en'] ?? '')) {
                return $i;
            }
        }

        return null;
    }

    public static function labelForLocale(?array $names, ?string $locale = null): ?string
    {
        if (!$names || ! is_array($names)) {
            return null;
        }

        $locale ??= app()->getLocale();

        return $names[$locale] ?? $names['ar'] ?? $names['en'] ?? null;
    }
}
