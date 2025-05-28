<?php

namespace App\Http\Controllers;

use App\Models\ImpactoSocioEconomico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

/**
 *  @OA\Schema(
 *     schema="ImpactoSocioEconomico",
 *     type="object",
 *     @OA\Property(property="idImpactoSocioEconomico", type="integer", example="1"),
 *     @OA\Property(property="nome", type="string", example="Nome do ImpactoSocioEconomico")
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
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $impactosocioeconomico = ImpactoSocioEconomico::all();
        return response()->json($impactosocioeconomico);
    }

    /**
     * @OA\Post(
     *     path="/api/impacto-socio-economico",
     *     tags={"ImpactoSocioEconomico"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo Impacto Sócio Econômicos",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Departamento de TI")
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
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $impactosocioeconomico = ImpactoSocioEconomico::create($validatedData);
        return response()->json($impactosocioeconomico, Response::HTTP_CREATED);
    }

    /**
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
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $impactosocioeconomico = ImpactoSocioEconomico::findOrFail($id);
        return response()->json($impactosocioeconomico);
    }

    /**
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
     *             @OA\Property(property="nome", type="string", example="Nome do Impacto Sócio Econômicos")
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
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $impactosocioeconomico = ImpactoSocioEconomico::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $impactosocioeconomico->update($validatedData);
        return response()->json($impactosocioeconomico);
    }

    /**
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
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $impactosocioeconomico = ImpactoSocioEconomico::findOrFail($id);
        $impactosocioeconomico->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
