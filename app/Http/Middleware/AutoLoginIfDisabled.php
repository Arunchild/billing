<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class AutoLoginIfDisabled
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $loginBypassed = false;
        
        $envNoLogin = env('NO_LOGIN');
        $envRequireLogin = env('REQUIRE_LOGIN');
        $envLoginRequired = env('LOGIN_REQUIRED');
        $envLogin = env('LOGIN');
        
        if ($envNoLogin === true || $envNoLogin === 'true' || $envNoLogin === 'yes' || $envNoLogin === '1' || $envNoLogin === 1) {
            $loginBypassed = true;
        }
        
        if ($envRequireLogin === false || $envRequireLogin === 'false' || $envRequireLogin === 'no' || $envRequireLogin === '0' || $envRequireLogin === 0) {
            $loginBypassed = true;
        }
        
        if ($envLoginRequired === false || $envLoginRequired === 'false' || $envLoginRequired === 'no' || $envLoginRequired === '0' || $envLoginRequired === 0) {
            $loginBypassed = true;
        }
        
        if ($envLogin === false || $envLogin === 'false' || $envLogin === 'no' || $envLogin === 'off' || $envLogin === '0' || $envLogin === 0) {
            $loginBypassed = true;
        }

        if ($loginBypassed) {
            if (!Auth::check()) {
                $admin = User::where('email', 'admin')->first() ?? User::first();
                if ($admin) {
                    Auth::login($admin);
                }
            }
        }

        return $next($request);
    }
}
