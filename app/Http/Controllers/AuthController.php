<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 *     name="Autenticação",
 *     description="Endpoints para Autenticação"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Autentica um usuário",
     *     description="Realiza o login e retorna um token de acesso",
     *     tags={"Autenticação"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","senha"},
     *             @OA\Property(property="email", type="string", format="email", example="luizleao@gmail.com"),
     *             @OA\Property(property="senha", type="string", format="password", example="senha")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login bem-sucedido",
     *         @OA\JsonContent(
     *                 @OA\Property(property="access_token", type="string", example="1|abcdef123456"),
     *                 @OA\Property(property="token_type", type="string", example="Bearer"),
     *                 @OA\Property(property="usuario", type="object",
     *                 @OA\Property(property="idUsuario", type="integer", example=1),
     *                 @OA\Property(property="nome", type="string", example="João Silva"),
     *                 @OA\Property(property="email", type="string", example="usuario@exemplo.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciais inválidas",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Credenciais inválidas")
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'senha' => 'required',
        ]);

        $usuario = Usuario::with(['orgao','perfil'])->where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->senha, $usuario->senha)) {
            return response()->json(['message' => 'Credenciais inválidas'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $usuario->createToken('auth_token', ['assunto:list', 
                                                      'assunto:view',
                                                      'assunto:create',
                                                      'assunto:update'])->plainTextToken;

        return response()->json(['access_token' => $token,
                                 'token_type'   => 'Bearer',
                                 'usuario'      => $usuario], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Desconecta o usuário",
     *     description="Revoga todos os tokens de acesso do usuário autenticado",
     *     tags={"Autenticação"},
     *     security={ {"sanctum": {} } },
     *     @OA\Response(
     *         response=200,
     *         description="Logout bem-sucedido",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logout realizado com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logout realizado com sucesso'], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/me",
     *     summary="Retorna os dados do usuário autenticado",
     *     description="Retorna as informações do usuário atualmente autenticado",
     *     tags={"Autenticação"},
     *     security={ {"sanctum": {} } },
     *     @OA\Response(
     *         response=200,
     *         description="Dados do usuário",
     *         @OA\JsonContent(
     *             @OA\Property(property="idUsuario", type="integer", example=1),
     *             @OA\Property(property="nome", type="string", example="João Silva"),
     *             @OA\Property(property="email", type="string", example="usuario@exemplo.com"),
     *             @OA\Property(property="idOrgao", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
