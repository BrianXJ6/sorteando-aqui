<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as MiddlewareAuthenticate;

class Authenticate extends MiddlewareAuthenticate
{
    /**
     * Handle an unauthenticated user.
     *
     * @param \Illuminate\Http\Request $request
     * @param array $guards
     *
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    #[\Override]
    protected function unauthenticated($request, array $guards): void
    {
        throw new AuthenticationException(
            trans('Unauthenticated.'),
            $guards,
            $request->expectsJson() ? null : $this->redirectTo($request)
        );
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
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
