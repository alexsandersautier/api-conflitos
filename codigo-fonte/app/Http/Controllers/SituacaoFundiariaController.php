<?php

namespace App\Http\Controllers;

use App\Models\SituacaoFundiaria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

/**
 * @OA\Schema(
 *     schema="SituacaoFundiaria",
 *     type="object",
 *     @OA\Property(property="idSituacaoFundiaria", type="integer", example="3"),
 *     @OA\Property(property="nome", type="string", example="Declarada")
 * )
 * 
 * @OA\PathItem(
 *     path="/api/situacao-fundiaria"
 * )
 *
 * @OA\Tag(
 *     name="SituacaoFundiarias",
 *     description="Endpoints para Situações Fundiárias"
 * )
 */
class SituacaoFundiariaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/situacao-fundiaria",
     *     tags={"SituacaoFundiarias"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os situações fundiarias",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de Situações Fundiárias",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/SituacaoFundiaria")
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
        
        $situacaofundiarias = SituacaoFundiaria::all();
        return response()->json($situacaofundiarias);
    }

    /**
     * @OA\Post(
     *     path="/api/situacao-fundiaria",
     *     tags={"SituacaoFundiarias"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo situacao fundiária",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Situação Fundiária")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Situação Fundiária criada"
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

        $situacaofundiaria = SituacaoFundiaria::create($validatedData);
        return response()->json($situacaofundiaria, Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/situacao-fundiaria/{id}",
     *     tags={"SituacaoFundiarias"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um situacao fundiaria específica",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do situação fundiária",
     *         @OA\JsonContent(ref="#/components/schemas/SituacaoFundiaria")
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
        
        $situacaofundiaria = SituacaoFundiaria::findOrFail($id);
        return response()->json($situacaofundiaria);
    }

    /**
     * @OA\Put(
     *     path="/api/situacao-fundiaria/{id}",
     *     tags={"SituacaoFundiarias"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um situacaofundiaria específico",
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
     *             @OA\Property(property="nome", type="string", example="Situação Fundiária")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Situação Fundiária atualizada"
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
        
        $situacaofundiaria = SituacaoFundiaria::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $situacaofundiaria->update($validatedData);
        return response()->json($situacaofundiaria);
    }

    /**
     * @OA\Delete(
     *     path="/api/situacao-fundiaria/{id}",
     *     tags={"SituacaoFundiarias"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um situação fundiária específica",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Situação Fundiária excluída"
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
        
        $situacaofundiaria = SituacaoFundiaria::findOrFail($id);
        $situacaofundiaria->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
