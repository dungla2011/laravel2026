<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;

/**
 * Translation Helper Functions for i18n
 * Usage in Blade: {{ trans_field('field_name') }}
 * Usage in PHP: TranslationHelper::transField('field_name')
 */
class TranslationHelper
{
    /**
     * Get translated field label from database
     * 
     * @param string $fieldName Field name (e.g., 'user_name', 'email')
     * @param string|null $locale Override current locale
     * @return string Translated label or original field name
     */
    public static function transField($fieldName, $locale = null)
    {
        $locale = $locale ?? App::getLocale();
        
        // Find field in model_meta_infos
        $metaInfo = \App\Models\ModelMetaInfo::where('field', $fieldName)->first();
        
        if ($metaInfo && !empty($metaInfo->translations)) {
            $translations = $metaInfo->translations; // Already array from $casts
            
            if (is_array($translations) && isset($translations[$locale])) {
                return $translations[$locale];
            }
        }
        
        // Fallback: Try Laravel lang files
        $trans = trans("fields.{$fieldName}", [], $locale);
        if ($trans !== "fields.{$fieldName}") {
            return $trans;
        }
        
        // Last resort: humanize field name
        return ucfirst(str_replace('_', ' ', $fieldName));
    }
    
    /**
     * Get translated menu name from database
     * 
     * @param int|string $menuId Menu ID or name
     * @param string|null $locale Override current locale
     * @return string Translated menu name
     */
    public static function transMenu($menuId, $locale = null)
    {
        $locale = $locale ?? App::getLocale();
        
        // Find menu in menu_tree
        $menu = is_numeric($menuId) 
            ? \App\Models\MenuTree::find($menuId)
            : \App\Models\MenuTree::where('name', $menuId)->first();
        
        if ($menu) {
            return $menu->getTranslatedName($locale);
        }
        
        return is_numeric($menuId) ? "Menu #{$menuId}" : $menuId;
    }
    
    /**
     * Get all available languages
     * 
     * @return array ['en' => 'English', 'vi' => 'Tiếng Việt', ...]
     */
    public static function getLanguages()
    {
        require_once app_path('common.php');
        return \clang1::getLanguageList();
    }
    
    /**
     * Get current locale
     * 
     * @return string Current locale code (e.g., 'en', 'vi')
     */
    public static function getCurrentLocale()
    {
        return App::getLocale();
    }
    
    /**
     * Check if locale is RTL (Right-to-Left)
     * 
     * @param string|null $locale
     * @return bool
     */
    public static function isRTL($locale = null)
    {
        $locale = $locale ?? App::getLocale();
        $rtlLanguages = ['ar', 'he', 'fa', 'ur'];
        
        return in_array($locale, $rtlLanguages);
    }
    
    /**
     * Get language flag icon class
     * 
     * @param string $locale Language code
     * @return string Flag icon class (e.g., 'flag-icon-us')
     */
    public static function getFlagIcon($locale)
    {
        require_once app_path('common.php');
        $flagMap = \clang1::$flagMap;
        
        return isset($flagMap[$locale]) 
            ? "flag-icon-{$flagMap[$locale]}" 
            : 'flag-icon-un';
    }
}
