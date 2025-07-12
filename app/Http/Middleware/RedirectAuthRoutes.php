<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectAuthRoutes
{
    private array $protectedRoutes = [
        'teacher/login',
        'student/login', 
        'admin/login',
        'teacher/*',    // Will catch all teacher routes
        'student/*',   // Will catch all student routes
        'admin/*'      // Will catch all admin routes
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Skip if already on quran subdomain
        if (str_contains($request->getHost(), 'quran.')) {
            return $next($request);
        }

        // Check if current path matches any protected route pattern
        foreach ($this->protectedRoutes as $route) {
            if ($request->is($route)) {
                return redirect()->away(
                    'https://quran.almhrah.com/'.$request->path()
                );
            }
        }

        return $next($request);
    }
}