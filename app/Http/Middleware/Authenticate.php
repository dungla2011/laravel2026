<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
//        dd('xxxxx');
        if (isset($_COOKIE['_tglx863516839'])) {
//            $user = User::where('token_user', $_COOKIE['_tglx863516839'])->first();
//            dd("OK USSER");
//            if ($user) {
//                Auth::login($user);
//                return redirect(url()->current());
//            }
        }

        if (! $request->expectsJson()) {
            if (!Auth::check()) {
//                session(['url.intended' => url()->current()]);
                return route('login.login');
            }
        }
    }
}
