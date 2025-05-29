<?php

namespace App\Http\Controllers;

use App\Models\Assunto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

/**
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
    public function index(Request $request){
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $assunto = Assunto::all();
        return response()->json($assunto);
    }

    /**
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
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $assunto = Assunto::create($validatedData);
        return response()->json($assunto, Response::HTTP_CREATED);
    }

    /**
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
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $assunto = Assunto::findOrFail($id);
        return response()->json($assunto);
    }

    /**
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
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $assunto = Assunto::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $assunto->update($validatedData);
        return response()->json($assunto);
    }

    /**
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
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $assunto = Assunto::findOrFail($id);
        $assunto->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
