<?php
namespace App\Http\Controllers;

use App\Models\Perfil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

/**
 *
 * @OA\Schema(
 *     schema="Perfil",
 *     type="object",
 *     @OA\Property(property="idPerfil", type="integer", example=1),
 *     @OA\Property(property="nome", type="string", example="Administrador"),
 * )
 *
 * @OA\PathItem(
 *     path="/api/perfil"
 * )
 *
 * @OA\Tag(
 *     name="Perfis",
 *     description="Endpoints para Perfil"
 * )
 */
class PerfilController extends Controller
{

    /**
     *
     * @OA\Get(
     *     path="/api/perfil",
     *     tags={"Perfis"},
     *     security={ {"sanctum": {} } },     *
     *     summary="Listar todos os perfils",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de perfils",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Perfil")
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

        $perfis = Perfil::all();
        return response()->json($perfis);
    }

    /**
     *
     * @OA\Post(
     *     path="/api/perfil",
     *     tags={"Perfis"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo perfil",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Administrador")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Perfil criado"
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

        $perfil = Perfil::create($validatedData);
        return response()->json($perfil, Response::HTTP_CREATED);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/perfil/{id}",
     *     tags={"Perfis"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um perfil específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do perfil",
     *         @OA\JsonContent(ref="#/components/schemas/Perfil")
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

        $perfil = Perfil::findOrFail($id);
        return response()->json($perfil);
    }

    /**
     *
     * @OA\Put(
     *     path="/api/perfil/{id}",
     *     tags={"Perfis"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um perfil específico",
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
     *             @OA\Property(property="nome", type="string", example="Administrador")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Perfil atualizado"
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

        $perfil = Perfil::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255'
        ]);

        $perfil->update($validatedData);
        return response()->json($perfil);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/perfil/pesquisar/buscar-texto",
     *     summary="Pesquisa perfil por texto",
     *     description="Retorna uma lista de perfil cujos nomes correspondem ao termo de pesquisa",
     *     tags={"Perfis"},
     *     security={ {"sanctum": {} } },
     *     @OA\Parameter(
     *         name="texto",
     *         description="Texto para pesquisa de perfil",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de Perfil encontrados",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Perfil")
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
            $perfis = Perfil::where('nome', 'LIKE', '%' . $validated['texto'] . '%')->get();
            
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
     *     path="/api/perfil/{id}",
     *     tags={"Perfis"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um perfil específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Perfil excluído"
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

        $perfil = Perfil::findOrFail($id);
        $perfil->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
