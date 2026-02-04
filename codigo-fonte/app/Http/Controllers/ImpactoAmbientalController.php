<?php
namespace App\Http\Controllers;

use App\Models\ImpactoAmbiental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

/**
 *
 *  @OA\Schema(
 *     schema="ImpactoAmbiental",
 *     type="object",
 *     @OA\Property(property="idImpactoAmbiental", type="integer", example="1"),
 *     @OA\Property(property="nome", type="string", example="Contaminação de cursos hídricos")
 * )
 *
 * @OA\PathItem(
 *     path="/api/impacto-ambiental"
 * )
 *
 * @OA\Tag(
 *     name="ImpactosAmbientais",
 *     description="Endpoints para Impactos Ambientais"
 * )
 */
class ImpactoAmbientalController extends Controller
{

    /**
     *
     * @OA\Get(
     *     path="/api/impacto-ambiental",
     *     tags={"ImpactosAmbientais"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os impactos ambientais",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de impactos ambientais",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ImpactoAmbiental")
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

        $impactoambiental = ImpactoAmbiental::all();
        return response()->json($impactoambiental);
    }

    /**
     *
     * @OA\Post(
     *     path="/api/impacto-ambiental",
     *     tags={"ImpactosAmbientais"},
     *     security={ {"sanctum": {} } },     *
     *     summary="Criar um novo impacto ambiental",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Contaminação de cursos hídricos")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Impacto Ambiental criado"
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

        $impactoambiental = ImpactoAmbiental::create($validatedData);
        return response()->json($impactoambiental, Response::HTTP_CREATED);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/impacto-ambiental/{id}",
     *     tags={"ImpactosAmbientais"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um impacto ambiental específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do impacto ambiental",
     *         @OA\JsonContent(ref="#/components/schemas/ImpactoAmbiental")
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

        $impactoambiental = ImpactoAmbiental::findOrFail($id);
        return response()->json($impactoambiental);
    }

    /**
     *
     * @OA\Put(
     *     path="/api/impacto-ambiental/{id}",
     *     tags={"ImpactosAmbientais"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um impacto ambiental específico",
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
     *             @OA\Property(property="nome", type="string", example="Contaminação de cursos hídricos")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Impacto Ambiental atualizado"
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

        $impactoambiental = ImpactoAmbiental::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255'
        ]);

        $impactoambiental->update($validatedData);
        return response()->json($impactoambiental);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/impacto-ambiental/pesquisar/buscar-texto",
     *     summary="Pesquisa ImpactoAmbiental por texto",
     *     description="Retorna uma lista de ImpactoAmbiental cujos nomes correspondem ao termo de pesquisa",
     *     tags={"ImpactosAmbientais"},
     *     security={ {"sanctum": {} } },
     *     @OA\Parameter(
     *         name="texto",
     *         description="Texto para pesquisa de ImpactoAmbiental",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de ImpactoAmbiental encontrados",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ImpactoAmbiental")
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
            $perfis = ImpactoAmbiental::where('nome', 'LIKE', '%' . $validated['texto'] . '%')->get();
            
            // 4. Verificação de Vazio
            if ($perfis->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhum resultado encontrado'
                ], Response::HTTP_NOT_FOUND);
            }
            
            // 5. Retorno de Sucesso (Padronizado como JSON)
            return response()->json($perfis, Response::HTTP_OK);
            
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
     *     path="/api/impacto-ambiental/{id}",
     *     tags={"ImpactosAmbientais"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um impacto ambiental específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Impacto Ambiental excluído"
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

        $impactoambiental = ImpactoAmbiental::findOrFail($id);
        $impactoambiental->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
