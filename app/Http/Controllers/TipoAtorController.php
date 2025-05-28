<?php

namespace App\Http\Controllers;

use App\Models\TipoAtor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Schema(
 *     schema="TipoAtor",
 *     type="object",
 *     @OA\Property(property="idTipoAtor", type="integer"),
 *     @OA\Property(property="nome", type="string", example="Latifundiário")
 * )
 * 
 * @OA\PathItem(
 *     path="/api/tipo-ator"
 * )
 *
 * @OA\Tag(
 *     name="TiposAtor",
 *     description="Endpoints para Tipos de Ator"
 * )
 */
class TipoAtorController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tipo-ator",
     *     tags={"TiposAtor"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os tipos de ator",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tipos de ator",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/TipoAtor")
     *         )
     *     )
     * )
     */
    public function index()
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $tipoator = TipoAtor::all();
        return response()->json($tipoator);
    }

    /**
     * @OA\Post(
     *     path="/api/tipo-ator",
     *     tags={"TiposAtor"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo tipo de ator",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Tipo de Ator do Conflito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tipo de Ator criado"
     *     )
     * )
     */
    public function store(Request $request)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $tipoator = TipoAtor::create($validatedData);
        return response()->json($tipoator, Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/tipo-ator/{id}",
     *     tags={"TiposAtor"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um tipo de ator específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do tipo de ator",
     *         @OA\JsonContent(ref="#/components/schemas/TipoAtor")
     *     )
     * )
     */
    public function show($id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $tipoator = TipoAtor::findOrFail($id);
        return response()->json($tipoator);
    }

    /**
     * @OA\Put(
     *     path="/api/tipo-ator/{id}",
     *     tags={"TiposAtor"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um tipo de ator específico",
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
     *             @OA\Property(property="nome", type="string", example="Tipo de Ator do Conflito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de Ator atualizado"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $tipoator = TipoAtor::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $tipoator->update($validatedData);
        return response()->json($tipoator);
    }

    /**
     * @OA\Delete(
     *     path="/api/tipo-ator/{id}",
     *     tags={"TiposAtor"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um tipo de ator específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Tipo de Ator excluído"
     *     )
     * )
     */
    public function destroy($id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $tipoator = TipoAtor::findOrFail($id);
        $tipoator->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
