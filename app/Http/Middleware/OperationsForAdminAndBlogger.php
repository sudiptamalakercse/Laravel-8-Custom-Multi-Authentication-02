<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationsForAdminAndBlogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $admin_logged_in=Auth::guard('admin')->check();

        $blogger_logged_in=Auth::guard('blogger')->check();
        
        if($admin_logged_in || $blogger_logged_in){

          return $next($request);
        
        }
        else{
            return redirect()->route('home');
        }
    }
}
