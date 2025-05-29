<?php

namespace App\Http\Controllers;

use App\Models\TipoConflito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

/**
 * @OA\Schema(
 *     schema="TipoConflito",
 *     type="object",
 *     @OA\Property(property="idTipoConflito", type="integer", example="1"),
 *     @OA\Property(property="nome", type="string", example="Prevenção e acompanhamento de conflito: Disputas territoriais"),
 * )
 * 
 * @OA\PathItem(
 *     path="/api/tipo-conflito"
 * )
 *
 * @OA\Tag(
 *     name="TiposConflito",
 *     description="Endpoints para Tipos de Conflito"
 * )
 */
class TipoConflitoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tipo-conflito",
     *     tags={"TiposConflito"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os tipos de conflito",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tipos de conflito",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/TipoConflito")
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
        
        $tipoconflito = TipoConflito::all();
        return response()->json($tipoconflito);
    }

    /**
     * @OA\Post(
     *     path="/api/tipo-conflito",
     *     tags={"TiposConflito"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo tipo de conflito",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Tipo de Conflito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tipo de Conflito criado"
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

        $tipoconflito = TipoConflito::create($validatedData);
        return response()->json($tipoconflito, Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/tipo-conflito/{id}",
     *     tags={"TiposConflito"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um tipo de conflito específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do tipo de conflito",
     *         @OA\JsonContent(ref="#/components/schemas/TipoConflito")
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
        
        $tipoconflito = TipoConflito::findOrFail($id);
        return response()->json($tipoconflito);
    }

    /**
     * @OA\Put(
     *     path="/api/tipo-conflito/{id}",
     *     tags={"TiposConflito"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um tipo de conflito específico",
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
     *             @OA\Property(property="nome", type="string", example="Tipo de Conflito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de Conflito atualizado"
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
        
        $tipoconflito = TipoConflito::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $tipoconflito->update($validatedData);
        return response()->json($tipoconflito);
    }
    

    /**
     * @OA\Delete(
     *     path="/api/tipo-conflito/{id}",
     *     tags={"TiposConflito"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um tipo de conflito específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Tipo de Conflito excluído"
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
        
        $tipoconflito = TipoConflito::findOrFail($id);
        $tipoconflito->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
