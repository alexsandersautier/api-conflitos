<?php

namespace App\Http\Controllers;

use App\Models\Episodio;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="Episodio",
 *     type="object",
 *     @OA\Property(property="idEpisodio", type="integer", example=1),
 *     @OA\Property(property="titulo",   type="string",   example="Título do episodio"),
 *     @OA\Property(property="conflito", type="object",   description="Conflito vinculado", ref="#/components/schemas/Conflito"),
 *     @OA\Property(property="dataHora", type="datetime", example="Data/Hora do episodio"),
 * )
 * 
 * @OA\PathItem(
 *     path="/api/episodio"
 * )
 *
 * @OA\Tag(
 *     name="Episodios",
 *     description="Endpoints para Episódios"
 * )
 */
class EpisodioController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/episodio",
     *     tags={"Episodios"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os episodios",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de episodios",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Episodio")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $episodio = Episodio::all();
        return response()->json($episodio);
    }

    /**
     * @OA\Post(
     *     path="/api/episodio",
     *     tags={"Episodios"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo episodio",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Departamento de TI")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Episodio criado"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $episodio = Episodio::create($validatedData);
        return response()->json($episodio, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/episodio/{id}",
     *     tags={"Episodios"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um episodio específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do episodio",
     *         @OA\JsonContent(ref="#/components/schemas/Episodio")
     *     )
     * )
     */
    public function show($id)
    {
        $episodio = Episodio::findOrFail($id);
        return response()->json($episodio);
    }

    /**
     * @OA\Put(
     *     path="/api/episodio/{id}",
     *     tags={"Episodios"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um episodio específico",
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
     *             @OA\Property(property="nome", type="string", example="Departamento de TI")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Episodio atualizado",
     *         @OA\JsonContent(ref="#/components/schemas/Episodio")
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $episodio = Episodio::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $episodio->update($validatedData);
        return response()->json($episodio);
    }

    /**
     * @OA\Delete(
     *     path="/api/episodio/{id}",
     *     tags={"Episodios"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um episodio específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Episodio excluído"
     *     )
     * )
     */
    public function destroy($id)
    {
        $episodio = Episodio::findOrFail($id);
        $episodio->delete();
        return response()->json(null, 204);
    }
}
