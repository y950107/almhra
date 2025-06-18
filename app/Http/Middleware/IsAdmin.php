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

            // do not allow Student and Teacher to access Admin Panel
            if ($user->hasRole('Student')) {
                Filament::auth()->logout();
                return redirect()->route('filament.student.auth.login');
            }

            if ($user->hasRole('Teacher')) {
                Filament::auth()->logout();
                return redirect()->route('filament.teacher.auth.login');
            }

        }

        return $next($request);
    }

}
