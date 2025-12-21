<?php
namespace App\Http\Controllers;

use App\Models\Ator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

/**
 *
 *  @OA\Schema(
 *     schema="Ator",
 *     type="object",
 *     @OA\Property(property="idAtor", type="integer", example="1"),
 *     @OA\Property(property="nome", type="string", example="João da Silva")
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
     *
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
        $atores = Ator::query()->orderBy('nome', 'asc')->get();
        return response()->json($atores);
    }

    /**
     *
     * @OA\Post(
     *     path="/api/ator",
     *     tags={"Atores"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo ator",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
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
        if (! Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        $validatedData = $request->validate([
            'nome' => 'required|string|max:100'
        ]);

        $ator = Ator::create($validatedData);
        return response()->json($ator, Response::HTTP_CREATED);
    }

    /**
     *
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
        if (! Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        $ator = Ator::findOrFail($id);
        return response()->json($ator);
    }

    /**
     *
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
     *             required={"nome"},
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
        if (! Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        $ator = Ator::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:100'
        ]);

        $ator->update($validatedData);
        return response()->json($ator);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/ator/pesquisar/buscar-texto",
     *     summary="Pesquisa ator por texto",
     *     description="Retorna uma lista de ator cujos nomes correspondem ao termo de pesquisa",
     *     tags={"Ators"},
     *     security={ {"sanctum": {} } },
     *     @OA\Parameter(
     *         name="texto",
     *         description="Texto para pesquisa de ator",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de Ator encontrados",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Ator")
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
            $atores = Ator::where('nome', 'LIKE', '%' . $validated['texto'] . '%')->get();
            
            // 4. Verificação de Vazio
            if ($atores->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhum resultado encontrado'
                ], Response::HTTP_NOT_FOUND);
            }
            
            // 5. Retorno de Sucesso (Padronizado como JSON)
            return response()->json($atores, Response::HTTP_OK);
            
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
        if (! Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        $ator = Ator::findOrFail($id);
        $ator->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}