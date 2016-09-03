<?php

namespace App\Http\Middleware;

use App;
use Closure;

class ForceHttps
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
        if (App::environment('production') && !$request->secure())
            return redirect()->secure($request->getRequestUri());

        return $next($request);
    }
}
