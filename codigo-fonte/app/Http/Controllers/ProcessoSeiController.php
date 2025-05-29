<?php

namespace App\Http\Controllers;

use App\Models\ProcessoSei;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 *  @OA\Schema(
 *     schema="ProcessoSei",
 *     type="object",
 *     @OA\Property(property="idProcessoSei", type="integer", example="1"),
 *     @OA\Property(property="tipoProcessoSei", type="object", ref="#/components/schemas/TipoProcessoSei"),
 *     @OA\Property(property="numero", type="string", example="59004.000960/2024-68"),
 *     @OA\Property(property="assunto", type="string", example="Conflito"),
 *     @OA\Property(property="especificacao", type="string", example="Especificação do processo"),
 *     @OA\Property(property="interessado", type="string", example="Luiz Silva")
 * )
 * 
 * @OA\PathItem(
 *     path="/api/processo-sei"
 * )
 *
 * @OA\Tag(
 *     name="ProcessosSei",
 *     description="Endpoints para Processos SEI"
 * )
 */
class ProcessoSeiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/processo-sei",
     *     tags={"ProcessosSei"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os processos SEI",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de processos SEI",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ProcessoSei")
     *         )
     *     )
     * )
     */
    public function index()
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $processos_sei = ProcessoSei::all();
        return response()->json($processos_sei);
    }

    /**
     * @OA\Post(
     *     path="/api/processo-sei",
     *     tags={"ProcessosSei"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo processosei",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"idTipoProcessoSei", "idConflito", "numero", "assunto", "especificacao", "interessado"}, 
     *             @OA\Property(property="idTipoProcessoSei", type="integer", example="1"),
     *             @OA\Property(property="idConflito", type="integer", example="1"),
     *             @OA\Property(property="numero", type="string", example="123.456.789.123"),
     *             @OA\Property(property="assunto", type="string", example="Descrição do assunto"),
     *             @OA\Property(property="especificacao", type="string", example="Detalhar especificação"),
     *             @OA\Property(property="interessado", type="string", example="João da Silva, Maria José,...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Processo SEI criado",
     *         @OA\JsonContent(ref="#/components/schemas/ProcessoSei")
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
            'idTipoProcessoSei' => 'required|integer|exists:tipo_processo_sei,idTipoProcessoSei',
            'idConflito'        => 'required|integer|exists:conflito,idConflito',
            'numero'            => 'required|string|max:50',
            'assunto'           => 'string|max:50',
            'especificacao'     => 'string|max:50',
            'interessado'       => 'string|max:50'
        ]);

        $processosei = ProcessoSei::create($validatedData);
        return response()->json($processosei, Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/processo-sei/{id}",
     *     tags={"ProcessosSei"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um processo SEI específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do processo sei"
     *     )
     * )
     */
    public function show($id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $processosei = ProcessoSei::findOrFail($id);
        return response()->json($processosei);
    }

    /**
     * @OA\Put(
     *     path="/api/processo-sei/{id}",
     *     tags={"ProcessosSei"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um processo SEI específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"idTipoProcessoSei", "idConflito", "numero", "assunto", "especificacao", "interessado"}, 
     *             @OA\Property(property="idTipoProcessoSei", type="integer", example="1"),
     *             @OA\Property(property="idConflito", type="integer", example="1"),
     *             @OA\Property(property="numero", type="string", example="123.456.789.123"),
     *             @OA\Property(property="assunto", type="string", example="Descrição do assunto"),
     *             @OA\Property(property="especificacao", type="string", example="Detalhar especificação"),
     *             @OA\Property(property="interessado", type="string", example="João da Silva, Maria José,...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Processo SEI atualizado"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $processosei = ProcessoSei::findOrFail($id);

        $validatedData = $request->validate([
            'idTipoProcessoSei' => 'required|integer|exists:tipo_processo_sei,idTipoProcessoSei',
            'idConflito'        => 'required|integer|exists:conflito,idConflito',
            'numero'            => 'required|string|max:50',
            'assunto'           => 'string|max:50',
            'especificacao'     => 'string|max:50',
            'interessado'       => 'string|max:50'
        ]);

        $processosei->update($validatedData);
        return response()->json($processosei);
    }

    /**
     * @OA\Delete(
     *     path="/api/processo-sei/{id}",
     *     tags={"ProcessosSei"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um processo Sei específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Processo SEI excluído"
     *     )
     * )
     */
    public function destroy($id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $processosei = ProcessoSei::findOrFail($id);
        $processosei->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
    
    /**
     * @OA\Get(
     *     path="/api/processo-sei/conflito/{idConflito}",
     *     tags={"ProcessosSei"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os processos SEI de um Conflito",
     *     @OA\Parameter(
     *         name="idConflito",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de processos SEI"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Termo de pesquisa não fornecido"
     *     )
     * )
     */
    public function getAllByConflito($idConflito){
        try {
            if (!Auth::guard('sanctum')->check()) {
                return response()->json([
                    'message' => 'Não autorizado',
                    'status' => Response::HTTP_UNAUTHORIZED
                ], Response::HTTP_UNAUTHORIZED);
            }
            
            return ProcessoSei::with(['tipo_processo_sei', 'conflito'])->where('idConflito', $idConflito)->get();
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro na consulta',
                'details' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
