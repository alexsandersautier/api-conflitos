<?php
namespace App\Http\Controllers;

use App\Models\SituacaoFundiaria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

/**
 *
 * @OA\Schema(
 *     schema="SituacaoFundiaria",
 *     type="object",
 *     @OA\Property(property="idSituacaoFundiaria", type="integer", example="3"),
 *     @OA\Property(property="nome", type="string", example="Declarada")
 * )
 *
 * @OA\PathItem(
 *     path="/api/situacao-fundiaria"
 * )
 *
 * @OA\Tag(
 *     name="SituacaoFundiarias",
 *     description="Endpoints para Situações Fundiárias"
 * )
 */
class SituacaoFundiariaController extends Controller
{

    /**
     *
     * @OA\Get(
     *     path="/api/situacao-fundiaria",
     *     tags={"SituacaoFundiarias"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os situações fundiarias",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de Situações Fundiárias",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/SituacaoFundiaria")
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

        $situacaofundiarias = SituacaoFundiaria::all();
        return response()->json($situacaofundiarias);
    }

    /**
     *
     * @OA\Post(
     *     path="/api/situacao-fundiaria",
     *     tags={"SituacaoFundiarias"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo situacao fundiária",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Declarada")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Situação Fundiária criada"
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

        $situacaofundiaria = SituacaoFundiaria::create($validatedData);
        return response()->json($situacaofundiaria, Response::HTTP_CREATED);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/situacao-fundiaria/{id}",
     *     tags={"SituacaoFundiarias"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um situacao fundiaria específica",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do situação fundiária",
     *         @OA\JsonContent(ref="#/components/schemas/SituacaoFundiaria")
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

        $situacaofundiaria = SituacaoFundiaria::findOrFail($id);
        return response()->json($situacaofundiaria);
    }

    /**
     *
     * @OA\Put(
     *     path="/api/situacao-fundiaria/{id}",
     *     tags={"SituacaoFundiarias"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um situacaofundiaria específico",
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
     *             @OA\Property(property="nome", type="string", example="Declarada")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Situação Fundiária atualizada"
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

        $situacaofundiaria = SituacaoFundiaria::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255'
        ]);

        $situacaofundiaria->update($validatedData);
        return response()->json($situacaofundiaria);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/situacao-fundiaria/pesquisar/buscar-texto",
     *     summary="Pesquisa situacao Fundiaria por texto",
     *     description="Retorna uma lista de situacao Fundiaria cujos nomes correspondem ao termo de pesquisa",
     *     tags={"SituacaoFundiarias"},
     *     security={ {"sanctum": {} } },
     *     @OA\Parameter(
     *         name="texto",
     *         description="Texto para pesquisa de situacao Fundiaria",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de SituacaoFundiaria encontrados",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/SituacaoFundiaria")
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
            $situacaoFundiarias = SituacaoFundiaria::where('nome', 'LIKE', '%' . $validated['texto'] . '%')->get();
            
            // 4. Verificação de Vazio
            if ($situacaoFundiarias->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhum resultado encontrado'
                ], Response::HTTP_NOT_FOUND);
            }
            
            // 5. Retorno de Sucesso (Padronizado como JSON)
            return response()->json($situacaoFundiarias, Response::HTTP_OK);
            
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
     *     path="/api/situacao-fundiaria/{id}",
     *     tags={"SituacaoFundiarias"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um situação fundiária específica",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Situação Fundiária excluída"
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

        $situacaofundiaria = SituacaoFundiaria::findOrFail($id);
        $situacaofundiaria->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
