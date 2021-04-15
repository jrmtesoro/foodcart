<?php

namespace App\Http\Middleware\Web;

use Closure;

class GuestMiddleware
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
        }
        if (session()->get('access_level') == 2) {
            return redirect()->route('owner.dashboard');
        } else if (session()->get('access_level') == 3) {
            return redirect()->route('admin.dashboard');
        }
        return $next($request);
    }
}
