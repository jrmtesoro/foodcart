<?php

namespace App\Http\Middleware\Web;

use Closure;

class AdminMiddleware
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
        } else if (session()->get('access_level') != 3) {
            $session = session()->get('access_level');
            if ($session == 1) {
                return redirect()->route('home');
            } else if ($session == 2) {
                return redirect()->route('owner.dashboard');
            }
        }
        
        return $next($request);
    }
}
