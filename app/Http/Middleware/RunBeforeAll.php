<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lad thêm để chạy trước mọi request
    Nhằm mục đích nếu có token_user trong cookie thì tự động login
 *  Vì session ngắn, 1 ngày, mà token_user có thể dài hơn, nên nếu sesssion hết, thì có thể login lại với token
 */
class RunBeforeAll
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response { 
 
        //Nếu đã hết phiên login session và vẫn còn token, thì login:
        // Skip for CLI requests (performance optimization for local development)
        if (!isCli())
        if (!app()->runningInConsole() && !$request->is('api/*') && !Auth::id())
//        if (isset($_COOKIE['_tglx863516839']))
        {

            if($user = User::getUserByTokenAccess())
                Auth::login($user);

//            $user = User::where('token_user', $_COOKIE['_tglx863516839'])->first();
//            if ($user) {
//                ol00("RunBeforeAll:  login_with_token_ok , $user->id, " . request()->ip() ." # " . request()->url());
//                Auth::login($user);
//            }
        }
        return $next($request);
    }
}
