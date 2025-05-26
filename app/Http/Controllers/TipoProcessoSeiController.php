<?php

namespace App\Http\Controllers;

use App\Models\TipoProcessoSei;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="TipoProcessoSei",
 *     type="object",
 *     @OA\Property(property="idTipoProcessoSei", type="integer", example="2"),
 *     @OA\Property(property="nome", type="string", example="Tipo de Processo B")
 * )
 * 
 * @OA\PathItem(
 *     path="/api/tipo-processo-sei"
 * )
 *
 * @OA\Tag(
 *     name="TiposProcessoSei",
 *     description="Endpoints para Tipos de Processo Sei"
 * )
 */
class TipoProcessoSeiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tipo-processo-sei",
     *     tags={"TiposProcessoSei"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os tipos de processo sei",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tipos de processo sei",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/TipoProcessoSei")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $tipoprocessosei = TipoProcessoSei::all();
        return response()->json($tipoprocessosei);
    }

    /**
     * @OA\Post(
     *     path="/api/tipo-processo-sei",
     *     tags={"TiposProcessoSei"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo tipo de processo sei",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Tipo de Processo SEI")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tipo Processo SEI criado",
     *         @OA\JsonContent(ref="#/components/schemas/TipoProcessoSei")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $tipoprocessosei = TipoProcessoSei::create($validatedData);
        return response()->json($tipoprocessosei, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/tipo-processo-sei/{id}",
     *     tags={"TiposProcessoSei"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um tipo de processo sei específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do tipo de processo sei",
     *         @OA\JsonContent(ref="#/components/schemas/TipoProcessoSei")
     *     )
     * )
     */
    public function show($id)
    {
        $tipoprocessosei = TipoProcessoSei::findOrFail($id);
        return response()->json($tipoprocessosei);
    }

    /**
     * @OA\Put(
     *     path="/api/tipo-processo-sei/{id}",
     *     tags={"TiposProcessoSei"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um tipo de processo sei específico",
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
     *             @OA\Property(property="nome", type="string", example="Tipo de Processo SEI")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de Processo SEI atualizado"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $tipoprocessosei = TipoProcessoSei::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $tipoprocessosei->update($validatedData);
        return response()->json($tipoprocessosei);
    }

    /**
     * @OA\Delete(
     *     path="/api/tipo-processo-sei/{id}",
     *     tags={"TiposProcessoSei"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um Tipo de Processo SEI específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Tipo de Processo SEI excluído"
     *     )
     * )
     */
    public function destroy($id)
    {
        $tipoprocessosei = TipoProcessoSei::findOrFail($id);
        $tipoprocessosei->delete();
        return response()->json(null, 204);
    }
}
