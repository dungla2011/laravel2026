<?php
// app/Http/Middleware/SetLocale.php
namespace App\Http\Middleware;

use App\Http\Controllers\SiteMngController;
use App\Models\SiteMng;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use LadLib\Common\UrlHelper1;

// Import clang1 class
require_once app_path('common.php');

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * Priority (from highest to lowest):
     * 1. URL locale parameter (/en/pricing, /ja/login) - User explicitly chose
     * 2. User's saved language preference (if logged in)
     * 3. Session locale (persisted from previous selection)
     * 4. Config default (vi)
     *
     * Note: Browser Accept-Language is NOT used to respect user's explicit choices
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = null;

        // Priority 1: URL locale parameter (HIGHEST - user explicitly navigated here)
        if ($request->route('locale')) {
            $locale = $request->route('locale');
            Session::put('locale', $locale); // Remember for non-prefixed URLs
        }

        // Set locale từ header, cho API
        $locale = $request->header('X-Locale') ?? '';

        if($locale && (UrlHelper1::fullUrlIncludeString("/list"))){
//            die("Locale : $locale");
        }


        // Priority 2: User's saved language preference (if logged in & no URL locale)
        if (!$locale && auth()->check() && !empty(auth()->user()->language)) {
            $locale = auth()->user()->language;
        }


        if (!$locale){
//            $locale = SiteMng::getForceLanguage();
        }

        // Priority 3: Session locale (from previous selection)
        if (!$locale && Session::has('locale')) {
            $locale = Session::get('locale');
        }

        //Nếu có primary lang thì lấy


        //Lấy locale từ trình duyệt nếu co:
        if (!$locale) {
            $browserLocale = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);
            $supportedLanguages = \clang1::getLanguageListKey();
            if (in_array($browserLocale, $supportedLanguages)) {
                $locale = $browserLocale;
            }
        }

        // Priority 4: Default from config
        if (!$locale) {
            $locale = config('app.locale', 'vi');
        }

        // Validate against supported languages
        $supportedLanguages = \clang1::getLanguageListKey();
        if (!in_array($locale, $supportedLanguages)) {
            $locale = config('app.locale', 'vi');
        }

        // Set application locale
        App::setLocale($locale);

        // Set Carbon locale for date formatting
        if (class_exists('\Carbon\Carbon')) {
            \Carbon\Carbon::setLocale($locale);
        }

        // Share locale with all views
        view()->share('currentLocale', $locale);

        return $next($request);
    }
}
