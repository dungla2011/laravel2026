<?php

/**
 * Locale Helper Functions
 *
 * Giúp xử lý URL đa ngôn ngữ với optional locale prefix
 */

if (!function_exists('current_locale')) {
    /**
     * Get current application locale
     *
     * @return string
     */
    function current_locale() {
        return app()->getLocale();
    }
}

if (!function_exists('default_locale')) {
    /**
     * Get default application locale
     *
     * @return string
     */
    function default_locale() {
        return \clang1::getDefaultLanguage();
    }
}

if (!function_exists('localized_url')) {
    /**
     * Generate URL with locale prefix
     *
     * @param string $routeName Route name
     * @param array $params Route parameters
     * @param string|null $locale Locale code (null = current locale)
     * @return string
     */
    function localized_url($routeName, $params = [], $locale = null) {
        $locale = $locale ?? current_locale();
        $defaultLocale = default_locale();

        // If default locale, don't add locale parameter
        if ($locale === $defaultLocale) {
            return route($routeName, $params);
        }

        // Add locale to parameters
        return route($routeName, array_merge(['locale' => $locale], $params));
    }
}

if (!function_exists('switch_locale')) {
    /**
     * Get current page URL with different locale
     *
     * @param string $locale Target locale code
     * @return string
     */
    function switch_locale($locale) {
        $route = request()->route();
        $defaultLocale = default_locale();
        $currentPath = request()->path();
        $currentLocale = current_locale();
        $queryParams = request()->query(); // Get all query parameters
        
        if (!$route) {
            $url = url($locale === $defaultLocale ? '/' : "/$locale");
            return $queryParams ? $url . '?' . http_build_query($queryParams) : $url;
        }

        // Get route name and parameters
        $routeName = $route->getName();
        $params = $route->parameters();

        // Method 1: Try to use named routes with .localized suffix
        if ($routeName) {
            // Remove .localized suffix if exists
            $baseRouteName = str_replace('.localized', '', $routeName);
            
            // Build new route name
            $newRouteName = $locale === $defaultLocale 
                ? $baseRouteName 
                : $baseRouteName . '.localized';
            
            // Update locale parameter
            if ($locale === $defaultLocale) {
                unset($params['locale']);
            } else {
                $params['locale'] = $locale;
            }

            try {
                $url = route($newRouteName, $params);
                // Append query parameters
                return $queryParams ? $url . '?' . http_build_query($queryParams) : $url;
            } catch (\Exception $e) {
                // Route name doesn't exist, fallback to path manipulation
            }
        }

        // Method 2: Manual path manipulation for dynamic/catch-all routes
        // Remove ALL locale prefixes from path (including default locale)
        $pathWithoutLocale = $currentPath;
        $supportedLocales = supported_locales();
        
        foreach ($supportedLocales as $localePrefix) {
            // Check if path starts with /locale/ or is exactly /locale
            if (strpos($currentPath, $localePrefix . '/') === 0) {
                $pathWithoutLocale = substr($currentPath, strlen($localePrefix) + 1);
                break;
            } elseif ($currentPath === $localePrefix) {
                $pathWithoutLocale = '';
                break;
            }
        }
        
        // Add new locale prefix if needed
        if ($locale === $defaultLocale) {
            $url = url($pathWithoutLocale ?: '/');
        } else {
            $url = url($locale . '/' . $pathWithoutLocale);
        }
        
        // Append query parameters
        return $queryParams ? $url . '?' . http_build_query($queryParams) : $url;
    }
}

if (!function_exists('locale_route')) {
    /**
     * Alias for localized_url()
     *
     * @param string $routeName
     * @param array $params
     * @param string|null $locale
     * @return string
     */
    function locale_route($routeName, $params = [], $locale = null) {
        return localized_url($routeName, $params, $locale);
    }
}

if (!function_exists('is_current_locale')) {
    /**
     * Check if given locale is current locale
     *
     * @param string $locale
     * @return bool
     */
    function is_current_locale($locale) {
        return current_locale() === $locale;
    }
}

if (!function_exists('supported_locales')) {
    /**
     * Get list of supported locales
     *
     * @return array
     */
    function supported_locales() {
        return \clang1::getLanguageListKey();
    }
}

if (!function_exists('locale_name')) {
    /**
     * Get locale display name
     *
     * @param string $locale Locale code
     * @return string
     */
    function locale_name($locale) {
        $names = [
            'vi' => 'Tiếng Việt',
            'en' => 'English',
            'ja' => '日本語',
            'ko' => '한국어',
            'zh' => '中文',
            'fr' => 'Français',
            'de' => 'Deutsch',
            'es' => 'Español',
        ];

        return $names[$locale] ?? strtoupper($locale);
    }
}

if (!function_exists('locale_flag')) {
    /**
     * Get locale flag emoji or icon class
     *
     * @param string $locale
     * @return string
     */
    function locale_flag($locale) {
        $flags = [
            'vi' => '🇻🇳',
            'en' => '🇺🇸',
            'ja' => '🇯🇵',
            'ko' => '🇰🇷',
            'zh' => '🇨🇳',
            'fr' => '🇫🇷',
            'de' => '🇩🇪',
            'es' => '🇪🇸',
        ];

        return $flags[$locale] ?? '🌐';
    }
}
