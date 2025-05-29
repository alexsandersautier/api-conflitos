<?php

namespace App\Http\Controllers;

use App\Models\Perfil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

/**
 * 
 * @OA\Schema(
 *     schema="Perfil",
 *     type="object",
 *     @OA\Property(property="idPerfil", type="integer", example=1),
 *     @OA\Property(property="nome", type="string", example="Administrador"),
 * )
 * 
 * @OA\PathItem(
 *     path="/api/perfil"
 * )
 *
 * @OA\Tag(
 *     name="Perfis",
 *     description="Endpoints para Perfil"
 * )
 */
class PerfilController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/perfil",
     *     tags={"Perfis"},
     *     security={ {"sanctum": {} } },     *     
     *     summary="Listar todos os perfils",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de perfils",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Perfil")
     *         )
     *     )
     * )
     */
    public function index()
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $perfis = Perfil::all();
        return response()->json($perfis);
    }

    /**
     * @OA\Post(
     *     path="/api/perfil",
     *     tags={"Perfis"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo perfil",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Administrador")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Perfil criado"
     *     )
     * )
     */
    public function store(Request $request)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $perfil = Perfil::create($validatedData);
        return response()->json($perfil, Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/perfil/{id}",
     *     tags={"Perfis"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um perfil específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do perfil",
     *         @OA\JsonContent(ref="#/components/schemas/Perfil")
     *     )
     * )
     */
    public function show($id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $perfil = Perfil::findOrFail($id);
        return response()->json($perfil);
    }

    /**
     * @OA\Put(
     *     path="/api/perfil/{id}",
     *     tags={"Perfis"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um perfil específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Administrador")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Perfil atualizado"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $perfil = Perfil::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $perfil->update($validatedData);
        return response()->json($perfil);
    }

    /**
     * @OA\Delete(
     *     path="/api/perfil/{id}",
     *     tags={"Perfis"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um perfil específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Perfil excluído"
     *     )
     * )
     */
    public function destroy($id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $perfil = Perfil::findOrFail($id);
        $perfil->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
