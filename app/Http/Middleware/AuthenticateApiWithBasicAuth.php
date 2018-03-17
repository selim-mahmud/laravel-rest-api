<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;

class AuthenticateApiWithBasicAuth extends AuthenticateWithBasicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     * @return mixed
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // check if the basic auth failed
        if($this->auth->guard($guard)->onceBasic('user_name')) {
            throw new AuthenticationException();
        }

        return $next($request);

    }
}