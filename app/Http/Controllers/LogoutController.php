<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{

     public function __construct()
    {
        $this->middleware(['guest:admin','guest:blogger'])->except('destroy');
    }

    public function destroy(Request $request)
    {
        if(Auth::guard('admin')->check()){
           Auth::guard('admin')->logout();
        }
        elseif(Auth::guard('blogger')->check()){
            Auth::guard('blogger')->logout();
        }

        $request->session()->invalidate();

        $request->session()->regenerateToken();
        
        return redirect()->route('home');
    }
}
