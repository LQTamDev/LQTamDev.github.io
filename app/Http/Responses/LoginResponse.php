<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{

    public function toResponse($request)
    {

        // below is the existing response
        // replace this with your own code
        // the user can be located with Auth facade
        if($request->user()->hasRole('manager')){
            $route = 'users.index';
        }
        else if($request->user()->hasRole('host')){
            $route = 'profile.show';
        }
        else $route = 'dashboard';
        return $request->wantsJson()
            ? response()->json(['two_factor' => false])
            : redirect()->intended(route($route));
    }
}
