<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class LoadDynamicRoutes
{
    /**
     * Handle an incoming request and register dynamic routes if needed.
     * This runs on EVERY request, bypassing route cache completely.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Only register routes if they haven't been registered yet


        return $next($request);
    }
}
