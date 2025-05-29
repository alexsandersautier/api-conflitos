<?php

namespace App\Http\Controllers;

use App\Models\TerraIndigena;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * 
 * @OA\Schema(
 *     schema="TerraIndigena",
 *     type="object",
 *     @OA\Property(property="idTerraIndigena", type="integer"),
 *     @OA\Property(property="idPovo", type="integer"),
 *     @OA\Property(property="idSituacaoFundiaria", type="integer"),
 *     @OA\Property(property="codigo_ti", type="string"),
 *     @OA\Property(property="nome", type="string"),
 *     @OA\Property(property="superficie_perimetro_ha", type="string"),
 *     @OA\Property(property="modalidade_ti", type="string"),
 *     @OA\Property(property="etnia_nome", type="string"),
 *     @OA\Property(property="municipio_nome", type="string"),
 *     @OA\Property(property="uf_sigla", type="string"),
 *     @OA\Property(property="coordenacao_regional", type="string"),
 *     @OA\Property(property="faixa_fronteira", type="string"),
 *     @OA\Property(property="undadm_codigo", type="string"),
 *     @OA\Property(property="undadm_nome", type="string"),
 *     @OA\Property(property="undadm_sigla", type="string"),
 *     @OA\Property(property="data_atualizacao", type="string"),
 *     @OA\Property(property="data_homologacao", type="string"),
 *     @OA\Property(property="decreto_homologacao", type="string"),
 *     @OA\Property(property="data_regularizacao", type="string"),
 *     @OA\Property(property="matricula_regularizacao", type="string"),
 *     @OA\Property(property="acao_recuperacao_territorial", type="string"),
 *     @OA\Property(property="dominio_uniao", type="string"),
 *     @OA\Property(property="numero_processo_funai", type="string"),
 *     @OA\Property(property="data_abertura_processo_funai", type="string"),
 *     @OA\Property(property="numero_portaria_funai", type="string"),
 *     @OA\Property(property="numero_processo_sei", type="string"),
 *     @OA\Property(property="numero_portaria_declaratoria", type="string"),
 *     @OA\Property(property="qtd_aldeias", type="string"),
 *     @OA\Property(property="qtd_familias", type="string"),
 *     @OA\Property(property="links_documentos_vinculados", type="string")
 * )
 * 
 * @OA\PathItem(
 *     path="/api/terra-indigena"
 * )
 *
 * @OA\Tag(
 *     name="TerraIndigenas",
 *     description="Endpoints para Terras Indigenas"
 * )
 */
class TerraIndigenaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/terra-indigena",
     *     tags={"TerraIndigenas"},
     *     security={ {"sanctum": {} } },
     *     @OA\Parameter(
     *         name="page",
     *         description="Página de terras indígenas",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     summary="Listar todos os terra indigenas",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de Terras indigenas",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/TerraIndigena")
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
        
        $terraindigenas = TerraIndigena::with(['situacao_fundiaria', 'povo'])->paginate(10);
        return response()->json($terraindigenas, Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/terra-indigena",
     *     tags={"TerraIndigenas"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar uma nova terra indigena",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Terra Indígena")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Terra Indigena criada"
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

        $terraindigena = TerraIndigena::create($validatedData);
        return response()->json($terraindigena, Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/terra-indigena/{id}",
     *     tags={"TerraIndigenas"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um terra indigena específica",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do terra indigena"
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
        
        $terraindigena = TerraIndigena::with(['situacao_fundiaria', 'povo'])->findOrFail($id);
        return response()->json($terraindigena);
    }

    /**
     * @OA\Put(
     *     path="/api/terra-indigena/{id}",
     *     tags={"TerraIndigenas"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um terra indigena específica",
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
     *             @OA\Property(property="nome", type="string", example="Terra Indigena")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Terra Indigena atualizada",
     *         @OA\JsonContent(ref="#/components/schemas/TerraIndigena")
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
        
        $terraindigena = TerraIndigena::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $terraindigena->update($validatedData);
        return response()->json($terraindigena);
    }

    /**
     * @OA\Delete(
     *     path="/api/terra-indigena/{id}",
     *     tags={"TerraIndigenas"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um terra indigena específica",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Terra Indigena excluída"
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
        
        $terraindigena = TerraIndigena::findOrFail($id);
        $terraindigena->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
