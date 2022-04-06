<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
         
            //email verification related code
            if ($request->routeIs('admin-verify')) {
                $request->session()->flash('message', 'To Confirm Your Email Account, First Login with Your Email & Password Which are Provided by You at the Time of Your Admin Account Creation!');
                 return route('login-admin');
              }
            //end email verification related code

            return route('home');
        }
    }
}
