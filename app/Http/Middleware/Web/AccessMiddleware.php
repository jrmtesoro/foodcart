<?php

namespace App\Http\Middleware\Web;

use Closure;

class AccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (session()->has('access_level')) {
            $session = session()->get('access_level');
            if ($session == 2) {
                return redirect()->route('owner.dashboard');
            } else if ($session == 3) {
                return redirect()->route('admin.dashboard');
            }
        } else if (!$request->is('login') && !\Route::is('guest.resend')) {
            if (session()->has('verified')) {
                $verified = session()->get('verified');
                if (!$verified) {
                    session()->forget('token');
                    session()->forget('verified');
                }
            }
        }
        return $next($request);
    }
}
