<?php

namespace App\Http\Controllers;

use App\Models\Assunto;
use App\Models\Conflito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\TipoConflito;
use App\Models\ProcessoSei;
use App\Models\ImpactoAmbiental;
use App\Models\ImpactoSaude;
use App\Models\ImpactoSocioEconomico;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use App\Models\TerraIndigena;
use App\Models\Povo;

/**
 *  @OA\Schema(
 *     schema="Conflito",
 *     type="object",
 *     @OA\Property(property="nome", type="string", example="nome do conflito"),
 *     @OA\Property(property="descrição", type="string", example="descrição do conflito"),
 *     @OA\Property(property="regiao", type="string", example="norte"),
 *     @OA\Property(property="dataConflito", type="date", format="yyyy-mm-dd", example="2025-04-13"),
 *     @OA\Property(property="latitude", type="string", example="41.40338"),
 *     @OA\Property(property="longitude", type="string", example="2.17403"),
 *     @OA\Property(property="municipio", type="string", example="Marabá"),
 *     @OA\Property(property="uf", type="string", example="PA"),
 *     @OA\Property(property="flagOcorrenciaAmeaca", type="boolean", example="1"),
 *     @OA\Property(property="flagOcorrenciaViolencia", type="boolean", example="0"),
 *     @OA\Property(property="flagOcorrenciaAssassinato", type="boolean", example="1"),
 *     @OA\Property(property="flagOcorrenciaFeridos", type="boolean", example="0"),
 *     @OA\Property(property="flagMembroProgramaProtecao", type="boolean", example="1"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(
 *         property="terrasIndigenas",
 *         type="array",
 *         description="Terras Indigenas vinculadas",
 *         @OA\Items(ref="#/components/schemas/TerraIndigena")
 *     ),
 *     @OA\Property(
 *         property="povos",
 *         type="array",
 *         description="Povos vinculadas",
 *         @OA\Items(ref="#/components/schemas/Povo")
 *     ),
 *     @OA\Property(
 *         property="assuntos",
 *         type="array",
 *         description="Assuntos vinculados",
 *         @OA\Items(ref="#/components/schemas/Assunto")
 *     ),
 *     @OA\Property(
 *         property="tiposConflito",
 *         type="array",
 *         description="Tipos de Conflito vinculados",
 *         @OA\Items(ref="#/components/schemas/TipoConflito")
 *     ),
 *     @OA\Property(
 *         property="impactosAmbientais",
 *         type="array",
 *         description="Impactos Ambientais vinculados",
 *         @OA\Items(ref="#/components/schemas/ImpactoAmbiental")
 *     ),
 *     @OA\Property(
 *         property="impactosSaude",
 *         type="array",
 *         description="Impactos Saúde vinculados",
 *         @OA\Items(ref="#/components/schemas/ImpactoSaude")
 *     ),
 *     @OA\Property(
 *         property="impactosSocioEconomicos",
 *         type="array",
 *         description="Impactos Sócio Econômicos vinculados",
 *         @OA\Items(ref="#/components/schemas/ImpactoSocioEconomico")
 *     )
 * )
 * 
 * 
 * @OA\PathItem(
 *     path="/api/conflitos"
 * )
 *
 * @OA\Tag(
 *     name="Conflitos",
 *     description="Endpoints para Conflitos"
 * )
 */
class ConflitoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/conflito",
     *     tags={"Conflitos"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os conflitos",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de conflitos",
     *         @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Conflito")
     *          )
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
        
        $conflitos = Conflito::all();
        return response()->json($conflitos);
    }
    
    /**
     * @OA\Get(
     *     path="/api/conflito/page",
     *     tags={"Conflitos"},
     *     security={ {"sanctum": {} } },
     *     @OA\Parameter(
     *         name="page",
     *         description="Página de registros",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         description="Registros por Página",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     summary="Listar os conflitos por página",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de conflitos",
     *         @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Conflito")
     *          )
     *     )
     * )
     */
    public function getAllPage(Request $request)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $per_page = $request->per_page ?? 10;
        $conflitos = Conflito::orderBy('created_at')->paginate($per_page);
        return response()->json($conflitos);
    }

    /**
     * @OA\Post(
     *     path="/api/conflito",
     *     tags={"Conflitos"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo conflito",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome","descricao","regiao","dataConflito","latitude","longitude","municipio","uf","flagOcorrenciaAmeaca","flagOcorrenciaViolencia","flagOcorrenciaAssassinato","flagOcorrenciaFeridos", "flagMembroProgramaProtecao"},
     *             @OA\Property(property="nome", type="string", example="nome do conflito"),
     *             @OA\Property(property="descrição", type="string", example="descrição do conflito"),
     *             @OA\Property(property="regiao", type="string", example="norte"),
     *             @OA\Property(property="dataConflito", type="date", format="yyyy-mm-dd", example="2025-04-13"),
     *             @OA\Property(property="latitude", type="string", example="41.40338"),
     *             @OA\Property(property="longitude", type="string", example="2.17403"),
     *             @OA\Property(property="municipio", type="string", example="Marabá"),
     *             @OA\Property(property="uf", type="string", example="PA"),
     *             @OA\Property(property="flagOcorrenciaAmeaca", type="boolean", example="1"),
     *             @OA\Property(property="flagOcorrenciaViolencia", type="boolean", example="0"),
     *             @OA\Property(property="flagOcorrenciaAssassinato", type="boolean", example="1"),
     *             @OA\Property(property="flagOcorrenciaFeridos", type="boolean", example="0"),
     *             @OA\Property(property="flagMembroProgramaProtecao", type="boolean", example="1"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Conflito criado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro na validação"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
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
        
        $validator = Validator::make($request->all(), [
            'nome'                       => 'required|string|max:255',
            'descricao'                  => 'required|string',
            'regiao'                     => 'required|string|max:100',
            'dataInicioConflito'         => 'required|date',
            'dataFimConflito'            => 'nullable|date|after_or_equal:dataInicioConflito',
            'latitude'                   => 'required|numeric|between:-90,90',
            'longitude'                  => 'required|numeric|between:-180,180',
            'municipio'                  => 'required|string|max:100',
            'uf'                         => 'required|string|size:2',
            'flagOcorrenciaAmeaca'       => 'sometimes|boolean',
            'flagOcorrenciaViolencia'    => 'sometimes|boolean',
            'flagOcorrenciaAssassinato'  => 'sometimes|boolean',
            'flagOcorrenciaFeridos'      => 'sometimes|boolean',
            'flagMembroProgramaProtecao' => 'sometimes|boolean'
        ],[
            'nome.required'              => 'O título é obrigatório',
            'descricao.required'         => 'A descrição é obrigatória',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        $validatedData = $validator->validated();
        
        // Define valores padrão para flags booleanas caso não sejam fornecidas
        $booleanFields = [
            'flagOcorrenciaAmeaca',
            'flagOcorrenciaViolencia',
            'flagOcorrenciaAssassinato',
            'flagOcorrenciaFeridos',
            'flagMembroProgramaProtecao'
        ];
        
        foreach ($booleanFields as $field) {
            if (!isset($validatedData[$field])) {
                $validatedData[$field] = false;
            }
        }
        
        $conflito = Conflito::create($validatedData);
        
        return response()->json($conflito, Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/conflito/{id}",
     *     tags={"Conflitos"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um conflito específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do conflito",
     *         @OA\JsonContent(ref="#/components/schemas/Conflito")
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
        
        $conflito = Conflito::find($id);
        
        if (!$conflito) {
            return response()->json([
                'message' => 'Conflito não encontrado'
            ], Response::HTTP_NOT_FOUND);
        }
        
        return response()->json($conflito);
    }

    /**
     * @OA\Put(
     *     path="/api/conflito/{id}",
     *     tags={"Conflitos"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um conflito específico",
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
     *         description="Conflito atualizado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro na validação"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
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
        
        $conflito = Conflito::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nome'                       => 'required|string|max:255',
            'descricao'                  => 'required|string',
            'regiao'                     => 'required|string|max:100',
            'dataConflito'               => 'required|date',
            'latitude'                   => 'required|numeric|between:-90,90',
            'longitude'                  => 'required|numeric|between:-180,180',
            'municipio'                  => 'required|string|max:100',
            'uf'                         => 'required|string|size:2',
            'flagOcorrenciaAmeaca'       => 'sometimes|boolean',
            'flagOcorrenciaViolencia'    => 'sometimes|boolean',
            'flagOcorrenciaAssassinato'  => 'sometimes|boolean',
            'flagOcorrenciaFeridos'      => 'sometimes|boolean',
            'flagMembroProgramaProtecao' => 'sometimes|boolean'
        ],[
            'nome.required'              => 'O título é obrigatório',
            'descricao.required'         => 'A descrição é obrigatória',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        $validatedData = $validator->validated();
        
        // Define valores padrão para flags booleanas caso não sejam fornecidas
        $booleanFields = [
            'flagOcorrenciaAmeaca',
            'flagOcorrenciaViolencia',
            'flagOcorrenciaAssassinato',
            'flagOcorrenciaFeridos',
            'flagMembroProgramaProtecao'
        ];
        
        foreach ($booleanFields as $field) {
            if (!isset($validatedData[$field])) {
                $validatedData[$field] = false;
            }
        }

        $conflito->update($validatedData);
        return response()->json($conflito);
    }

    /**
     * @OA\Delete(
     *     path="/api/conflito/{id}",
     *     tags={"Conflitos"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um conflito específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Conflito excluído"
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
        
        $conflito = Conflito::findOrFail($id);
        $conflito->delete();
        return response()->json(null, Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/conflito/{id}/terras-indigenas",
     *     tags={"Conflitos"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter as Terras Indígenas de um conflito específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Terras Indígenas de um conflito",
     *         @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/TerraIndigena")
     *          )
     *     )
     * )
     */
    public function getTerrasIndigenas($id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $conflito = Conflito::findOrFail($id);
        $terrasIndigenas = $conflito->terras_indigenas()->get();
        
        return response()->json($terrasIndigenas);
    }
    
    /**
     * Adiciona uma Terra Indígena a um conflito
     *
     * @OA\Post(
     *     path="/api/conflito/{id}/terra-indigena",
     *     tags={"Conflitos"},
     *     security={ {"sanctum": {} } },
     *     summary="Associa uma Terra Indígena a um conflito",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do conflito",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"idTerraIndigena"},
     *             @OA\Property(property="idTerraIndigena", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Terra Indigena associada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Conflito")
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Conflito ou Terra Indigena não encontrado"
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Validação falhou"
     *     )
     * )
     */
    public function attachTerraIndigena(Request $request, $id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $request->validate([
            'idTerraIndigena' => 'required|integer|exists:terra_indigena,idTerraIndigena'
        ]);
        
        $conflito = Conflito::findOrFail($id);
        $idTerraIndigena = $request->input('idTerraIndigena');
        
        if(!$conflito->exists()){
            return response()->json([
                'message' => 'Conflito não encontrado'
            ], Response::HTTP_NOT_FOUND);
        }
        
        // Verifica se a relação já existe
        if ($conflito->terras_indigenas()->where('terra_indigena_conflito.idTerraIndigena', $idTerraIndigena)->exists()) {
            return response()->json([
                'message' => 'Este Povo já está associado ao conflito'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        // Cria a relação
        $conflito->terras_indigenas()->attach($idTerraIndigena);
        
        // Retorna o conflito com os assuntos atualizados
        return response()->json([
            'message' => 'Terra indigena adicionada com sucesso',
            'data' => $conflito->load('terras_indigenas')
        ]);
    }
    
    /**
     * Remove uma Terra Indigena de um conflito
     *
     * @OA\Delete(
     *     path="/api/conflito/{idConflito}/terra-indigena/{idTerraIndigena}",
     *     tags={"Conflitos"},
     *     security={ {"sanctum": {} } },
     *     summary="Remove a associação de uma Terra Indigena com um conflito",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="idConflito",
     *         in="path",
     *         description="ID do conflito",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="idTerraIndigena",
     *         in="path",
     *         description="ID da Terra Indigena",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Terra Indigena desassociada com sucesso",
     *     ),
     *     @OA\Response(response=404, description="Conflito ou Povo não encontrado")
     * )
     */
    public function detachTerraIndigena($idConflito, $idTerraIndigena)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $conflito = Conflito::findOrFail($idConflito);
        
        if(!$conflito->exists()){
            return response()->json([
                'message' => 'Conflito não encontrado'
            ], Response::HTTP_NOT_FOUND);
        }
        
        // Verifica se o assunto existe
        TerraIndigena::findOrFail($idTerraIndigena);
        
        // Remove a relação
        $conflito->terras_indigenas()->detach($idTerraIndigena);
        
        return response()->json([
            'message' => 'Terra Indigena removida com sucesso',
            'data' => $conflito->load('terras_indigenas')
        ]);
    }
    
    /**
     * @OA\Get(
     *     path="/api/conflito/{id}/povos",
     *     tags={"Conflitos"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter os Povos de um conflito específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Povos de um conflito",
     *         @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Povo")
     *          )
     *     )
     * )
     */
    public function getPovos($id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $conflito = Conflito::findOrFail($id);
        $povos = $conflito->povos()->get();
        
        return response()->json($povos);
    }
    
    /**
     * Adiciona um Povo a um conflito
     *
     * @OA\Post(
     *     path="/api/conflito/{id}/povo",
     *     tags={"Conflitos"},
     *     security={ {"sanctum": {} } },
     *     summary="Associa um Povo a um conflito",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do conflito",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"idPovo"},
     *             @OA\Property(property="idPovo", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Povo associado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Conflito")
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Conflito ou Povo não encontrado"
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Validação falhou"
     *     )
     * )
     */
    public function attachPovo(Request $request, $id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $request->validate([
            'idPovo' => 'required|integer|exists:povo,idPovo'
        ]);
        
        $conflito = Conflito::findOrFail($id);
        $idPovo = $request->input('idPovo');
        
        if(!$conflito->exists()){
            return response()->json([
                'message' => 'Conflito não encontrado'
            ], Response::HTTP_NOT_FOUND);
        }
        
        // Verifica se a relação já existe
        if ($conflito->povos()->where('povo_conflito.idPovo', $idPovo)->exists()) {
            return response()->json([
                'message' => 'Este Povo já está associado ao conflito'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        // Cria a relação
        $conflito->povos()->attach($idPovo);
        
        // Retorna o conflito com os assuntos atualizados
        return response()->json([
            'message' => 'Povo adicionado com sucesso',
            'data' => $conflito->load('povos')
        ]);
    }
    
    /**
     * Remove um Povo de um conflito
     *
     * @OA\Delete(
     *     path="/api/conflito/{idConflito}/povo/{idPovo}",
     *     tags={"Conflitos"},
     *     security={ {"sanctum": {} } },
     *     summary="Remove a associação de um Povo com um conflito",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="idConflito",
     *         in="path",
     *         description="ID do conflito",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="idPovo",
     *         in="path",
     *         description="ID do Povo",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Povo desassociado com sucesso",
     *     ),
     *     @OA\Response(response=404, description="Conflito ou Povo não encontrado")
     * )
     */
    public function detachPovo($idConflito, $idPovo)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $conflito = Conflito::findOrFail($idConflito);
        
        if(!$conflito->exists()){
            return response()->json([
                'message' => 'Conflito não encontrado'
            ], Response::HTTP_NOT_FOUND);
        }
        
        // Verifica se o povo existe
        Povo::findOrFail($idPovo);
        
        // Remove a relação
        $conflito->povos()->detach($idPovo);
        
        return response()->json([
            'message' => 'Povo removido com sucesso',
            'data' => $conflito->load('povos')
        ]);
    }
    
    /**
     * @OA\Get(
     *     path="/api/conflito/{id}/assuntos",
     *     tags={"Conflitos"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter os assuntos de um conflito específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Assuntos de um conflito",
     *         @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Assunto")
     *          )
     *     )
     * )
     */
    public function getAssuntos($id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $conflito = Conflito::findOrFail($id);
        $assuntos = $conflito->assuntos()->get();

        return response()->json($assuntos);
    }

    /**
     * Adiciona um assunto a um conflito
     *
     * @OA\Post(
     *     path="/api/conflito/{id}/assunto",
     *     tags={"Conflitos"},
     *     security={ {"sanctum": {} } },
     *     summary="Associa um assunto a um conflito",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do conflito",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"idAssunto"},
     *             @OA\Property(property="idAssunto", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Assunto associado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Conflito")
     *     ),
     *     @OA\Response(
     *          response=404, 
     *          description="Conflito ou Assunto não encontrado"
     *     ),
     *     @OA\Response(
     *          response=422, 
     *          description="Validação falhou"
     *     )
     * )
     */
    public function attachAssunto(Request $request, $id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $request->validate([
            'idAssunto' => 'required|integer|exists:assunto,idAssunto'
        ]);
        
        $conflito = Conflito::findOrFail($id);
        $idAssunto = $request->input('idAssunto');
        
        if(!$conflito->exists()){
            return response()->json([
                'message' => 'Conflito não encontrado'
            ], Response::HTTP_NOT_FOUND);
        }
        
        // Verifica se a relação já existe
        if ($conflito->assuntos()->where('assunto_conflito.idAssunto', $idAssunto)->exists()) {
            return response()->json([
                'message' => 'Este assunto já está associado ao conflito'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        // Cria a relação
        $conflito->assuntos()->attach($idAssunto);
        
        // Retorna o conflito com os assuntos atualizados
        return response()->json([
            'message' => 'Assunto adicionado com sucesso',
            'data' => $conflito->load('assuntos')
        ]);
    }
    
    /**
     * Remove um assunto de um conflito
     *
     * @OA\Delete(
     *     path="/api/conflito/{idConflito}/assunto/{idAssunto}",
     *     tags={"Conflitos"},
     *     security={ {"sanctum": {} } },
     *     summary="Remove a associação de um assunto com um conflito",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="idConflito",
     *         in="path",
     *         description="ID do conflito",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="idAssunto",
     *         in="path",
     *         description="ID do assunto",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Assunto desassociado com sucesso",
     *     ),
     *     @OA\Response(response=404, description="Conflito ou Assunto não encontrado")
     * )
     */
    public function detachAssunto($idConflito, $idAssunto)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $conflito = Conflito::findOrFail($idConflito);
        
        if(!$conflito->exists()){
            return response()->json([
                'message' => 'Conflito não encontrado'
            ], Response::HTTP_NOT_FOUND);
        }
        
        // Verifica se o assunto existe
        Assunto::findOrFail($idAssunto);

        // Remove a relação
        $conflito->assuntos()->detach($idAssunto);

        return response()->json([
            'message' => 'Assunto removido com sucesso',
            'data' => $conflito->load('assuntos')
        ]);
    }
    
    /**
     * @OA\Get(
     *     path="/api/conflito/{id}/impactos-ambientais",
     *     tags={"Conflitos"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter os Impactos Ambientais de um conflito específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Impactos Ambientais de um conflito",
     *         @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/ImpactoAmbiental")
     *          )
     *     )
     * )
     */
    public function getImpactosAmbientais($id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $conflito = Conflito::findOrFail($id);
        $impactosAmbientais = $conflito->impactosAmbientais()->get();
        
        return response()->json($impactosAmbientais);
    }
    
    /**
     * Adiciona um Impacto Ambiental a um conflito
     *
     * @OA\Post(
     *     path="/api/conflito/{id}/impacto-ambiental",
     *     tags={"Conflitos"},
     *     security={ {"sanctum": {} } },
     *     summary="Associa um Impacto Ambiental a um conflito",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do conflito",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"idImpactoAmbiental"},
     *             @OA\Property(property="idImpactoAmbiental", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Impacto Ambiental associado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Conflito")
     *     ),
     *     @OA\Response(response=404, description="Conflito ou Impacto Ambiental não encontrado"),
     *     @OA\Response(response=422, description="Validação falhou")
     * )
     */
    public function attachImpactoAmbiental(Request $request, $id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $request->validate([
            'idImpactoAmbiental' => 'required|integer|exists:impacto_ambiental,idImpactoAmbiental'
        ]);
        
        $conflito = Conflito::findOrFail($id);
        $idImpactoAmbiental = $request->input('idImpactoAmbiental');
        
        if(!$conflito->exists()){
            return response()->json([
                'message' => 'Conflito não encontrado'
            ], Response::HTTP_NOT_FOUND);
        }
        
        // Verifica se a relação já existe
        if ($conflito->impactosAmbientais()->where('impacto_ambiental.idImpactoAmbiental', $idImpactoAmbiental)->exists()) {
            return response()->json([
                'message' => 'Este Impacto Ambiental já está associado ao conflito'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        // Cria a relação
        $conflito->impactosAmbientais()->attach($idImpactoAmbiental);
        
        // Retorna o conflito com os assuntos atualizados
        return response()->json([
            'message' => 'Impacto Ambiental adicionado com sucesso',
            'data' => $conflito->load('impactosAmbientais')
        ]);
    }
    
    /**
     * Remove um Impacto Ambiental de um conflito
     *
     * @OA\Delete(
     *     path="/api/conflito/{idConflito}/impacto-ambiental/{idImpactoAmbiental}",
     *     tags={"Conflitos"},
     *     security={{"sanctum":{}}},
     *     summary="Remove a associação de um impacto ambiental com um conflito",
     *     @OA\Parameter(
     *         name="idConflito",
     *         in="path",
     *         description="ID do conflito",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="idImpactoAmbiental",
     *         in="path",
     *         description="ID do Impacto Ambiental",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Impacto Ambiental desassociado com sucesso",
     *     ),
     *     @OA\Response(
     *         response=401, 
     *         description="Não autorizado"
     *     )
     * )
     */
    public function detachImpactoAmbiental($idConflito, $idImpactoAmbiental)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $conflito = Conflito::findOrFail($idConflito);
        
        // Verifica se o assunto existe
        ImpactoAmbiental::findOrFail($idImpactoAmbiental);
        
        // Remove a relação
        $conflito->impactosambientais()->detach($idImpactoAmbiental);
        
        return response()->json([
            'message' => 'Impacto Ambiental removido com sucesso',
            'data' => $conflito->load('impactosambientais')
        ]);
    }
    
    
    /**
     * @OA\Get(
     *     path="/api/conflito/{id}/impactos-saude",
     *     tags={"Conflitos"},
     *     security={{"sanctum":{}}},
     *     summary="Obter os Impactos Saude de um conflito específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Impactos Saude de um conflito",
     *         @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/ImpactoSaude")
     *          )
     *     ),
     *     @OA\Response(
     *         response=401, 
     *         description="Não autorizado"
     *     )
     * )
     */
    public function getImpactosSaude($id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $conflito = Conflito::findOrFail($id);
        $impactosSaude = $conflito->impactosSaude()->get();
        
        return response()->json($impactosSaude);
    }
    
    /**
     * Adiciona um Impacto Saude a um conflito
     *
     * @OA\Post(
     *     path="/api/conflito/{id}/impacto-saude",
     *     tags={"Conflitos"},
     *     security={{"sanctum":{}}},
     *     summary="Associa um Impacto Saude a um conflito",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do conflito",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"idImpactoSaude"},
     *             @OA\Property(property="idImpactoSaude", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Impacto Ambiental associado com sucesso",
     *         @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/ImpactoSaude")
     *          )
     *     ),
     *     @OA\Response(
     *                  response=404, 
     *                  description="Conflito ou Impacto Saude não encontrado"),
     *     @OA\Response(
     *                  response=422, 
     *                  description="Validação falhou")
     * )
     */
    public function attachImpactoSaude(Request $request, $id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $request->validate([
            'idImpactoSaude' => 'required|integer|exists:impacto_saude,idImpactoSaude'
        ]);
        
        $conflito = Conflito::findOrFail($id);
        $idImpactoSaude = $request->input('idImpactoSaude');
        
        if(!$conflito->exists()){
            return response()->json([
                'message' => 'Conflito não encontrado'
            ], Response::HTTP_NOT_FOUND);
        }
        
        // Verifica se a relação já existe
        if ($conflito->impactosSaude()->where('impacto_saude.idImpactoSaude', $idImpactoSaude)->exists()) {
            return response()->json([
                'message' => 'Este Impacto Saude já está associado ao conflito'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        // Cria a relação
        $conflito->impactosSaude()->attach($idImpactoSaude);
        
        // Retorna o conflito com os assuntos atualizados
        return response()->json([
            'message' => 'Impacto na Saúde adicionado com sucesso',
            'data' => $conflito->load('impactosSaude')
        ]);
    }
    
    /**
     * Remove um Impacto Saude de um conflito
     *
     * @OA\Delete(
     *     path="/api/conflito/{idConflito}/impacto-saude/{idImpactoSaude}",
     *     tags={"Conflitos"},
     *     security={{"sanctum":{}}},
     *     summary="Remove a associação de um impacto saúde com um conflito",
     *     @OA\Parameter(
     *         name="idConflito",
     *         in="path",
     *         description="ID do conflito",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="idImpactoSaude",
     *         in="path",
     *         description="ID do Impacto Saude",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Impacto Saude desassociado com sucesso",
     *     ),
     *     @OA\Response(
     *          response=401, 
     *          description="Não autorizado"
     *     )
     * )
     */
    public function detachImpactoSaude($idConflito, $idImpactoSaude)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $conflito = Conflito::findOrFail($idConflito);
        
        // Verifica se o assunto existe
        ImpactoSaude::findOrFail($idImpactoSaude);
        
        // Remove a relação
        $conflito->impactosSaude()->detach($idImpactoSaude);
        
        return response()->json([
            'message' => 'Impacto Saúde removido com sucesso',
            'data' => $conflito->load('impactosSaude')
        ]);
    }
    
    /**
     * @OA\Get(
     *     path="/api/conflito/{id}/impactos-socio-economicos",
     *     tags={"Conflitos"},
     *     security={{"sanctum":{}}},
     *     summary="Obter os Impactos Socio Economico de um conflito específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Impactos Socio Economico de um conflito",
     *         @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/ImpactoSocioEconomico")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401, 
     *          description="Não autorizado"
     *     )
     * )
     */
    public function getImpactosSocioEconomicos($id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $conflito = Conflito::findOrFail($id);
        $impactosSocioEconomicos = $conflito->impactosSocioEconomicos()->get();
        
        return response()->json($impactosSocioEconomicos);
    }
    
    /**
     * Adiciona um Impacto Socio Economico a um conflito
     *
     * @OA\Post(
     *     path="/api/conflito/{id}/impacto-socio-economico",
     *     tags={"Conflitos"},
     *     security={{"sanctum":{}}},
     *     summary="Associa um Impacto Socio Economico a um conflito",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do conflito",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"idImpactoSocioEconomico"},
     *             @OA\Property(property="idImpactoSocioEconomico", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Impacto Socio Economico associado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Conflito")
     *     ),
     *     @OA\Response(
     *          response=401, 
     *          description="Não autorizado"
     *     ),
     *     @OA\Response(
     *          response=404, 
     *          description="Conflito ou Impacto Socio Economico não encontrado"
     *     ),
     *     @OA\Response(
     *          response=422, 
     *          description="Validação falhou"
     *     )
     * )
     */
    public function attachImpactoSocioEconomico(Request $request, $id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $request->validate([
            'idImpactoSocioEconomico' => 'required|integer|exists:impacto_socio_economico,idImpactoSocioEconomico'
        ]);
        
        $conflito = Conflito::findOrFail($id);
        $idImpactoSocioEconomico = $request->input('idImpactoSocioEconomico');
        
        if(!$conflito->exists()){
            return response()->json([
                'message' => 'Conflito não encontrado'
            ], Response::HTTP_NOT_FOUND);
        }
        
        // Verifica se a relação já existe
        if ($conflito->impactosSocioEconomicos()->where('impacto_socio_economico.idImpactoSocioEconomico', $idImpactoSocioEconomico)->exists()) {
            return response()->json([
                'message' => 'Este Impacto Socio Economico já está associado ao conflito'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        // Cria a relação
        $conflito->impactosSocioEconomicos()->attach($idImpactoSocioEconomico);
        
        // Retorna o conflito com os assuntos atualizados
        return response()->json([
            'message' => 'Impacto na Socio Economico adicionado com sucesso',
            'data' => $conflito->load('impactosSocioEconomicos')
        ]);
    }
    
    /**
     * Remove um Impacto Saude de um conflito
     *
     * @OA\Delete(
     *     path="/api/conflito/{idConflito}/impacto-socio-economico/{idSocioEconomico}",
     *     tags={"Conflitos"},
     *     security={{"sanctum":{}}},
     *     summary="Remove a associação de um impacto Socio Economico com um conflito",
     *     @OA\Parameter(
     *         name="idConflito",
     *         in="path",
     *         description="ID do conflito",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="idImpactoSocioEconomico",
     *         in="path",
     *         description="ID do Impacto Socio Economico",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Impacto Socio Economico desassociado com sucesso",
     *     ),
     *     @OA\Response(
     *         response=401, 
     *         description="Não autorizado"
     *     )
     * )
     */
    public function detachImpactoSocioEconomico($idConflito, $idSocioEconomico)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $conflito = Conflito::findOrFail($idConflito);
        
        // Verifica se o assunto existe
        ImpactoSocioEconomico::findOrFail($idSocioEconomico);
        
        // Remove a relação
        $conflito->impactosSocioEconomicos()->detach($idSocioEconomico);
        
        return response()->json([
            'message' => 'Impacto Socio Economico removido com sucesso',
            'data' => $conflito->load('impactosSocioEconomicos')
        ]);
    }
    
    
    /**
     * @OA\Get(
     *     path="/api/conflito/{id}/tipos-conflito",
     *     tags={"Conflitos"},
     *     security={{"sanctum":{}}},
     *     summary="Obter os Tipos de Conflito de um conflito específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipos de Conflito de um conflito",
     *         @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/TipoConflito")
     *          )
     *     ),
     *     @OA\Response(
     *         response=401, 
     *         description="Não autorizado"
     *     )
     * )
     */
    public function getTiposConflito($id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $conflito = Conflito::findOrFail($id);
        $tiposConflito = $conflito->tiposConflito()->get();
        
        return response()->json($tiposConflito);
    }
    
    /**
     * Adiciona um Tipo de Conflito a um conflito
     *
     * @OA\Post(
     *     path="/api/conflito/{id}/tipo-conflito",
     *     tags={"Conflitos"},
     *     security={{"sanctum":{}}},
     *     summary="Associa um Tipo de Conflito a um conflito",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do conflito",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"idTipoConflito"},
     *             @OA\Property(property="idTipoConflito", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de Conflito associado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Conflito")
     *     ),
     *     @OA\Response(
     *         response=401, 
     *         description="Não autorizado"
     *     ),
     *     @OA\Response(
     *          response=404, 
     *          description="Conflito ou Tipo de Conflito não encontrado"
     *     ),
     *     @OA\Response(
     *          response=422, 
     *          description="Validação falhou")
     * )
     */
    public function attachTipoConflito(Request $request, $id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $request->validate([
            'idTipoConflito' => 'required|integer|exists:tipo_conflito,idTipoConflito'
        ]);
        
        $conflito = Conflito::findOrFail($id);
        $idTipoConflito = $request->input('idTipoConflito');
        
        if(!$conflito->exists()){
            return response()->json([
                'message' => 'Conflito não encontrado'
            ], Response::HTTP_NOT_FOUND);
        }
        
        // Verifica se a relação já existe
        if ($conflito->tiposConflito()->where('tipo_conflito.idTipoConflito', $idTipoConflito)->exists()) {
            return response()->json([
                'message' => 'Este Tipo de Conflito já está associado ao conflito'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        // Cria a relação
        $conflito->tiposConflito()->attach($idTipoConflito);
        
        // Retorna o conflito com os assuntos atualizados
        return response()->json([
            'message' => 'Tipo de Conflito adicionado com sucesso',
            'data' => $conflito->load('tiposConflito')
        ]);
    }
    
    /**
     * Remove um Tipo de Conflito de um conflito
     *
     * @OA\Delete(
     *     path="/api/conflito/{idConflito}/tipo-conflito/{idTipoConflito}",
     *     tags={"Conflitos"},
     *     security={{"sanctum":{}}},
     *     summary="Remove a associação de um tipo de conflito com um conflito",
     *     @OA\Parameter(
     *         name="idConflito",
     *         in="path",
     *         description="ID do conflito",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="idTipoConflito",
     *         in="path",
     *         description="ID do tipo de conflito",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de Conflito desassociado com sucesso",
     *     ),
     *     @OA\Response(
     *         response=401, 
     *         description="Não autorizado"
     *     )
     * )
     */
    public function detachTipoConflito($idConflito, $idTipoConflito)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $conflito = Conflito::findOrFail($idConflito);
        
        // Verifica se o assunto existe
        TipoConflito::findOrFail($idTipoConflito);
        
        // Remove a relação
        $conflito->tiposconflito()->detach($idTipoConflito);
        
        return response()->json([
            'message' => 'Tipo de Conflito removido com sucesso',
            'data' => $conflito->load('tiposconflito')
        ]);
    }
    
}
