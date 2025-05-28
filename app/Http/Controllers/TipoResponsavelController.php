<?php

namespace App\Http\Controllers;

use App\Models\TipoResponsavel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

/**
 * @OA\Schema(
 *     schema="TipoResponsavel",
 *     type="object",
 *     @OA\Property(property="idTipoResponsavel", type="integer"),
 *     @OA\Property(property="nome", type="string", example="Latifundiário")
 * )
 * 
 * @OA\PathItem(
 *     path="/api/tipo-Responsavel"
 * )
 *
 * @OA\Tag(
 *     name="TiposResponsavel",
 *     description="Endpoints para Tipos de Responsavel"
 * )
 */
class TipoResponsavelController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tipo-Responsavel",
     *     tags={"TiposResponsavel"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os tipos de Responsavel",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tipos de Responsavel",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/TipoResponsavel")
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
        
        $tipoResponsavel = TipoResponsavel::all();
        return response()->json($tipoResponsavel);
    }

    /**
     * @OA\Post(
     *     path="/api/tipo-Responsavel",
     *     tags={"TiposResponsavel"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo tipo de Responsavel",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Tipo de Responsavel do Conflito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tipo de Responsavel criado"
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

        $tipoResponsavel = TipoResponsavel::create($validatedData);
        return response()->json($tipoResponsavel, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/tipo-Responsavel/{id}",
     *     tags={"TiposResponsavel"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um tipo de Responsavel específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do tipo de Responsavel",
     *         @OA\JsonContent(ref="#/components/schemas/TipoResponsavel")
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
        
        $tipoResponsavel = TipoResponsavel::findOrFail($id);
        return response()->json($tipoResponsavel);
    }

    /**
     * @OA\Put(
     *     path="/api/tipo-Responsavel/{id}",
     *     tags={"TiposResponsavel"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um tipo de Responsavel específico",
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
     *             @OA\Property(property="nome", type="string", example="Tipo de Responsavel do Conflito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de Responsavel atualizado"
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
        
        $tipoResponsavel = TipoResponsavel::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $tipoResponsavel->update($validatedData);
        return response()->json($tipoResponsavel);
    }

    /**
     * @OA\Delete(
     *     path="/api/tipo-Responsavel/{id}",
     *     tags={"TiposResponsavel"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um tipo de Responsavel específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Tipo de Responsavel excluído"
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
        
        $tipoResponsavel = TipoResponsavel::findOrFail($id);
        $tipoResponsavel->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
