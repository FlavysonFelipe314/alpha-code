<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Cors
{
    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->header('Origin');
        
        // Lista de origens permitidas (localhost pode ser tanto 127.0.0.1 quanto localhost)
        $allowedOrigins = [
            'http://localhost:8000',
            'http://127.0.0.1:8000',
            'http://localhost',
            'http://127.0.0.1',
        ];
        
        // Se não há origem (mesma origem), usa a origem atual
        if (!$origin) {
            $allowedOrigin = $request->getSchemeAndHttpHost();
        } 
        // Se a origem está na lista permitida, usa ela
        elseif (in_array($origin, $allowedOrigins)) {
            $allowedOrigin = $origin;
        } 
        // Caso contrário, não permite (ou poderia usar '*', mas sem credentials)
        else {
            $allowedOrigin = '*';
        }

        if ($request->isMethod('OPTIONS')) {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', $allowedOrigin)
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH')
                ->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, X-Requested-With, X-CSRF-TOKEN')
                ->header('Access-Control-Allow-Credentials', $allowedOrigin !== '*' ? 'true' : 'false');
        }

        $response = $next($request);

        $response->header('Access-Control-Allow-Origin', $allowedOrigin);
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH');
        $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, X-Requested-With, X-CSRF-TOKEN');
        
        // Só permite credentials se não estiver usando wildcard
        if ($allowedOrigin !== '*') {
            $response->header('Access-Control-Allow-Credentials', 'true');
        }

        return $response;
    }
}