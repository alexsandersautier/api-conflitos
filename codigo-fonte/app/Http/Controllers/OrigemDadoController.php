<?php
namespace App\Http\Controllers;

use App\Models\OrigemDado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

/**
 *
 * @OA\Schema(
 *     schema="OrigemDado",
 *     type="object",
 *     @OA\Property(property="idOrigemDado", type="integer", example="1"),
 *     @OA\Property(property="conflito", type="object",  description="Conflito vinculado", ref="#/components/schemas/Conflito"),
 *     @OA\Property(property="tipoResponsavel", type="object",  description="Tipo de Responsável vinculado", ref="#/components/schemas/TipoResponsavel"),
 *     @OA\Property(property="setor_cadastrante", type="string", example="FUNAI"),
 *     @OA\Property(property="observacao", type="string", example="Lista de observações")
 * )
 *
 * @OA\PathItem(
 *     path="/api/origem-dado"
 * )
 *
 * @OA\Tag(
 *     name="OrigemDado",
 *     description="Endpoints para Origens de Dados"
 * )
 */
class OrigemDadoController extends Controller
{

    /**
     *
     * @OA\Get(
     *     path="/api/origem-dado",
     *     tags={"OrigemDado"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos as origens de dados",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de Origens de Dados",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/OrigemDado")
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

        $origens_dados = OrigemDado::with([
            'conflito',
            'tipo_responsavel'
        ])->get();
        return response()->json($origens_dados);
    }

    /**
     *
     * @OA\Post(
     *     path="/api/origem-dado",
     *     tags={"OrigemDado"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar uma novo origem de dados",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"idConflito", "idTiporesponsavel", "setor_cadastrante"},
     *             @OA\Property(property="idConflito", type="integer", example="1"),
     *             @OA\Property(property="idTiporesponsavel", type="integer", example="1"),
     *             @OA\Property(property="setor_cadastrante", type="string", example="FUNAI"),
     *             @OA\Property(property="observacao", type="string", example="Observações")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Origem de Dados criado"
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
            'idConflito' => 'required|integer|exists:conflito,idConflito',
            'idTipoResponsavel' => 'required|integer|exists:tipo_responsavel,idTipoResponsavel',
            'setor_cadastrante' => 'string|max:50',
            'observacao' => 'string|max:100'
        ]);

        $origemDado = OrigemDado::create($validatedData);
        return response()->json($origemDado, 201);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/origem-dado/{id}",
     *     tags={"OrigemDado"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um origem de dados específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do origem de dados",
     *         @OA\JsonContent(ref="#/components/schemas/OrigemDado")
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

        $origemDado = OrigemDado::with([
            'conflito',
            'tipo_responsavel'
        ])->findOrFail($id);
        return response()->json($origemDado);
    }

    /**
     *
     * @OA\Put(
     *     path="/api/origem-dado/{id}",
     *     tags={"OrigemDado"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um origem de dados específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"idConflito", "idTiporesponsavel", "setor_cadastrante"},
     *             @OA\Property(property="idConflito", type="integer", example="1"),
     *             @OA\Property(property="idTiporesponsavel", type="integer", example="1"),
     *             @OA\Property(property="setor_cadastrante", type="string", example="FUNAI"),
     *             @OA\Property(property="observacao", type="string", example="Observações")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Origem de Dados atualizado"
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

        $origemDado = OrigemDado::findOrFail($id);

        $validatedData = $request->validate([
            'idConflito' => 'required|integer|exists:conflito,idConflito',
            'idTipoResponsavel' => 'required|integer|exists:tipo_responsavel,idTipoResponsavel',
            'setor_cadastrante' => 'string|max:50',
            'observacao' => 'string|max:100'
        ]);

        $origemDado->update($validatedData);
        return response()->json($origemDado);
    }

    /**
     *
     * @OA\Delete(
     *     path="/api/origem-dado/{id}",
     *     tags={"OrigemDado"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um origem de dados específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Origem de Dados excluída"
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

        $origemDado = OrigemDado::findOrFail($id);
        $origemDado->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/origem-dado/conflito/{idConflito}",
     *     tags={"OrigemDado"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos as origens de dados de um Conflito",
     *     @OA\Parameter(
     *         name="idConflito",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de Origens de Dados"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro no consulta"
     *     )
     * )
     */
    public function getAllByConflito($idConflito)
    {
        try {
            if (! Auth::guard('sanctum')->check()) {
                return response()->json([
                    'message' => 'Não autorizado',
                    'status' => Response::HTTP_UNAUTHORIZED
                ], Response::HTTP_UNAUTHORIZED);
            }

            return OrigemDado::with([
                'conflito',
                'tipo_responsavel'
            ])->where('idConflito', $idConflito)->get();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro na consulta',
                'details' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
