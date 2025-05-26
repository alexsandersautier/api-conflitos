<?php

namespace App\Http\Controllers;

use App\Models\ImpactoSaude;
use Illuminate\Http\Request;

/**
 * 
 *  @OA\Schema(
 *     schema="ImpactoSaude",
 *     type="object",
 *     @OA\Property(property="idImpactoSaude", type="integer", example="1"),
 *     @OA\Property(property="nome", type="string", example="Nome do Impacto Saúde")
 * )
 * 
 * @OA\PathItem(
 *     path="/api/impacto-saude"
 * )
 *
 * @OA\Tag(
 *     name="ImpactosSaude",
 *     description="Endpoints para Impactos de Saúde"
 * )
 */
class ImpactoSaudeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/impacto-saude",
     *     tags={"ImpactosSaude"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os impactos de saúde",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de impactos de saúde",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ImpactoSaude")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $impactosaude = ImpactoSaude::all();
        return response()->json($impactosaude);
    }

    /**
     * @OA\Post(
     *     path="/api/impacto-saude",
     *     tags={"ImpactosSaude"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo Impacto Saude",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Epidemia")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Impacto na Saude criado"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $impactosaude = ImpactoSaude::create($validatedData);
        return response()->json($impactosaude, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/impacto-saude/{id}",
     *     tags={"ImpactosSaude"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um Impacto na Saude específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do Impacto Saude",
     *         @OA\JsonContent(ref="#/components/schemas/ImpactoSaude")
     *     )
     * )
     */
    public function show($id)
    {
        $impactosaude = ImpactoSaude::findOrFail($id);
        return response()->json($impactosaude);
    }

    /**
     * @OA\Put(
     *     path="/api/impacto-saude/{id}",
     *     tags={"ImpactosSaude"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um Impacto na Saude específico",
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
     *             @OA\Property(property="nome", type="string", example="Impacto")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Impacto Saude atualizado"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $impactosaude = ImpactoSaude::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $impactosaude->update($validatedData);
        return response()->json($impactosaude);
    }

    /**
     * @OA\Delete(
     *     path="/api/impacto-saude/{id}",
     *     tags={"ImpactosSaude"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um impacto saude específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Impacto Saude excluído"
     *     )
     * )
     */
    public function destroy($id)
    {
        $impactosaude = ImpactoSaude::findOrFail($id);
        $impactosaude->delete();
        return response()->json(null, 204);
    }
}
