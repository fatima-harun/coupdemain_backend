<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est authentifié et si son statut est désactivé
        if (Auth::check() && Auth::user()->status == 0) {
            // Si le statut est 0 (désactivé), rediriger l'utilisateur
            return redirect('/account-deactivated'); // ou une page d'erreur spécifique
        }
        return $next($request);
    }
}
