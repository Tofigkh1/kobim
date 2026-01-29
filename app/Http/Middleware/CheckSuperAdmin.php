<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if ($user && $user->isSuperAdmin()) {
            $request->merge(['isSuperAdmin' => true]);
        } else {
            $request->merge(['isSuperAdmin' => false]);
        }

        return $next($request);
    }
}

