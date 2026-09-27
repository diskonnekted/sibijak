<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            if (in_array('kontraktor', $roles)) {
                return redirect()->route('login.kontraktor');
            } elseif (in_array('pemeriksa_lapangan', $roles)) {
                return redirect()->route('login.pengawas');
            }
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Admin PUPR has preview access to mobile views
        if ($user->role === 'admin_pupr') {
            return $next($request);
        }

        if (!in_array($user->role, $roles)) {
            if ($user->role === 'kontraktor') {
                return redirect()->route('kontraktor.dashboard')->with('error', 'Akses ditolak: Anda terdaftar sebagai akun Kontraktor.');
            } elseif ($user->role === 'pemeriksa_lapangan') {
                return redirect()->route('pengawas.dashboard')->with('error', 'Akses ditolak: Anda terdaftar sebagai akun Pengawas Lapangan.');
            }

            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
