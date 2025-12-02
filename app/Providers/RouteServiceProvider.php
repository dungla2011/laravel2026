<?php

namespace App\Providers;

use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

//        Debugbar::startMeasure('RouteLoadAll');

        //\//ladDebug::addTime(" route_service ", __LINE__);
        $this->routes(function () {
            //\//ladDebug::addTime(" route_service ", __LINE__);
            Route::prefix('api')
                //Nếu để là API, sẽ ko bị check session
//                ->middleware('api')
                //Nếu để là web, sẽ có thể check thêm session (khi cần thiết, ví dụ khi api ko có token, thì check user-session của web)
                //Nếu là web sẽ bị check csrf token,
                //Nếu cần, có thể bỏ qua check csrf với API: Tìm lớp VerifyCsrfToken , thêm $except = ['api/*'] ...
                ->middleware('web')
                ->namespace($this->namespace)
                //->group(base_path('routes/api.php'));
                ->group(function ($route) {
                    //\//ladDebug::addTime(" route_service ", __LINE__);
                    foreach (glob(base_path('routes').'/api*.php') as $filename) {
                        require $filename;
                    }
                    //\//ladDebug::addTime(" route_service ", __LINE__);
                    //                    require base_path('routes/api.php');
                    //                    require base_path('routes/api_demo.php');
                    //                    require base_path('routes/api_folder.php');
                });

            Route::middleware('web')
                ->namespace($this->namespace)
                //->group(base_path('routes/web.php'));
                ->group(function ($route) {

                    //\//ladDebug::addTime(" route_service ", __LINE__);
                    foreach (glob(base_path('routes').'/web*.php') as $filename) {
                        // Skip web_z.php - it will be loaded dynamically in AppServiceProvider
                        if (basename($filename) === 'web_z.php') {
                            continue;
                        }
                        require $filename;
                    }
                    //\//ladDebug::addTime(" route_service ", __LINE__);
                    //                    require base_path('routes/web.php');
                    //                    require base_path('routes/web_admin_demo.php');
                    //                    require base_path('routes/web_admin_demo_folder.php');
                    //                    require base_path('routes/web_admin_user.php');
                });

            //\//ladDebug::addTime(" route_service ", __LINE__);
        });

//        Debugbar::stopMeasure('RouteLoadAll');

        //\//ladDebug::addTime(" route_service ", __LINE__);
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        //\//ladDebug::addTime(" route_service ", __LINE__);
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
        //\//ladDebug::addTime(" route_service ", __LINE__);
    }
}
