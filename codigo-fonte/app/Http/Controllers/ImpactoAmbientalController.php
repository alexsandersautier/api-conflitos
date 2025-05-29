<?php

namespace App\Http\Controllers;

use App\Models\ImpactoAmbiental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

/**
 *  @OA\Schema(
 *     schema="ImpactoAmbiental",
 *     type="object",
 *     @OA\Property(property="idImpactoAmbiental", type="integer", example="1"),
 *     @OA\Property(property="nome", type="string", example="Nome do ImpactoAmbiental")
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
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $impactoambiental = ImpactoAmbiental::all();
        return response()->json($impactoambiental);
    }

    /**
     * @OA\Post(
     *     path="/api/impacto-ambiental",
     *     tags={"ImpactosAmbientais"},
     *     security={ {"sanctum": {} } },     *     
     *     summary="Criar um novo impacto ambiental",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Impacto")
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
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $impactoambiental = ImpactoAmbiental::create($validatedData);
        return response()->json($impactoambiental, Response::HTTP_CREATED);
    }

    /**
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
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $impactoambiental = ImpactoAmbiental::findOrFail($id);
        return response()->json($impactoambiental);
    }

    /**
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
     *             @OA\Property(property="nome", type="string", example="Departamento de TI")
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
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $impactoambiental = ImpactoAmbiental::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $impactoambiental->update($validatedData);
        return response()->json($impactoambiental);
    }

    /**
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
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $impactoambiental = ImpactoAmbiental::findOrFail($id);
        $impactoambiental->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
