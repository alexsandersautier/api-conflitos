<?php
namespace App\Http\Controllers;

use App\Models\CategoriaAtor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 *
 * @OA\Schema(
 *     schema="CategoriaAtor",
 *     type="object",
 *     @OA\Property(property="idCategoriaAtor", type="integer"),
 *     @OA\Property(property="nome", type="string", example="Latifundiário")
 * )
 *
 * @OA\PathItem(
 *     path="/api/categoria-ator"
 * )
 *
 * @OA\Tag(
 *     name="CategoriasAtor",
 *     description="Endpoints para Categorias de Ator"
 * )
 */
class CategoriaAtorController extends Controller
{

    /**
     *
     * @OA\Get(
     *     path="/api/categoria-ator",
     *     tags={"CategoriasAtor"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os categorias de ator",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de categorias de ator",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/CategoriaAtor")
     *         )
     *     )
     * )
     */
    public function index()
    {
        if (! Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        $categoriaator = CategoriaAtor::all();
        return response()->json($categoriaator);
    }

    /**
     *
     * @OA\Post(
     *     path="/api/categoria-ator",
     *     tags={"CategoriasAtor"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo categoria de ator",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Categoria de Ator do Conflito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Categoria de Ator criado"
     *     )
     * )
     */
    public function store(Request $request)
    {
        if (! Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255'
        ]);

        $categoriaator = CategoriaAtor::create($validatedData);
        return response()->json($categoriaator, Response::HTTP_CREATED);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/categoria-ator/{id}",
     *     tags={"CategoriasAtor"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um categoria de ator específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do categoria de ator",
     *         @OA\JsonContent(ref="#/components/schemas/CategoriaAtor")
     *     )
     * )
     */
    public function show($id)
    {
        if (! Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        $categoriaator = CategoriaAtor::findOrFail($id);
        return response()->json($categoriaator);
    }

    /**
     *
     * @OA\Put(
     *     path="/api/categoria-ator/{id}",
     *     tags={"CategoriasAtor"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um categoria de ator específico",
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
     *             @OA\Property(property="nome", type="string", example="Categoria de Ator do Conflito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoria de Ator atualizado"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        if (! Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        $categoriaator = CategoriaAtor::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255'
        ]);

        $categoriaator->update($validatedData);
        return response()->json($categoriaator);
    }

    /**
     *
     * @OA\Delete(
     *     path="/api/categoria-ator/{id}",
     *     tags={"CategoriasAtor"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um categoria de ator específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Categoria de Ator excluído"
     *     )
     * )
     */
    public function destroy($id)
    {
        if (! Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        $categoriaator = CategoriaAtor::findOrFail($id);
        $categoriaator->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
