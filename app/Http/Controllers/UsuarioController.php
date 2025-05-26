<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\PathItem(
 *     path="/api/usuario"
 * )
 *
 * @OA\Tag(
 *     name="Usuarios",
 *     description="Endpoints para Usuários"
 * )
 * @OA\Schema(
 *     schema="Usuario",
 *     type="object",
 *     @OA\Property(property="idUsuario", type="integer", example="2"),
 *     @OA\Property(property="orgao", type="object", ref="#/components/schemas/Orgao"),
 *     @OA\Property(property="perfil", type="object", ref="#/components/schemas/Perfil"),
 *     @OA\Property(property="nome", type="string", example="Luiz Leão"),
 *     @OA\Property(property="email", type="string", format="email", example="luizleao@gmail.com"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class UsuarioController extends Controller {
    /**
     * @OA\Get(
     *     path="/api/usuario",
     *     tags={"Usuarios"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os usuários",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuários",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Usuario")
     *         )
     *     )
     * )
     */
    public function index() {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',  
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $usuarios = Usuario::with(['orgao', 'perfil'])->get();
        return response()->json($usuarios, Response::HTTP_OK);
    }
    
    /**
     * @OA\Get(
     *     path="/api/usuario/{id}",
     *     tags={"Usuarios"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um usuario específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do usuario",
     *         @OA\JsonContent(ref="#/components/schemas/Usuario")
     *     )
     * )
     */
    public function show($id) {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $usuario = Usuario::findOrFail($id);
        return response()->json($usuario, Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/usuarios",
     *     tags={"Usuarios"},
     *     security={ {"sanctum": {} } },
     *     summary="Cria um novo usuário",
     *     operationId="createUsuario",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome", "email", "senha", "idOrgao"},
     *             @OA\Property(property="nome", type="string", example="João Silva"),
     *             @OA\Property(property="email", type="string", format="email", example="joao@example.com"),
     *             @OA\Property(property="senha", type="string", format="password", example="senha123"),
     *             @OA\Property(property="idOrgao", type="integer", example=1),
     *             @OA\Property(property="idPerfil", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuário criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Usuario")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validação falhou"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:usuario',
            'senha'    => 'required|string|min:4',
            'idOrgao'  => 'required|integer|exists:orgao,idOrgao',
            'idPerfil' => 'required|integer|exists:perfil,idPerfil'
        ]);
        
        // Criptografa a senha antes de armazenar
        $validatedData['senha'] = Hash::make($validatedData['senha']);
        
        $usuario = Usuario::create($validatedData);
        
        return response()->json($usuario, 201);
    }
    
    /**
     * @OA\Put(
     *     path="/api/usuario/{id}",
     *     tags={"Usuarios"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um usuário específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome", "email", "senha", "idOrgao", "idPerfil"},
     *             @OA\Property(property="nome", type="string", example="João Silva"),
     *             @OA\Property(property="email", type="string", format="email", example="joao@example.com"),
     *             @OA\Property(property="senha", type="string", format="password", example="senha123"),
     *             @OA\Property(property="idOrgao", type="integer", example=1),
     *             @OA\Property(property="idPerfil", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário atualizado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nome'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:usuario',
            'senha'    => 'required|string|min:4',
            'idOrgao'  => 'required|integer|exists:orgao,idOrgao',
            'idPerfil' => 'required|integer|exists:perfil,idPerfil'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $validatedData = $validator->validated();
        
        $usuario->update($validatedData);
        return response()->json($usuario);
    }
    
    /**
     * @OA\Get(
     *     path="/api/usuario/pesquisar/buscar-texto",
     *     summary="Pesquisa usuários por texto",
     *     description="Retorna uma lista de usuários cujos nomes correspondem ao termo de pesquisa",
     *     tags={"Usuarios"},
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
     *         description="Lista de usuários encontrados",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Usuario")
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
            if (!Auth::guard('sanctum')->check()) {
                return response()->json([
                    'message' => 'Não autorizado',
                    'status' => Response::HTTP_UNAUTHORIZED
                ], Response::HTTP_UNAUTHORIZED);
            }
            
            $request->validate([
                'texto' => 'required|string|min:2'
            ],[
                'texto.required' => 'O termo é obrigatório.',
                'texto.string'   => 'O termo deve ser uma string.',
                'texto.min'      => 'O termo deve ter no mínimo :min caracteres.'
            ]);
            
            $usuarios = Usuario::with(['orgao', 'perfil'])->where('nome', 'LIKE', '%'.$request->texto.'%')
                                                          ->orWhere('email', 'LIKE', '%'.$request->texto.'%')
                                                          ->get(); //->paginate(10)
            
            if($usuarios->isEmpty()){
                return response()->json([
                    'message' => 'Nenhum resultado encontrado'
                ], Response::HTTP_NOT_FOUND);
            }
            
            return $usuarios;
            
        } catch (ValidationException $e) {
            // Captura exceções de validação e retorna status 422 (Unprocessable Entity)
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro na pesquisa',
                'details' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/usuario/alterar-senha",
     *     summary="Altera a senha do usuário autenticado",
     *     tags={"Usuarios"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"senha_atual", "nova_senha", "confirmacao_senha"},
     *             @OA\Property(property="senha_atual", type="string", format="password", example="SenhaAntiga123"),
     *             @OA\Property(property="nova_senha", type="string", format="password", example="NovaSenhaSegura123"),
     *             @OA\Property(property="confirmacao_senha", type="string", format="password", example="NovaSenhaSegura123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Senha alterada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Senha alterada com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validação falhou",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="As senhas não coincidem."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Senha atual incorreta.")
     *         )
     *     )
     * )
     */
    public function alterarSenha(Request $request) {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $request->validate(['senha_atual'       => 'required|string',
                            'nova_senha'        => 'required|string|min:5|different:senha_atual',
                            'confirmacao_senha' => 'required|string|same:nova_senha'
                        ], [
                            'nova_senha.min'         => 'A nova senha deve ter no mínimo 5 caracteres',
                            'nova_senha.different'   => 'A nova senha deve ser diferente da senha atual.',
                            'confirmacao_senha.same' => 'A confirmação de senha não coincide com a nova senha.'
                        ]);

        
        if (!Hash::check($request->senha_atual, $user->senha)) {
            return response()->json([
                'message' => 'Senha atual incorreta.',
                'status'  => Response::HTTP_UNPROCESSABLE_ENTITY
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        $user->update([
            'senha' => Hash::make($request->nova_senha)
        ]);
        
        return response()->json(['message' => 'Senha alterada com sucesso.']);
    }
}
