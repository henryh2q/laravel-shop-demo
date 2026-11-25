<?php

namespace App\Models\Concerns;

/**
 * Minimal JSON-column translations: {"en": "...", "ja": "...", "vi": "..."}.
 * Falls back to the app fallback locale, then to the first non-empty value.
 */
trait HasTranslations
{
    public function translate(string $attribute, ?string $locale = null): string
    {
        $values = $this->{$attribute} ?? [];
        $locale ??= app()->getLocale();

        return $values[$locale]
            ?? $values[config('app.fallback_locale')]
            ?? (collect($values)->first(fn ($v) => filled($v)) ?? '');
    }
}
