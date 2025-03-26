<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Symfony\Component\HttpFoundation\Response;

class IsStudent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(auth()->check() && !auth()->user()?->hasRole('Student'))
        {
          Filament::auth()->logout();
          if(auth()->user()?->hasRole('Teacher'))
          {
            return redirect()->route('filament.teacher.auth.login');
          }  
          return redirect()->route('filament.admin.auth.login');
        }
        return $next($request);
        
    }
}
