<?php
namespace App\Http\Controllers;

use App\Models\TipoConflito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

/**
 *
 * @OA\Schema(
 *     schema="TipoConflito",
 *     type="object",
 *     @OA\Property(property="idTipoConflito", type="integer", example="1"),
 *     @OA\Property(property="nome", type="string", example="Prevenção e acompanhamento de conflito: Disputas territoriais"),
 * )
 *
 * @OA\PathItem(
 *     path="/api/tipo-conflito"
 * )
 *
 * @OA\Tag(
 *     name="TiposConflito",
 *     description="Endpoints para Tipos de Conflito"
 * )
 */
class TipoConflitoController extends Controller
{

    /**
     *
     * @OA\Get(
     *     path="/api/tipo-conflito",
     *     tags={"TiposConflito"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os tipos de conflito",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tipos de conflito",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/TipoConflito")
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

        $tipoconflito = TipoConflito::all();
        return response()->json($tipoconflito);
    }

    /**
     *
     * @OA\Post(
     *     path="/api/tipo-conflito",
     *     tags={"TiposConflito"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo tipo de conflito",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Prevenção e acompanhamento de conflito: Disputas territoriais")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tipo de Conflito criado"
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

        $tipoconflito = TipoConflito::create($validatedData);
        return response()->json($tipoconflito, Response::HTTP_CREATED);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/tipo-conflito/{id}",
     *     tags={"TiposConflito"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um tipo de conflito específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do tipo de conflito",
     *         @OA\JsonContent(ref="#/components/schemas/TipoConflito")
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

        $tipoconflito = TipoConflito::findOrFail($id);
        return response()->json($tipoconflito);
    }

    /**
     *
     * @OA\Put(
     *     path="/api/tipo-conflito/{id}",
     *     tags={"TiposConflito"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um tipo de conflito específico",
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
     *             @OA\Property(property="nome", type="string", example="Prevenção e acompanhamento de conflito: Disputas territoriais")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de Conflito atualizado"
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

        $tipoconflito = TipoConflito::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255'
        ]);

        $tipoconflito->update($validatedData);
        return response()->json($tipoconflito);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/tipo-conflito/pesquisar/buscar-texto",
     *     summary="Pesquisa Tipo de Conflito por texto",
     *     description="Retorna uma lista de TipoConflito cujos nomes correspondem ao termo de pesquisa",
     *     tags={"TiposConflito"},
     *     security={ {"sanctum": {} } },
     *     @OA\Parameter(
     *         name="texto",
     *         description="Texto para pesquisa de Tipo de Conflito",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de Tipo de Conflito encontrados",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/TipoConflito")
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
            $tiposConflito = TipoConflito::where('nome', 'LIKE', '%' . $validated['texto'] . '%')->get();
            
            // 4. Verificação de Vazio
            if ($tiposConflito->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhum resultado encontrado'
                ], Response::HTTP_NOT_FOUND);
            }
            
            // 5. Retorno de Sucesso (Padronizado como JSON)
            return response()->json($tiposConflito, Response::HTTP_OK);
            
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
     *     path="/api/tipo-conflito/{id}",
     *     tags={"TiposConflito"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um tipo de conflito específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Tipo de Conflito excluído"
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

        $tipoconflito = TipoConflito::findOrFail($id);
        $tipoconflito->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
