<?php
namespace App\Http\Controllers;

use App\Models\Aldeia;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Services\AldeiaService;
use Illuminate\Validation\ValidationException;

/**
 *
 * @OA\Schema(
 *     schema="Aldeia",
 *     type="object",
 *     @OA\Property(property="idAldeia", type="integer", example=1),
 *     @OA\Property(property="nm_uf", type="string", example="Amazonas"),
 *     @OA\Property(property="nm_munic", type="string", example="Careiro"),
 *     @OA\Property(property="nome", type="string", example="Aldeia Indígena Itaboca"),
 *     @OA\Property(property="situacao", type="string", example="Rural"),
 *     @OA\Property(property="fase", type="string", example="Regularizada"),
 *     @OA\Property(property="amz_leg", type="string", example="1"),
 *     @OA\Property(property="lat", type="string", example="41.40338"),
 *     @OA\Property(property="long", type="string", example="2.17403")
 * )
 * @OA\PathItem(
 *     path="/api/aldeia"
 * )
 *
 * @OA\Tag(
 *     name="Aldeias",
 *     description="Endpoints para Aldeias"
 * )
 */
class AldeiaController extends Controller
{

    protected $aldeiaService;

    public function __construct(AldeiaService $aldeiaService)
    {
        $this->aldeiaService = $aldeiaService;

        if (ini_get('memory_limit') < 256) {
            @ini_set('memory_limit', '256M');
        }
    }

    /**
     *
     * @OA\Get(
     *     path="/api/aldeia",
     *     tags={"Aldeias"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os aldeias",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de aldeias",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Aldeia")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Acesso não autorizado"
     *     )
     * )
     */
    public function index(Request $request)
    {
        if (! Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        // $aldeias = Aldeia::all();
        $aldeias = $this->aldeiaService->getAllAldeias();
        return response()->json($aldeias);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/aldeia/paginadas",
     *     tags={"Aldeias"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os aldeias",
     *     @OA\Parameter(
     *         name="page",
     *         description="Página de registros",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         description="Registros por Página",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de aldeias",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Aldeia")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Acesso não autorizado"
     *     )
     * )
     */
    public function getAldeiasPaginated(Request $request)
    {
        if (! Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        $per_page = $request->per_page ?? 15;
        $aldeias = Aldeia::paginate($per_page);

        return response()->json($aldeias);
    }

    /**
     *
     * @OA\Post(
     *     path="/api/aldeia",
     *     tags={"Aldeias"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo aldeia",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nm_uf", "nm_munic", "nome"},
     *             @OA\Property(property="nm_uf", type="string", example="Amazonas"),
     *             @OA\Property(property="nm_munic", type="string", example="Careiro"),
     *             @OA\Property(property="nome", type="string", example="Aldeia Indígena Itaboca"),
     *             @OA\Property(property="situacao", type="string", example="Rural"),
     *             @OA\Property(property="fase", type="string", example="Regularizada"),
     *             @OA\Property(property="amz_leg", type="string", example="1"),
     *             @OA\Property(property="lat", type="string", example="41.40338"),
     *             @OA\Property(property="long", type="string", example="2.17403")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Aldeia criado"
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

        $validator = validator($request->all(), [
            'nm_uf'    => 'nullable|string|max:255',
            'nm_munic' => 'nullable|string|max:255',
            'nome'     => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Parâmetros inválidos',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        $request['carga_funai'] = 0; 
        
        $aldeia = Aldeia::create($request->only([
            'nm_uf',
            'nm_munic',
            'nome',
            'carga_funai'
        ]));
        return response()->json($aldeia, Response::HTTP_CREATED);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/aldeia/{id}",
     *     tags={"Aldeias"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um aldeia específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do aldeia",
     *         @OA\JsonContent(ref="#/components/schemas/Aldeia")
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

        $aldeia = Aldeia::findOrFail($id);
        return response()->json($aldeia);
    }

    /**
     *
     * @OA\Put(
     *     path="/api/aldeia/{id}",
     *     tags={"Aldeias"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um aldeia específico",
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
     *             @OA\Property(property="nome", type="string", example="Grilagem")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Aldeia atualizado",
     *         @OA\JsonContent(ref="#/components/schemas/Aldeia")
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

        $aldeia = Aldeia::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255'
        ]);

        $aldeia->update($validatedData);
        return response()->json($aldeia);
    }
    
    /**
     *
     * @OA\Get(
     *     path="/api/aldeia/pesquisar/buscar-texto",
     *     summary="Pesquisa aldeia por texto",
     *     description="Retorna uma lista de aldeia cujos nomes correspondem ao termo de pesquisa",
     *     tags={"Aldeias"},
     *     security={ {"sanctum": {} } },
     *     @OA\Parameter(
     *         name="texto",
     *         description="Texto para pesquisa de aldeia",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de Aldeia encontrados",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Aldeia")
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
            $aldeias = Aldeia::where('nome', 'LIKE', '%' . $validated['texto'] . '%')->get();
            
            // 4. Verificação de Vazio
            if ($aldeias->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhum resultado encontrado'
                ], Response::HTTP_NOT_FOUND);
            }
            
            // 5. Retorno de Sucesso (Padronizado como JSON)
            return response()->json($aldeias, Response::HTTP_OK);
            
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
     *     path="/api/aldeia/{id}",
     *     tags={"Aldeias"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um aldeia específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Aldeia excluído"
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

        $aldeia = Aldeia::findOrFail($id);
        $aldeia->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
