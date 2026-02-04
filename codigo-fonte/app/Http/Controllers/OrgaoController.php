<?php
namespace App\Http\Controllers;

use App\Models\Orgao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

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
     *
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
        if (! Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        $orgaos = Orgao::all();
        return response()->json($orgaos);
    }

    /**
     *
     * @OA\Post(
     *     path="/api/orgao",
     *     tags={"Orgaos"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo orgão",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="MPI")
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
        if (! Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255'
        ]);

        $orgao = Orgao::create($validatedData);
        return response()->json($orgao, Response::HTTP_CREATED);
    }

    /**
     *
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
        if (! Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        $orgao = Orgao::findOrFail($id);
        return response()->json($orgao);
    }

    /**
     *
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
        if (! Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        $orgao = Orgao::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255'
        ]);

        $orgao->update($validatedData);
        return response()->json($orgao);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/orgao/pesquisar/buscar-texto",
     *     summary="Pesquisa orgao por texto",
     *     description="Retorna uma lista de orgao cujos nomes correspondem ao termo de pesquisa",
     *     tags={"Orgaos"},
     *     security={ {"sanctum": {} } },
     *     @OA\Parameter(
     *         name="texto",
     *         description="Texto para pesquisa de orgao",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de Orgao encontrados",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Orgao")
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
            $orgaos = Orgao::where('nome', 'LIKE', '%' . $validated['texto'] . '%')->get();
            
            // 4. Verificação de Vazio
            if ($orgaos->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhum resultado encontrado'
                ], Response::HTTP_NOT_FOUND);
            }
            
            // 5. Retorno de Sucesso (Padronizado como JSON)
            return response()->json($orgaos, Response::HTTP_OK);
            
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
        if (! Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        $orgao = Orgao::findOrFail($id);
        $orgao->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
