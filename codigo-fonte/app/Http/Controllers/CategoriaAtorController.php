<?php
namespace App\Http\Controllers;

use App\Models\CategoriaAtor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 *
 * @OA\Schema(
 *     schema="CategoriaAtor",
 *     type="object",
 *     @OA\Property(property="idCategoriaAtor", type="integer", example="1"),
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
     *             @OA\Property(property="nome", type="string", example="Latifundiário")
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
     *             @OA\Property(property="nome", type="string", example="Latifundiário")
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
     * @OA\Get(
     *     path="/api/categoria-ator/pesquisar/buscar-texto",
     *     summary="Pesquisa categoriaAtor por texto",
     *     description="Retorna uma lista de categoriaAtor cujos nomes correspondem ao termo de pesquisa",
     *     tags={"Perfis"},
     *     security={ {"sanctum": {} } },
     *     @OA\Parameter(
     *         name="texto",
     *         description="Texto para pesquisa de categoriaAtor",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de CategoriaAtor encontrados",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/CategoriaAtor")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Termo de pesquisa não fornecido"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Nenhum resultado encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     )
     * )
     */
    public function getAllByTexto(Request $request)
    {
        try {
            // 1. Verificação de Auth (Pode ser removida se usar rota protegida por middleware)
            if (!Auth::guard('sanctum')->check()) {
                return response()->json([
                    'message' => 'Não autorizado',
                    'status'  => Response::HTTP_UNAUTHORIZED
                ], Response::HTTP_UNAUTHORIZED);
            }
            
            // 2. Validação
            $validated = $request->validate([
                'texto' => 'required|string|min:2'
            ], [
                'texto.required' => 'O termo é obrigatório.',
                'texto.string'   => 'O termo deve ser uma string.',
                'texto.min'      => 'O termo deve ter no mínimo :min caracteres.'
            ]);
            
            // 3. Consulta (Sintaxe Corrigida: ::where)
            $categoriasAtor = CategoriaAtor::where('nome', 'LIKE', '%' . $validated['texto'] . '%')->get();
            
            // 4. Verificação de Vazio
            if ($categoriasAtor->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhum resultado encontrado'
                ], Response::HTTP_NOT_FOUND);
            }
            
            // 5. Retorno de Sucesso (Padronizado como JSON)
            return response()->json($categoriasAtor, Response::HTTP_OK);
            
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors'  => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
            
        } catch (\Exception $e) {
            // Logar o erro real internamente é uma boa prática aqui: Log::error($e);
            return response()->json([
                'error'   => 'Erro interno na pesquisa',
                // Cuidado: Em produção, evite enviar $e->getMessage() para o usuário final
                'details' => config('app.debug') ? $e->getMessage() : 'Contate o suporte'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
