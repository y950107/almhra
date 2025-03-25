<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsTeacher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(auth()->check() && !auth()->user()?->hasRole('Teacher'))
        {
          if(auth()->user()?->hasRole('super_admin'))
          {
            return redirect()->route('filament.admin.pages.dashboard');
          }  
          return redirect()->route('filament.student.pages.dashboard');
        }
        return $next($request);
    }
}
