<?php

/**
 * I18N Helper Functions
 * Shorthand wrappers for TranslationHelper class
 * Usage in Blade: {{ trans_field('user_name') }}
 */

use App\Helpers\TranslationHelper;

if (!function_exists('trans_field')) {
    /**
     * Get translated field label
     * @param string $fieldName
     * @param string|null $locale
     * @return string
     */
    function trans_field($fieldName, $locale = null)
    {
        return TranslationHelper::transField($fieldName, $locale);
    }
}

if (!function_exists('trans_menu')) {
    /**
     * Get translated menu name
     * @param int|string $menuId
     * @param string|null $locale
     * @return string
     */
    function trans_menu($menuId, $locale = null)
    {
        return TranslationHelper::transMenu($menuId, $locale);
    }
}

if (!function_exists('get_languages')) {
    /**
     * Get all available languages
     * @return array
     */
    function get_languages()
    {
        return TranslationHelper::getLanguages();
    }
}

if (!function_exists('current_locale')) {
    /**
     * Get current locale
     * @return string
     */
    function current_locale()
    {
        return TranslationHelper::getCurrentLocale();
    }
}

if (!function_exists('is_rtl')) {
    /**
     * Check if current locale is RTL
     * @param string|null $locale
     * @return bool
     */
    function is_rtl($locale = null)
    {
        return TranslationHelper::isRTL($locale);
    }
}

if (!function_exists('flag_icon')) {
    /**
     * Get flag icon class for locale
     * @param string $locale
     * @return string
     */
    function flag_icon($locale)
    {
        return TranslationHelper::getFlagIcon($locale);
    }
}

if (!function_exists('localized_route')) {
    /**
     * Generate route URL with current locale prefix (if not default)
     * 
     * This ensures all links maintain the current locale context:
     * - If on /ja/pricing → links become /ja/home, /ja/about, etc.
     * - If on /pricing (vi default) → links become /home, /about, etc.
     * 
     * @param string $name Route name
     * @param array $params Route parameters
     * @param string|null $locale Specific locale (null = use current)
     * @return string
     */
    function localized_route($name, $params = [], $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $defaultLocale = \clang1::getDefaultLanguage();
        
        // If current locale is not default, add locale parameter
        if ($locale !== $defaultLocale) {
            $params = array_merge(['locale' => $locale], $params);
            
            // Try to find .localized route variant first
            if (\Illuminate\Support\Facades\Route::has($name . '.localized')) {
                return route($name . '.localized', $params);
            }
        }
        
        // Fallback to base route name
        try {
            return route($name, $params);
        } catch (\Exception $e) {
            // Route not found, return home
            return url($locale !== $defaultLocale ? "/$locale" : '/');
        }
    }
}

if (!function_exists('get_locale_name')) {
    /**
     * Get full language name for locale code
     * 
     * @param string|null $locale Locale code (null = current)
     * @return string
     */
    function get_locale_name($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $languages = \clang1::getLanguageList();
        
        return $languages[$locale] ?? $locale;
    }
}
