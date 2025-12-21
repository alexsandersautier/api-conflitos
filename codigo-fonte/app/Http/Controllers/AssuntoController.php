<?php
namespace App\Http\Controllers;

use App\Models\Assunto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

/**
 *
 * @OA\Schema(
 *     schema="Assunto",
 *     type="object",
 *     @OA\Property(property="idAssunto", type="integer", example=1),
 *     @OA\Property(property="nome", type="string", example="nome do assunto"),
 * )
 * @OA\PathItem(
 *     path="/api/assunto"
 * )
 *
 * @OA\Tag(
 *     name="Assuntos",
 *     description="Endpoints para Assuntos"
 * )
 */
class AssuntoController extends Controller
{

    /**
     *
     * @OA\Get(
     *     path="/api/assunto",
     *     tags={"Assuntos"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os assuntos",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de assuntos",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Assunto")
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

        $assunto = Assunto::all();
        return response()->json($assunto);
    }

    /**
     *
     * @OA\Post(
     *     path="/api/assunto",
     *     tags={"Assuntos"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo assunto",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Departamento de TI")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Assunto criado"
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

        $assunto = Assunto::create($validatedData);
        return response()->json($assunto, Response::HTTP_CREATED);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/assunto/{id}",
     *     tags={"Assuntos"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um assunto específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do assunto",
     *         @OA\JsonContent(ref="#/components/schemas/Assunto")
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

        $assunto = Assunto::findOrFail($id);
        return response()->json($assunto);
    }

    /**
     *
     * @OA\Put(
     *     path="/api/assunto/{id}",
     *     tags={"Assuntos"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um assunto específico",
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
     *         description="Assunto atualizado",
     *         @OA\JsonContent(ref="#/components/schemas/Assunto")
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

        $assunto = Assunto::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255'
        ]);

        $assunto->update($validatedData);
        return response()->json($assunto);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/assunto/pesquisar/buscar-texto",
     *     summary="Pesquisa assunto por texto",
     *     description="Retorna uma lista de assunto cujos nomes correspondem ao termo de pesquisa",
     *     tags={"Assuntos"},
     *     security={ {"sanctum": {} } },
     *     @OA\Parameter(
     *         name="texto",
     *         description="Texto para pesquisa de assunto",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de Assunto encontrados",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Assunto")
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
            $assuntos = Assunto::where('nome', 'LIKE', '%' . $validated['texto'] . '%')->get();
            
            // 4. Verificação de Vazio
            if ($assuntos->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhum resultado encontrado'
                ], Response::HTTP_NOT_FOUND);
            }
            
            // 5. Retorno de Sucesso (Padronizado como JSON)
            return response()->json($assuntos, Response::HTTP_OK);
            
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
     *     path="/api/assunto/{id}",
     *     tags={"Assuntos"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um assunto específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Assunto excluído"
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

        $assunto = Assunto::findOrFail($id);
        $assunto->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
