<?php

namespace App\Http\Controllers;

use App\Models\Orgao;
use Illuminate\Http\Request;

/**
 * 
 *  @OA\Schema(
 *     schema="Orgao",
 *     type="object",
 *     @OA\Property(property="idOrgao", type="integer", example="1"),
 *     @OA\Property(property="nome", type="string", example="MPI")
 * )
 * 
 * @OA\PathItem(
 *     path="/api/orgao"
 * )
 *
 * @OA\Tag(
 *     name="Orgaos",
 *     description="Endpoints para Órgãos"
 * )
 */
class OrgaoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/orgao",
     *     tags={"Orgaos"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os orgãos",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de orgãos",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Orgao")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $orgaos = Orgao::all();
        return response()->json($orgaos);
    }

    /**
     * @OA\Post(
     *     path="/api/orgao",
     *     tags={"Orgaos"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo orgão",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Departamento de TI")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Orgão criado"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $orgao = Orgao::create($validatedData);
        return response()->json($orgao, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/orgao/{id}",
     *     tags={"Orgaos"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um orgão específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do orgão",
     *         @OA\JsonContent(ref="#/components/schemas/Orgao")
     *     )
     * )
     */
    public function show($id)
    {
        $orgao = Orgao::findOrFail($id);
        return response()->json($orgao);
    }

    /**
     * @OA\Put(
     *     path="/api/orgao/{id}",
     *     tags={"Orgaos"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um orgão específico",
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
     *             @OA\Property(property="nome", type="string", example="MPI")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Orgão atualizado"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $orgao = Orgao::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $orgao->update($validatedData);
        return response()->json($orgao);
    }

    /**
     * @OA\Delete(
     *     path="/api/orgao/{id}",
     *     tags={"Orgaos"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um orgão específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Orgão excluído"
     *     )
     * )
     */
    public function destroy($id)
    {
        $orgao = Orgao::findOrFail($id);
        $orgao->delete();
        return response()->json(null, 204);
    }
}
