<?php

namespace App\Http\Controllers;

use App\Models\Aldeia;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Services\AldeiaService;

/**
 * @OA\Schema(
 *     schema="Aldeia",
 *     type="object",
 *     @OA\Property(property="idAldeia", type="integer", example=1),
 *     @OA\Property(property="nome", type="string", example="nome do aldeia"),
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
class AldeiaController extends Controller{
    
    protected $aldeiaService;
    
    public function __construct(AldeiaService $aldeiaService)
    {
        $this->aldeiaService = $aldeiaService;
        
        if (ini_get('memory_limit') < 256) {
            @ini_set('memory_limit', '256M');
        }
    }
    
    /**
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
    public function index(Request $request){
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        //$aldeias = Aldeia::all();
        $aldeias = $this->aldeiaService->getAllAldeias();
        return response()->json($aldeias);
    }
    
    /**
     * @OA\Post(
     *     path="/api/aldeia",
     *     tags={"Aldeias"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo aldeia",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Departamento de TI")
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
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);
        
        $aldeia = Aldeia::create($validatedData);
        return response()->json($aldeia, Response::HTTP_CREATED);
    }
    
    /**
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
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $aldeia = Aldeia::findOrFail($id);
        return response()->json($aldeia);
    }
    
    /**
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
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $aldeia = Aldeia::findOrFail($id);
        
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);
        
        $aldeia->update($validatedData);
        return response()->json($aldeia);
    }
    
    /**
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
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $aldeia = Aldeia::findOrFail($id);
        $aldeia->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
