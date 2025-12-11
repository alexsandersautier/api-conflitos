<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleCors
{

    /**
     * Handle an incoming request.
     *
     * @param
     *            \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    protected $paths = [
        'api/*',
        'sanctum/csrf-cookie',
        'login',
        'logout'
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Configurações CORS
        $headers = [
            'Access-Control-Allow-Origin' => '*', // env('FRONTEND_URL', 'http://localhost:3000'),
            'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE, PATCH',
            'Access-Control-Max-Age' => '86400',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN',
            'Access-Control-Allow-Credentials' => 'true'
        ];

        // Resposta para requisições OPTIONS (preflight)
        if ($request->isMethod('OPTIONS')) {
            return response()->json([
                'method' => 'OPTIONS'
            ], 200, $headers);
        }

        $response = $next($request);

        // Adiciona headers CORS à resposta
        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        return $response;
    }
}