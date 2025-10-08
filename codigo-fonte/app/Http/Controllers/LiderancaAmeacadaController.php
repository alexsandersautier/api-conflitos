<?php

namespace App\Http\Controllers;

use App\Models\LiderancaAmeacada;
use Illuminate\Http\Request;

/**
 *  @OA\Schema(
 *     schema="LiderancaAmeacada",
 *     type="object",
 *     @OA\Property(property="idLiderancaAmeacada", type="integer", example="1"),
 *     @OA\Property(property="conflito", type="object", ref="#/components/schemas/Conflito"),
 *     @OA\Property(property="nome", type="string", example="Nome da Liderança Ameaçada"),
 *     @OA\Property(property="distancia_conflito", type="decimal", example="1025.23")
 * )
 * 
 * @OA\PathItem(
 *     path="/api/lideranca-ameacada"
 * )
 *
 * @OA\Tag(
 *     name="LiderancasAmeacadas",
 *     description="Endpoints para Lideranças Ameaçadas"
 * )
 */
class LiderancaAmeacadaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/lideranca-ameacada",
     *     tags={"LiderancasAmeacadas"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os Lideranças Ameaçadas",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de Lideranças Ameaçadas",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/LiderancaAmeacada")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $liderancas_ameacadas = LiderancaAmeacada::all();
        return response()->json($liderancas_ameacadas);
    }

    /**
     * @OA\Post(
     *     path="/api/lideranca-ameacada",
     *     tags={"LiderancasAmeacadas"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo liderança ameaçada",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"idConflito", "nome"}, 
     *             @OA\Property(property="idConflito", type="integer", example=1),
     *             @OA\Property(property="nome", type="string", example="Cacique Raoni"),
     *             @OA\Property(property="distancia_conflito", type="number", format="float", example=5.2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="LiderancaAmeacada criado"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'idConflito'         => 'required|integer|exists:conflito,idConflito',
            'nome'               => 'required|string|max:100',
            'distancia_conflito' => 'numeric|decimal:0,2'
        ]);

        $liderancaAmeacada = LiderancaAmeacada::create($validatedData);
        return response()->json($liderancaAmeacada, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/lideranca-ameacada/{id}",
     *     tags={"LiderancasAmeacadas"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um liderança ameaçada específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados da Liderança Ameaçada",
     *         @OA\JsonContent(ref="#/components/schemas/LiderancaAmeacada")
     *     )
     * )
     */
    public function show($id)
    {
        $lideranca_ameacada = LiderancaAmeacada::findOrFail($id);
        return response()->json($lideranca_ameacada);
    }

    /**
     * @OA\Put(
     *     path="/api/lideranca-ameacada/{id}",
     *     tags={"LiderancasAmeacadas"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um liderança ameaçada específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"idConflito", "nome"}, 
     *             @OA\Property(property="idConflito", type="integer", example=1),
     *             @OA\Property(property="nome", type="string", example="Cacique Raoni"),
     *             @OA\Property(property="distancia_conflito", type="number", format="float", example=5.2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lideranca Ameacada atualizada"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $liderancaAmeacada = LiderancaAmeacada::findOrFail($id);

        $validatedData = $request->validate([
            'idConflito'         => 'required|integer|exists:conflito,idConflito',
            'nome'               => 'required|string|max:100',
            'distancia_conflito' => 'numeric|decimal:0,2'
        ]);

        $liderancaAmeacada->update($validatedData);
        return response()->json($liderancaAmeacada);
    }

    /**
     * @OA\Delete(
     *     path="/api/lideranca-ameacada/{id}",
     *     tags={"LiderancasAmeacadas"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um Liderança Ameaçada específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Liderança Ameaçada excluído"
     *     )
     * )
     */
    public function destroy($id)
    {
        $liderancaAmeacada = LiderancaAmeacada::findOrFail($id);
        $liderancaAmeacada->delete();
        return response()->json(null, 204);
    }
    
    /**
     * @OA\Get(
     *     path="/api/lideranca-ameacada/conflito/{idConflito}",
     *     tags={"LiderancasAmeacadas"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os Lideranças Ameaçadas de um Conflito",
     *     @OA\Parameter(
     *         name="idConflito",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de Lideranças Ameaçadas"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Termo de pesquisa não fornecido"
     *     )
     * )
     */
    public function getAllByConflito($idConflito){
        try {
            return LiderancaAmeacada::with('conflito')->where('idConflito', $idConflito)->get();
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro na pesquisa',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
