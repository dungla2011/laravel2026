<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;
use Illuminate\Support\Str;

class AddRequestId extends Middleware
{
    public function handle($request, \Closure $next)
    {

        $request->requestId = Str::uuid()->toString();

        return $next($request);
    }
}
