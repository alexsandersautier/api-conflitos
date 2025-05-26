<?php

namespace App\Http\Controllers;

use App\Models\TipoInqueritoPolicial;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @OA\Schema(
 *     schema="TipoInqueritoPolicial",
 *     type="object",
 *     @OA\Property(property="idTipoInqueritoPolicial", type="integer", example="3"),
 *     @OA\Property(property="nome", type="string", example="Processos em tramitação na justica federal")
 * )
 * 
 * @OA\PathItem(
 *     path="/api/tipo-inquerito-policial"
 * )
 *
 * @OA\Tag(
 *     name="TiposInqueritoPolicial",
 *     description="Endpoints para Tipos de Inquerito Policial"
 * )
 */
class TipoInqueritoPolicialController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tipo-inquerito-policial",
     *     tags={"TiposInqueritoPolicial"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os tipos de Inquerito Policial",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tipos de Inquerito Policial",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/TipoInqueritoPolicial")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $tipoinqueritopolicial = TipoInqueritoPolicial::all();
        return response()->json($tipoinqueritopolicial, Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/tipo-inquerito-policial",
     *     tags={"TiposInqueritoPolicial"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo tipo de Inquerito Policial",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Tipo de Inquerito Policial")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tipo de Inquerito Policial criado"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $tipoinqueritopolicial = TipoInqueritoPolicial::create($validatedData);
        return response()->json($tipoinqueritopolicial, Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/tipo-inquerito-policial/{id}",
     *     tags={"TiposInqueritoPolicial"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um tipo de Inquerito Policial específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do tipo de Inquerito Policial",
     *         @OA\JsonContent(ref="#/components/schemas/TipoInqueritoPolicial")
     *     )
     * )
     */
    public function show($id)
    {
        $tipoinqueritopolicial = TipoInqueritoPolicial::findOrFail($id);
        return response()->json($tipoinqueritopolicial, Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/tipo-inquerito-policial/{id}",
     *     tags={"TiposInqueritoPolicial"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um tipo de Inquerito Policial específico",
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
     *             @OA\Property(property="nome", type="string", example="Tipo de Inquerito Policial")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de Inquerito Policial atualizado"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $tipoinqueritopolicial = TipoInqueritoPolicial::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $tipoinqueritopolicial->update($validatedData);
        return response()->json($tipoinqueritopolicial, Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/tipo-inquerito-policial/{id}",
     *     tags={"TiposInqueritoPolicial"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um tipo de Inquerito Policial específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Tipo de Inquerito Policial excluído"
     *     )
     * )
     */
    public function destroy($id)
    {
        $tipoinqueritopolicial = TipoInqueritoPolicial::findOrFail($id);
        $tipoinqueritopolicial->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
