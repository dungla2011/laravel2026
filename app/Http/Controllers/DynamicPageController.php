<?php

namespace App\Http\Controllers;

use App\Models\MenuTree;
use App\Models\BlockUi;
use Illuminate\Http\Request;

class DynamicPageController extends Controller
{
    /**
     * Handle dynamic pages from database (MenuTree + BlockUi)
     * This is how WordPress, Joomla, Drupal handle friendly URLs
     *
     * Supports multi-language URLs: /en/privacy, /vi/gioi-thieu, etc.
     *
     * @param Request $request
     * @param string $slug - The URL path like "gioi-thieu", "en/privacy", etc.
     * @return \Illuminate\View\View
     */
    public function show(Request $request, $slug = null)
    {
        // Build full path from request
        $path = '/' . ltrim($request->path(), '/');

        // Check if path starts with language prefix
        $languages = array_keys(\clang1::$enableLanguage);
        $detectedLang = null;
        $pathWithoutLang = $path;

        foreach ($languages as $lang) {
            if (preg_match('#^/' . preg_quote($lang) . '(/|$)#', $path)) {
                $detectedLang = $lang;
                // Remove language prefix from path
                $pathWithoutLang = preg_replace('#^/' . preg_quote($lang) . '#', '', $path);
                if (empty($pathWithoutLang)) {
                    $pathWithoutLang = '/';
                }
                break;
            }
        }

        // Set locale if language detected
        if ($detectedLang) {
            app()->setLocale($detectedLang);

            // Debug for admin
            if (function_exists('isAdminCookie') && isAdminCookie()) {
//                echo "<div style='background: lightblue; padding: 5px; margin: 5px;'>";
//                echo "🌐 Language detected: <strong>{$detectedLang}</strong> | Original path: {$path} | Lookup path: {$pathWithoutLang}";
//                echo "</div>";
            }
        }

        // Find MenuTree by link/slug (without language prefix)
        $menu = MenuTree::where('link', $pathWithoutLang)
            ->where('id_news', '>', 0)
            ->first();

        if (!$menu) {
            abort(404, "Page not found: {$path}");
        }

        // Check if BlockUi exists
        $blockUi = BlockUi::find($menu->id_news);

        if (!$blockUi) {
            abort(404, "Content not found for: {$path}");
        }

        // Debug for admin
        if (function_exists('isAdminCookie') && isAdminCookie()) {
//            echo "<div style='background: yellow; padding: 5px; margin: 5px;'>";
//            echo "📄 Dynamic Page: {$menu->link} (Menu ID: {$menu->id}, Block ID: {$menu->id_news})";
//            if ($detectedLang) {
//                echo " | Current locale: " . app()->getLocale();
//            }
//            echo "</div>";
        }

        // Render view with data
        $data = [
            'id_ui_block' => $menu->id_news,
            'menu' => $menu,
            'blockUi' => $blockUi,
        ];

        return view('public.link_html', $data);
    }
}
