<?php

namespace App\Http\Controllers;

use App\Models\Ator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

/**
 *  @OA\Schema(
 *     schema="Ator",
 *     type="object",
 *     @OA\Property(property="idAtor", type="integer", example="1"),
 *     @OA\Property(property="categoriaAtor", type="object", ref="#/components/schemas/CategoriaAtor"),
 *     @OA\Property(property="conflito", type="object", ref="#/components/schemas/Conflito"),
 *     @OA\Property(property="nome", type="string", example="Nome do ator")
 * )
 * 
 * @OA\PathItem(
 *     path="/api/ator"
 * )
 *
 * @OA\Tag(
 *     name="Atores",
 *     description="Endpoints para Atores"
 * )
 */
class AtorController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/ator",
     *     tags={"Atores"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os Atores",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de Atores",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Ator")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $atores = Ator::all();
        return response()->json($atores);
    }

    /**
     * @OA\Post(
     *     path="/api/ator",
     *     tags={"Atores"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo ator",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"idCategoriaAtor", "idConflito", "nome"}, 
     *             @OA\Property(property="idCategoriaAtor", type="integer", example="1"),
     *             @OA\Property(property="idConflito", type="integer", example="1"),
     *             @OA\Property(property="nome", type="string", example="João da Silva")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ator criado"
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
            'idCategoriaAtor' => 'required|integer|exists:categoria_ator,idCategoriaAtor',
            'idConflito' => 'required|integer|exists:conflito,idConflito',
            'nome' => 'required|string|max:100'
        ]);

        $ator = Ator::create($validatedData);
        return response()->json($ator, Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/ator/{id}",
     *     tags={"Atores"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um ator específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do ator",
     *         @OA\JsonContent(ref="#/components/schemas/Ator")
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
        
        $ator = Ator::findOrFail($id);
        return response()->json($ator);
    }

    /**
     * @OA\Put(
     *     path="/api/ator/{id}",
     *     tags={"Atores"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um ator específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"idCategoriaAtor", "idConflito", "nome"}, 
     *             @OA\Property(property="idCategoriaAtor", type="integer", example="1"),
     *             @OA\Property(property="idConflito", type="integer", example="1"),
     *             @OA\Property(property="nome", type="string", example="João da Silva")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ator atualizado"
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
        
        $ator = Ator::findOrFail($id);

        $validatedData = $request->validate([
            'idCategoriaAtor' => 'required|integer|exists:categoria_ator,idCategoriaAtor',
            'idConflito' => 'required|integer|exists:conflito,idConflito',
            'nome' => 'required|string|max:100'
        ]);

        $ator->update($validatedData);
        return response()->json($ator);
    }

    /**
     * @OA\Delete(
     *     path="/api/ator/{id}",
     *     tags={"Atores"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um ator específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Ator excluído"
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
        
        $ator = Ator::findOrFail($id);
        $ator->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
    
    /**
     * @OA\Get(
     *     path="/api/ator/conflito/{idConflito}",
     *     tags={"Atores"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os Atores de um Conflito",
     *     @OA\Parameter(
     *         name="idConflito",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de Atores"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro na consulta"
     *     )
     * )
     */
    public function getAllByConflito($idConflito){
        try {
            if (!Auth::guard('sanctum')->check()) {
                return response()->json([
                    'message' => 'Não autorizado',
                    'status'  => Response::HTTP_UNAUTHORIZED
                ], Response::HTTP_UNAUTHORIZED);
            }
            
            return Ator::with(['categoria_ator', 'conflito'])->where('idConflito', $idConflito)->get();
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro na pesquisa',
                'details' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
