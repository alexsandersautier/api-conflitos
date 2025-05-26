<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['message' => 'Usuário não encontrado'], 404);
            }

        } catch (TokenExpiredException $e) {
            return response()->json([
                'message' => 'Token expirado',
                'error' => 'token_expired',
                'code' => 401
            ], 401);

        } catch (TokenInvalidException $e) {
            return response()->json([
                'message' => 'Token inválido',
                'error' => 'token_invalid',
                'code' => 401
            ], 401);

        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Token de autorização não encontrado',
                'error' => 'token_absent',
                'code' => 401
            ], 401);
        }

        return $next($request);
    }
}
