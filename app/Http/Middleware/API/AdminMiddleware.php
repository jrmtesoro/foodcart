<?php

namespace App\Http\Middleware\API;

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
        $user_auth = auth()->user()->toArray();
        if ($user_auth['access_level'] != 3) {
            return response()->json([
                "success" => false,
                "message" => "Unauthorized Request!"
            ]);
        }
        return $next($request);
    }
}
