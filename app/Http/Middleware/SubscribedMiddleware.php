<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SubscribedMiddleware
{
    /**
     * Handle an incoming request.
     * Verifica apenas se o usuário está autenticado (lógica de pagamento removida)
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Apenas verifica se o usuário está autenticado
        // Removida toda a lógica de verificação de assinatura/pagamento
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}


