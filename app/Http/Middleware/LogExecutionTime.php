<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

class LogExecutionTime extends Middleware
{
    public function handle($request, \Closure $next)
    {
        $startTime = microtime(true);

        $response = $next($request);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        if ($executionTime > 10) {
            outputFile('/var/glx/weblog/slow_web2.log',
                "Request execution time: $executionTime seconds / ".$request->url());
        }

        return $response;
    }
}
