<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated as MiddlewareRedirectIfAuthenticated;

class RedirectIfAuthenticated extends MiddlewareRedirectIfAuthenticated
{
    /**
     * Get the path the user should be redirected to when they are authenticated.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return null|string
     */
    #[\Override]
    protected function redirectTo(Request $request): ?string
    {
        return route('home');
    }
}
