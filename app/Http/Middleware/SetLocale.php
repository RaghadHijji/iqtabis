<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Use the language the user picked (saved in the session), Arabic by default.
     */
    public function handle(Request $request, Closure $next): Response
    {
        App::setLocale(session('locale', 'ar'));

        return $next($request);
    }
}
