<?php

namespace App\Http\Middleware\Web;

use Closure;

class InfoMiddleware
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
        if (session()->get('info')) {
            return redirect()->route('owner.info');
        }
        return $next($request);
    }
}
