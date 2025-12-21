<?php
namespace App\Http\Controllers;

use App\Models\Povo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

/**
 *
 *  @OA\Schema(
 *     schema="Povo",
 *     type="object",
 *     @OA\Property(property="idPovo", type="integer", example="1"),
 *     @OA\Property(property="nome", type="string", example="Tupi-guarani"),
 *     @OA\Property(property="codEtnia", type="string", example="267.00"),
 *     @OA\Property(property="lingua", type="string", example="Tupi-guarani"),
 *     @OA\Property(property="familia_linguistica", type="string", example="Tupi"),
 *     @OA\Property(property="ufs_povos", type="string", example="PA,MA"),
 *     @OA\Property(property="qtd_ti_povo", type="integer", example="3"),
 * )
 *
 * @OA\PathItem(
 *     path="/api/povo"
 * )
 *
 * @OA\Tag(
 *     name="Povos",
 *     description="Endpoints para Povos"
 * )
 */
class PovoController extends Controller
{

    /**
     *
     * @OA\Get(
     *     path="/api/povo",
     *     tags={"Povos"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os povos",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de povos",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Povo")
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

        $povos = Povo::all();
        return response()->json($povos);
    }

    /**
     *
     * @OA\Post(
     *     path="/api/povo",
     *     tags={"Povos"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo povo",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Tupi-guarani"),
     *             @OA\Property(property="codEtnia", type="string", example="267.00"),
     *             @OA\Property(property="lingua", type="string", example="Tupi-guarani"),
     *             @OA\Property(property="familia_linguistica", type="string", example="Tupi"),
     *             @OA\Property(property="ufs_povos", type="string", example="PA,MA"),
     *             @OA\Property(property="qtd_ti_povo", type="integer", example="3")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Povo criado"
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
            'nome' => 'required|string|max:255',
            'codEtnia' => 'required|string',
            'lingua' => 'string|max:255',
            'ufs_povo' => 'string|max:255',
            'qtd_ti_povo' => 'integer|min:0'
        ]);

        $povo = Povo::create($validatedData);
        return response()->json($povo, Response::HTTP_CREATED);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/povo/{id}",
     *     tags={"Povos"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um povo específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do povo",
     *         @OA\JsonContent(ref="#/components/schemas/Povo")
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

        $povo = Povo::findOrFail($id);
        return response()->json($povo);
    }

    /**
     *
     * @OA\Put(
     *     path="/api/povo/{id}",
     *     tags={"Povos"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um povo específico",
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
     *             @OA\Property(property="nome", type="string", example="Tupi-guarani"),
     *             @OA\Property(property="codEtnia", type="string", example="267.00"),
     *             @OA\Property(property="lingua", type="string", example="Tupi-guarani"),
     *             @OA\Property(property="familia_linguistica", type="string", example="Tupi"),
     *             @OA\Property(property="ufs_povos", type="string", example="PA,MA"),
     *             @OA\Property(property="qtd_ti_povo", type="integer", example="3")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Povo atualizado"
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

        $povo = Povo::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'codEtnia' => 'required|string',
            'lingua' => 'string|max:255',
            'ufs_povo' => 'string|max:255',
            'qtd_ti_povo' => 'integer|min:0'
        ]);

        $povo->update($validatedData);
        return response()->json($povo);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/povo/pesquisar/buscar-texto",
     *     summary="Pesquisa Povo por texto",
     *     description="Retorna uma lista de Povo cujos nomes correspondem ao termo de pesquisa",
     *     tags={"Povos"},
     *     security={ {"sanctum": {} } },
     *     @OA\Parameter(
     *         name="texto",
     *         description="Texto para pesquisa de Povo",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de Povo encontrados",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Povo")
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
            $povos = Povo::where('nome', 'LIKE', '%' . $validated['texto'] . '%')->get();
            
            // 4. Verificação de Vazio
            if ($povos->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhum resultado encontrado'
                ], Response::HTTP_NOT_FOUND);
            }
            
            // 5. Retorno de Sucesso (Padronizado como JSON)
            return response()->json($povos, Response::HTTP_OK);
            
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
     *     path="/api/povo/{id}",
     *     tags={"Povos"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um povo específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Povo excluído"
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

        $povo = Povo::findOrFail($id);
        $povo->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
