<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/** Panel web hanya untuk admin & staf. */
class EnsurePengelola
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isPengelola()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();

            return redirect()->route('login')->withErrors(['email' => 'Akun ini tidak memiliki akses ke panel pengelola.']);
        }

        return $next($request);
    }
}
