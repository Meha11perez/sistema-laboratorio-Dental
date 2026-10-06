<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerificarUsuarioActivo
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $guard = Auth::guard(
            config('fortify.guard')
                ?: config('auth.defaults.guard', 'web')
        );

        $usuario = $guard->user();

        if ($usuario && !$usuario->estado) {
            $guard->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $mensaje = 'Su cuenta está desactivada. Comuníquese con el administrador del laboratorio.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $mensaje,
                ], 401);
            }

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => $mensaje,
                ]);
        }

        return $next($request);
    }
}