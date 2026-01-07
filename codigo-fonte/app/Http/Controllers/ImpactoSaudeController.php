<?php
namespace App\Http\Controllers;

use App\Models\ImpactoSaude;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

/**
 *
 *  @OA\Schema(
 *     schema="ImpactoSaude",
 *     type="object",
 *     @OA\Property(property="idImpactoSaude", type="integer", example="1"),
 *     @OA\Property(property="nome", type="string", example="Lesões por acidentes")
 * )
 *
 * @OA\PathItem(
 *     path="/api/impacto-saude"
 * )
 *
 * @OA\Tag(
 *     name="ImpactosSaude",
 *     description="Endpoints para Impactos de Saúde"
 * )
 */
class ImpactoSaudeController extends Controller
{

    /**
     *
     * @OA\Get(
     *     path="/api/impacto-saude",
     *     tags={"ImpactosSaude"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os impactos de saúde",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de impactos de saúde",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ImpactoSaude")
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

        $impactosaude = ImpactoSaude::all();
        return response()->json($impactosaude);
    }

    /**
     *
     * @OA\Post(
     *     path="/api/impacto-saude",
     *     tags={"ImpactosSaude"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo Impacto Saude",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Lesões por acidentes")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Impacto na Saude criado"
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

        $impactosaude = ImpactoSaude::create($validatedData);
        return response()->json($impactosaude, Response::HTTP_CREATED);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/impacto-saude/{id}",
     *     tags={"ImpactosSaude"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um Impacto na Saude específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do Impacto Saude",
     *         @OA\JsonContent(ref="#/components/schemas/ImpactoSaude")
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

        $impactosaude = ImpactoSaude::findOrFail($id);
        return response()->json($impactosaude);
    }

    /**
     *
     * @OA\Put(
     *     path="/api/impacto-saude/{id}",
     *     tags={"ImpactosSaude"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um Impacto na Saude específico",
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
     *             @OA\Property(property="nome", type="string", example="Lesões por acidentes")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Impacto Saude atualizado"
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

        $impactosaude = ImpactoSaude::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255'
        ]);

        $impactosaude->update($validatedData);
        return response()->json($impactosaude);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/impacto-saude/pesquisar/buscar-texto",
     *     summary="Pesquisa ImpactoSaude por texto",
     *     description="Retorna uma lista de ImpactoSaude cujos nomes correspondem ao termo de pesquisa",
     *     tags={"ImpactosSaude"},
     *     security={ {"sanctum": {} } },
     *     @OA\Parameter(
     *         name="texto",
     *         description="Texto para pesquisa de ImpactoSaude",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de ImpactoSaude encontrados",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ImpactoSaude")
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
            $perfis = ImpactoSaude::where('nome', 'LIKE', '%' . $validated['texto'] . '%')->get();
            
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
     *     path="/api/impacto-saude/{id}",
     *     tags={"ImpactosSaude"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um impacto saude específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Impacto Saude excluído"
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

        $impactosaude = ImpactoSaude::findOrFail($id);
        $impactosaude->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
