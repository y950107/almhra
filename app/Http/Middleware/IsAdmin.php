<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            if (!$user->hasRole('super_admin')) {
                Filament::auth()->logout();

                if ($user->hasRole('Student')) {
                    return redirect()->route('filament.student.auth.login');
                }

                return redirect()->route('filament.teacher.auth.login');
            }
        }

        return $next($request);
    }

}
