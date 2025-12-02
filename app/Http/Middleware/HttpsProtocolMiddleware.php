<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

//https://robindirksen.com/blog/laravel-redirect-to-https-a-middleware-to-force-https
class HttpsProtocolMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    //Todo: *** redirect https
    public function handle($request, Closure $next)
    {

        //        if (!$request->secure() && app()->environment('production')) {
        if (! $request->secure()) {
            // return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }
}
