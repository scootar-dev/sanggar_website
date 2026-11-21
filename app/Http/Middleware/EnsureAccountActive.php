<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAccountActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user && !$user->isAktif()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Akun nonaktif atau masa keanggotaan telah berakhir. Hubungi admin.'], 403);
            }
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors(['email' => 'Akun nonaktif atau masa keanggotaan telah berakhir. Hubungi admin.']);
        }

        return $next($request);
    }
}
