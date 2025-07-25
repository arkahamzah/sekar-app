<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }
        
        // Check if user has pengurus role
        if (!$user->pengurus || !$user->pengurus->role) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Anda tidak memiliki hak akses admin.');
        }
        
        // Check if user has admin role
        $adminRoles = ['ADM', 'ADMIN_DPP', 'ADMIN_DPW', 'ADMIN_DPD'];
        if (!in_array($user->pengurus->role->NAME, $adminRoles)) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Fitur ini hanya untuk admin.');
        }
        
        return $next($request);
    }
}