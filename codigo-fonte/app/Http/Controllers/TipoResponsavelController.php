<?php
namespace App\Http\Controllers;

use App\Models\TipoResponsavel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

/**
 *
 * @OA\Schema(
 *     schema="TipoResponsavel",
 *     type="object",
 *     @OA\Property(property="idTipoResponsavel", type="integer", example="1"),
 *     @OA\Property(property="nome", type="string", example="Latifundiário")
 * )
 *
 * @OA\PathItem(
 *     path="/api/tipo-Responsavel"
 * )
 *
 * @OA\Tag(
 *     name="TiposResponsavel",
 *     description="Endpoints para Tipos de Responsavel"
 * )
 */
class TipoResponsavelController extends Controller
{

    /**
     *
     * @OA\Get(
     *     path="/api/tipo-Responsavel",
     *     tags={"TiposResponsavel"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os tipos de Responsavel",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tipos de Responsavel",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/TipoResponsavel")
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

        $tipoResponsavel = TipoResponsavel::all();
        return response()->json($tipoResponsavel);
    }

    /**
     *
     * @OA\Post(
     *     path="/api/tipo-Responsavel",
     *     tags={"TiposResponsavel"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo tipo de Responsavel",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Latifundiário")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tipo de Responsavel criado"
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

        $tipoResponsavel = TipoResponsavel::create($validatedData);
        return response()->json($tipoResponsavel, 201);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/tipo-Responsavel/{id}",
     *     tags={"TiposResponsavel"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um tipo de Responsavel específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do tipo de Responsavel",
     *         @OA\JsonContent(ref="#/components/schemas/TipoResponsavel")
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

        $tipoResponsavel = TipoResponsavel::findOrFail($id);
        return response()->json($tipoResponsavel);
    }

    /**
     *
     * @OA\Put(
     *     path="/api/tipo-Responsavel/{id}",
     *     tags={"TiposResponsavel"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um tipo de Responsavel específico",
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
     *         description="Tipo de Responsavel atualizado"
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

        $tipoResponsavel = TipoResponsavel::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255'
        ]);

        $tipoResponsavel->update($validatedData);
        return response()->json($tipoResponsavel);
    }
    
    /**
     *
     * @OA\Get(
     *     path="/api/tipo-Responsavel/pesquisar/buscar-texto",
     *     summary="Pesquisa tipo-Responsavel por texto",
     *     description="Retorna uma lista de tipo-Responsavel cujos nomes correspondem ao termo de pesquisa",
     *     tags={"TiposResponsavel"},
     *     security={ {"sanctum": {} } },
     *     @OA\Parameter(
     *         name="texto",
     *         description="Texto para pesquisa de usuário",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de TipoResponsavel encontrados",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/TipoResponsavel")
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
            $tiposResponsaveis = TipoResponsavel::where('nome', 'LIKE', '%' . $validated['texto'] . '%')->get();
            
            // 4. Verificação de Vazio
            if ($tiposResponsaveis->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhum resultado encontrado'
                ], Response::HTTP_NOT_FOUND);
            }
            
            // 5. Retorno de Sucesso (Padronizado como JSON)
            return response()->json($tiposResponsaveis, Response::HTTP_OK);
            
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
     *     path="/api/tipo-Responsavel/{id}",
     *     tags={"TiposResponsavel"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um tipo de Responsavel específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Tipo de Responsavel excluído"
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

        $tipoResponsavel = TipoResponsavel::findOrFail($id);
        $tipoResponsavel->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
