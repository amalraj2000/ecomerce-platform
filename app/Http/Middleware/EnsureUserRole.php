<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user() || $request->user()->role !== $role) {
            // Check where to redirect them based on their actual role if they are logged in
            if ($request->user()) {
                return match ($request->user()->role) {
                    'admin' => redirect()->route('admin.dashboard'),
                    'vendor' => redirect()->route('vendor.dashboard'),
                    default => redirect()->route('dashboard'),
                };
            }
            
            return redirect('/');
        }

        return $next($request);
    }
}
