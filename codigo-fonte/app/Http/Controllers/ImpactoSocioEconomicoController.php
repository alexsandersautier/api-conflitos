<?php
namespace App\Http\Controllers;

use App\Models\ImpactoSocioEconomico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

/**
 *
 *  @OA\Schema(
 *     schema="ImpactoSocioEconomico",
 *     type="object",
 *     @OA\Property(property="idImpactoSocioEconomico", type="integer", example="1"),
 *     @OA\Property(property="nome", type="string", example="Aliciamento e cooptação da população indígena")
 * )
 *
 * @OA\PathItem(
 *     path="/api/impacto-socio-economico"
 * )
 *
 * @OA\Tag(
 *     name="ImpactoSocioEconomico",
 *     description="Endpoints para Impactos Sócio Econômicos"
 * )
 */
class ImpactoSocioEconomicoController extends Controller
{

    /**
     *
     * @OA\Get(
     *     path="/api/impacto-socio-economico",
     *     tags={"ImpactoSocioEconomico"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os Impactos Sócio Econômicos",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de Impactos Sócio Econômicos",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ImpactoSocioEconomico")
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

        $impactosocioeconomico = ImpactoSocioEconomico::all();
        return response()->json($impactosocioeconomico);
    }

    /**
     *
     * @OA\Post(
     *     path="/api/impacto-socio-economico",
     *     tags={"ImpactoSocioEconomico"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo Impacto Sócio Econômicos",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Aliciamento e cooptação da população indígena")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Impacto Sócio Econômicos criado"
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

        $impactosocioeconomico = ImpactoSocioEconomico::create($validatedData);
        return response()->json($impactosocioeconomico, Response::HTTP_CREATED);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/impacto-socio-economico/{id}",
     *     tags={"ImpactoSocioEconomico"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um Impacto Sócio Econômico específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do Impacto Sócio Econômico",
     *         @OA\JsonContent(ref="#/components/schemas/ImpactoSocioEconomico")
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

        $impactosocioeconomico = ImpactoSocioEconomico::findOrFail($id);
        return response()->json($impactosocioeconomico);
    }

    /**
     *
     * @OA\Put(
     *     path="/api/impacto-socio-economico/{id}",
     *     tags={"ImpactoSocioEconomico"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um Impacto Sócio Econômico específico",
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
     *             @OA\Property(property="nome", type="string", example="Aliciamento e cooptação da população indígena")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Impacto Sócio Econômico atualizado"
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

        $impactosocioeconomico = ImpactoSocioEconomico::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255'
        ]);

        $impactosocioeconomico->update($validatedData);
        return response()->json($impactosocioeconomico);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/impacto-socio-economico/pesquisar/buscar-texto",
     *     summary="Pesquisa ImpactoSocioEconomico por texto",
     *     description="Retorna uma lista de ImpactoSocioEconomico cujos nomes correspondem ao termo de pesquisa",
     *     tags={"ImpactoSocioEconomico"},
     *     security={ {"sanctum": {} } },
     *     @OA\Parameter(
     *         name="texto",
     *         description="Texto para pesquisa de ImpactoSocioEconomico",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de ImpactoSocioEconomico encontrados",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ImpactoSocioEconomico")
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
            $perfis = ImpactoSocioEconomico::where('nome', 'LIKE', '%' . $validated['texto'] . '%')->get();
            
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
     *     path="/api/impacto-socio-economico/{id}",
     *     tags={"ImpactoSocioEconomico"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um Impacto Sócio Econômico específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Impacto Sócio Econômico excluído"
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

        $impactosocioeconomico = ImpactoSocioEconomico::findOrFail($id);
        $impactosocioeconomico->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
