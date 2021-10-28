<?php

namespace BadMushroom\LaravelTourist\Middleware;

use BadMushroom\LaravelTourist\Facades\Tour;
use Closure;
use Illuminate\Http\Request;

class Tourism
{
    /**
     *
     */
    public function handle(Request $request, Closure $next)
    {
        $passport = Tour::issuePassport();
        session()->put('tourist_passport', $passport);

        Tour::startTour($passport);

        return $next($request);
    }
}
