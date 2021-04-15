<?php

namespace App\Http\Middleware\Web;

use Closure;

class OwnerMiddleware
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
        if (!session()->has('access_level')) {
            return redirect()->route('login');
        } else if (session()->has('info')) {
            if (session()->get('info')) {
                return redirect()->route('owner.info');
            }
        } else if (session()->get('access_level') != 2) {
            $session = session()->get('access_level');
            if ($session == 1) {
                return redirect()->route('home');
            } else if ($session == 3) {
                return redirect()->route('admin.dashboard');
            }
        }
        return $next($request);
    }
}
