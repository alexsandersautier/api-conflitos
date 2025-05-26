<?php

namespace App\Http\Controllers;

use App\Models\InqueritoPolicial;
use Illuminate\Http\Request;

/**
 * 
 * @OA\Schema(
 *     schema="InqueritoPolicial",
 *     type="object",
 *     @OA\Property(property="idInqueritoPolicial", type="integer", example="1"),
 *     @OA\Property(property="conflito", type="object",  description="Conflito vinculado", ref="#/components/schemas/Conflito"),
 *     @OA\Property(property="tipoInqueritoPolicial", type="object",  description="Tipo de Inquerito Policial", ref="#/components/schemas/TipoInqueritoPolicial"),
 *     @OA\Property(property="numero_bo", type="string", example="5465466546"),
 *     @OA\Property(property="data", type="date", example="yyyy-mm-dd"),
 *     @OA\Property(property="assistencia_juridica", type="string", example="DPE")
 * )
 * 
 * @OA\PathItem(
 *     path="/api/inquerito-policial"
 * )
 *
 * @OA\Tag(
 *     name="InqueritosPoliciais",
 *     description="Endpoints para Inqueritos Policiais"
 * )
 */
class InqueritoPolicialController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/inquerito-policial",
     *     tags={"InqueritosPoliciais"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os inqueritos policiais",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de Inqueritos Policiais",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/InqueritoPolicial")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $inqueritoPolicials = InqueritoPolicial::with(['conflito', 'tipo_inquerito_policial'])->get();
        return response()->json($inqueritoPolicials);
    }

    /**
     * @OA\Post(
     *     path="/api/inquerito-policial",
     *     tags={"InqueritosPoliciais"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar uma novo inquerito policial",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"idConflito", "idTipoInqueritoPolicial", "numero_bo", "data", "assistencia_juridica"},
     *             @OA\Property(property="idConflito", type="integer", example="1"),
     *             @OA\Property(property="idTipoInqueritoPolicial", type="integer", example="1"),
     *             @OA\Property(property="numero_bo", type="string", example="123.456.789"),
     *             @OA\Property(property="data", type="date", example="yyyy-mm-dd"),
     *             @OA\Property(property="assistencia_juridica", type="string", example="Inquerito Policial")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Inquerito Policial criado"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'idConflito'              => 'required|integer|exists:conflito,idConflito',
            'idTipoInqueritoPolicial' => 'required|integer|exists:tipo_inquerito_policial,idTipoInqueritoPolicial',
            'numero_bo'               => 'string|max:50',
            'data'                    => 'date',
            'assistencia_juridica'    => 'string|max:100'
        ]);

        $inqueritoPolicial = InqueritoPolicial::create($validatedData);
        return response()->json($inqueritoPolicial, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/inquerito-policial/{id}",
     *     tags={"InqueritosPoliciais"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um inquerito policial específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do inquerito policial",
     *         @OA\JsonContent(ref="#/components/schemas/InqueritoPolicial")
     *     )
     * )
     */
    public function show($id)
    {
        $inqueritoPolicial = InqueritoPolicial::with(['conflito', 'tipo_inquerito_policial'])->findOrFail($id);
        return response()->json($inqueritoPolicial);
    }

    /**
     * @OA\Put(
     *     path="/api/inquerito-policial/{id}",
     *     tags={"InqueritosPoliciais"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um inquerito policial específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"idConflito", "idTipoInqueritoPolicial", "numero_bo", "data", "assistencia_juridica"},
     *             @OA\Property(property="idConflito", type="integer", example="1"),
     *             @OA\Property(property="idTipoInqueritoPolicial", type="integer", example="1"),
     *             @OA\Property(property="numero_bo", type="string", example="123.456.789"),
     *             @OA\Property(property="data", type="date", example="yyyy-mm-dd"),
     *             @OA\Property(property="assistencia_juridica", type="string", example="Inquerito Policial")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Inquerito Policial atualizado"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $inqueritoPolicial = InqueritoPolicial::findOrFail($id);

        $validatedData = $request->validate([
            'idConflito'              => 'required|integer|exists:conflito,idConflito',
            'idTipoInqueritoPolicial' => 'required|integer|exists:tipo_inquerito_policial,idTipoInqueritoPolicial',
            'numero_bo'               => 'string|max:50',
            'data'                    => 'date',
            'assistencia_juridica'    => 'string|max:100'
        ]);

        $inqueritoPolicial->update($validatedData);
        return response()->json($inqueritoPolicial);
    }

    /**
     * @OA\Delete(
     *     path="/api/inquerito-policial/{id}",
     *     tags={"InqueritosPoliciais"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um inquerito policial específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Inquerito Policial excluída"
     *     )
     * )
     */
    public function destroy($id)
    {
        $inqueritoPolicial = InqueritoPolicial::findOrFail($id);
        $inqueritoPolicial->delete();
        return response()->json(null, 204);
    }
    
    /**
     * @OA\Get(
     *     path="/api/inquerito-policial/conflito/{idConflito}",
     *     tags={"InqueritosPoliciais"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os inqueritos policiais de um Conflito",
     *     @OA\Parameter(
     *         name="idConflito",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de inqueritos policiais"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Termo de pesquisa não fornecido"
     *     )
     * )
     */
    public function getAllByConflito($idConflito){
        try {
            return InqueritoPolicial::with(['conflito', 'tipo_inquerito_policial'])->where('idConflito', $idConflito)->get();
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro na consulta',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
