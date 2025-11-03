<?php

namespace App\Http\Controllers;

use App\Models\Assunto;
use App\Models\Conflito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\TipoConflito;
use App\Models\ImpactoAmbiental;
use App\Models\ImpactoSaude;
use App\Models\ImpactoSocioEconomico;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use App\Models\TerraIndigena;
use App\Models\Povo;
use App\Models\NumeroSeiIdentificacaoConflito;
use App\Models\ViolenciaPessoaNaoIndigena;
use App\Models\ViolenciaPessoaIndigena;
use App\Models\ViolenciaPatrimonial;
use App\Models\AtorIdentificadoConflito;
use App\Models\Inquerito;
use App\Models\ProgramaProtecao;
use App\Models\ProcessoJudicial;
use Illuminate\Support\Facades\Log;
use App\Models\LocalidadeConflito;
use App\Models\RegistroBoNf;

/**
 *  @OA\Schema(
 *     schema="Conflito",
 *     type="object",
 *     @OA\Property(property="nome", type="string", example="nome do conflito"),
 *     @OA\Property(property="relato", type="string", example="Relato do conflito"),
 *     @OA\Property(property="dataConflito", type="date", format="yyyy-mm-dd", example="2025-04-13"),
 *     @OA\Property(property="latitude", type="string", example="41.40338"),
 *     @OA\Property(property="longitude", type="string", example="2.17403"),
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
 * @OA\Schema(
 *     schema="Localidade",
 *     type="object",
 *     @OA\Property(property="idLocalidade", type="integer", example="1"),
 *     @OA\Property(property="regiao", type="string", example="AMAZÔNIA"),
 *     @OA\Property(property="uf", type="string", example="PA"),
 *     @OA\Property(property="municipio", type="string", example="Belém")
 * )
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
     *          *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Número de itens por página",
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Número da página",
     *         @OA\Schema(type="integer", minimum=1, example=1)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Termo de busca por nome",
     *         @OA\Schema(type="string", example="conflito teste")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         description="Campo para ordenação",
     *         @OA\Schema(type="string", enum={"nome", "dataInicioConflito", "created_at", "updated_at"}, example="dataInicioConflito")
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         required=false,
     *         description="Direção da ordenação",
     *         @OA\Schema(type="string", enum={"asc", "desc"}, example="desc")
     *     ),
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
    public function index(Request $request)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        try {
            // Valida parâmetros
            $validator = validator($request->all(), [
                'per_page'   => 'nullable|integer|min:1|max:100',
                'page'       => 'nullable|integer|min:1',
                'search'     => 'nullable|string|max:255',
                'sort_by'    => 'nullable|string|in:nome,dataInicioConflito,dataAcionamentoMpiConflito,created_at,updated_at',
                'sort_order' => 'nullable|string|in:asc,desc'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parâmetros inválidos',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            // Configurações
            $perPage   = $request->per_page ?? 15;
            $sortBy    = $request->sort_by ?? 'dataInicioConflito';
            $sortOrder = $request->sort_order ?? 'desc';
            $search    = $request->search;
            $page      = $request->page;
            
            // Query base
            $query = Conflito::with([
                'aldeias',
                'assuntos',
                'atoresIdentificados',
                'categoriasAtores',
                'impactosAmbientais',
                'impactosSaude',
                'impactosSocioEconomicos',
                'inqueritos',
                'numerosSeiIdentificacaoConflito',
                'povos',
                'processosJudiciais',
                'programasProtecao',
                'terrasIndigenas',
                'tiposConflito',
                'violenciasPatrimoniais',
                'violenciasPessoasIndigenas',
                'violenciasPessoasNaoIndigenas'
            ]);
            
            // Aplica busca se fornecida
            if (!empty($search)) {
                $query->where('nome', 'LIKE', "%{$search}%");
            }
            
            // Aplica ordenação
            $query->orderBy($sortBy, $sortOrder);
            
            // Lógica de paginação/busca
            if (!empty($search)) {
                // Se há busca, retorna todos os resultados sem paginação
                $conflitos = $query->get();
                
                return response()->json([
                    'success' => true,
                    'data' => ['data' => $conflitos],
                    'message' => 'Resultados da busca retornados com sucesso.'
                ]);
            } else {
                // Se não há busca, usa paginação
                $currentPage = $page ?? 1; // Se page for nulo, usa 1 como padrão
                
                $conflitos = $query->paginate($perPage, ['*'], 'page', $currentPage);
                
                return response()->json([
                    'success' => true,
                    'data' => $conflitos,
                    'message' => 'Conflitos paginados recuperados com sucesso.'
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Erro em index:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar conflitos: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * @OA\Get(
     *     path="/api/conflito/dashboard",
     *     tags={"Conflitos"},
     *     summary="Listar todos os conflitos para o dashboard",
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
    public function getAllDashboard(Request $request)
    {
        
        try {
            // Query base
            $conflitos = Conflito::all();
                        
            return response()->json([
                'success' => true,
                'data' => $conflitos,
                'message' => 'Dados de Conflito retornados com sucesso.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro em index:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar conflitos: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
     *             @OA\Property(property="relato", type="string", example="Relato do conflito"),
     *             @OA\Property(property="regiao", type="string", example="norte"),
     *             @OA\Property(property="dataConflito", type="date", format="yyyy-mm-dd", example="2025-04-13"),
     *             @OA\Property(property="latitude", type="string", example="41.40338"),
     *             @OA\Property(property="longitude", type="string", example="2.17403"),
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
        
        $auth = Auth::guard('sanctum')->user();
        
        $validator = Validator::make($request->all(), $this->getRegrasValidacao());
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        try {
            
            Log::info('Processos Judiciais:', ['processos' => $request->processosJudiciais]);
            Log::info('Programa Protecao:', ['programasProtecao' => $request->programasProtecao]);
            
            DB::beginTransaction();
            
            
            // Criar o conflito principal
            $conflitoData = $request->only(['latitude',
                                            'longitude',
                                            'nome',
                                            'relato',
                                            'dataInicioConflito',
                                            'dataAcionamentoMpiConflito',
                                            'observacoes',
                                            'flagHasImpactoAmbiental',
                                            'flagHasImpactoSaude',
                                            'flagHasImpactoSocioEconomico',
                                            'flagHasViolenciaIndigena',
                                            'flagHasMembroProgramaProtecao',
                                            'flagHasBOouNF',
                                            'flagHasInquerito',
                                            'flagHasProcessoJudicial',
                                            'flagHasAssistenciaJuridica',
                                            'flagHasRegiaoPrioritaria',
                                            'flagHasViolenciaPatrimonialIndigena',
                                            'flagHasEventoViolenciaIndigena',
                                            'flagHasAssassinatoPrisaoNaoIndigena',
                                            'tipoInstituicaoAssistenciaJuridica',
                                            'advogadoInstituicaoAssistenciaJuridica',
                                            'regiaoPrioritaria',
                                            'classificacaoGravidadeConflitoDemed',
                                            'atualizacaoClassificacaoGravidadeConflito',
                                            'dataReferenciaMudancaClassificacao',
                                            'estrategiaGeralUtilizadaDemed',
                                            'estrategiaColetiva']);
            
            
            $conflitoData['created_by'] = $auth->email;
            $conflitoData['updated_by'] = $auth->email;
            
            $conflito = Conflito::create($conflitoData);
            
            // Criar números SEI de identificação
            if ($request->has('atores_identificados') && is_array($request->atores_identificados)) {
                foreach ($request->atores_identificados as $ator) {
                    if (!empty(trim($ator))) {
                        AtorIdentificadoConflito::create([
                            'idConflito' => $conflito->idConflito,
                            'nome' => trim($ator)
                        ]);
                    }
                }
            }
            
            // Cadastrar processos SEI de identificação
            if ($request->has('numeros_sei_identificacao_conflito') && is_array($request->numeros_sei_identificacao_conflito)) {
                foreach ($request->numeros_sei_identificacao_conflito as $numeroSei) {
                    if (!empty(trim($numeroSei))) {
                        NumeroSeiIdentificacaoConflito::create([
                            'idConflito' => $conflito->idConflito,
                            'numeroSei' => trim($numeroSei)
                        ]);
                    }
                }
            }
            
            // Criar inqueritos
            if ($request->has('inqueritos') && is_array($request->inqueritos)) {
                foreach ($request->inqueritos as $inquerito) {
                    Inquerito::create([
                        'idConflito' => $conflito->idConflito,
                        'data'       => $inquerito['data'] ?? null,
                        'numero'     => $inquerito['numero'] ?? null,
                        'orgao'      => $inquerito['orgao'] ?? null,
                        'tipoOrgao'  => $inquerito['tipoOrgao'] ?? null,
                        'numeroSei'  => $inquerito['numeroSei'] ?? null
                    ]);
                }
            }
            
            // Criar localidadesConflito
            if ($request->has('localidades_conflito') && is_array($request->localidades_conflito)) {
                foreach ($request->localidades_conflito as $localidade) {
                    LocalidadeConflito::create([
                        'idConflito' => $conflito->idConflito,
                        'regiao'     => $localidade['regiao'] ?? null,
                        'uf'         => $localidade['uf'] ?? null,
                        'municipio'  => $localidade['municipio'] ?? null
                    ]);
                }
            }
            
            
            // Criar processosJudiciais
            if ($request->has('processos_judiciais') && is_array($request->processos_judiciais)) {
                foreach ($request->processos_judiciais as $processo) {
                    ProcessoJudicial::create([
                        'idConflito' => $conflito->idConflito,
                        'data'       => $processo['data'] ?? null,
                        'numero'     => $processo['numero'] ?? null,
                        'tipoPoder'  => $processo['tipoPoder'] ?? null,
                        'orgaoApoio' => $processo['orgaoApoio'] ?? null,
                        'numeroSei'  => $processo['numeroSei'] ?? null
                    ]);
                }
            }
            
            // Criar programasProtecao
            if ($request->has('programas_protecao') && is_array($request->programas_protecao)) {
                foreach ($request->programas_protecao as $programaProtecao) {
                    ProgramaProtecao::create([
                        'idConflito'   => $conflito->idConflito,
                        'tipoPrograma' => $programaProtecao['tipoPrograma'] ?? null,
                        'uf'           => $programaProtecao['uf'] ?? null,
                        'numeroSei'    => $programaProtecao['numeroSei'] ?? null
                    ]);
                }
            }
            
            //Criar 
            if ($request->has('registros_b_oou_n_f') && is_array($request->registros_b_oou_n_f)) {
                foreach ($request->registros_b_oou_n_f as $registro) {
                    RegistroBoNf::create([
                        'idConflito' => $conflito->idConflito,
                        'data'       => $registro['data'] ?? null,
                        'numero'     => $registro['numero'] ?? null,
                        'orgao'      => $registro['orgao'] ?? null,
                        'tipoOrgao'  => $registro['tipoOrgao'] ?? null,
                        'numeroSei'  => $registro['numeroSei'] ?? null
                    ]);
                }
            }
            
            // Criar violências patrimoniais
            if ($request->has('violencias_patrimoniais') && is_array($request->violencias_patrimoniais)) {
                foreach ($request->violencias_patrimoniais as $violenciaPatrimonial) {
                    ViolenciaPatrimonial::create([
                        'idConflito'    => $conflito->idConflito,
                        'tipoViolencia' => $violenciaPatrimonial['tipoViolencia'] ?? null,
                        'data'          => $violenciaPatrimonial['data'] ?? null,
                        'numeroSei'     => $violenciaPatrimonial['numeroSei'] ?? null
                    ]);
                }
            }
            
            // Criar violências contra pessoas indígenas
            if ($request->has('violencias_pessoas_indigenas') && is_array($request->violencias_pessoas_indigenas)) {
                foreach ($request->violencias_pessoas_indigenas as $violenciaIndigena) {
                    ViolenciaPessoaIndigena::create([
                        'idConflito' => $conflito->idConflito,
                        'tipoViolencia'        => $violenciaIndigena['tipoViolencia'] ?? null,
                        'data'                 => $violenciaIndigena['data'] ?? null,
                        'nome'                 => $violenciaIndigena['nome'] ?? null,
                        'idade'                => $violenciaIndigena['idade'] ?? null,
                        'faixaEtaria'          => $violenciaIndigena['faixaEtaria'] ?? null,
                        'genero'               => $violenciaIndigena['genero'] ?? null,
                        'instrumentoViolencia' => $violenciaIndigena['instrumentoViolencia'] ?? null,
                        'numeroSei'            => $violenciaIndigena['numeroSei'] ?? null
                    ]);
                }
            }
            
            // Criar violências contra pessoas não indígenas
            if ($request->has('violencias_pessoas_nao_indigenas') && is_array($request->violencias_pessoas_nao_indigenas)) {
                foreach ($request->violencias_pessoas_nao_indigenas as $violenciaNaoIndigena) {
                    ViolenciaPessoaNaoIndigena::create([
                        'idConflito'    => $conflito->idConflito,
                        'tipoViolencia' => $violenciaNaoIndigena['tipoViolencia'] ?? null,
                        'tipoPessoa'    => $violenciaNaoIndigena['tipoPessoa'] ?? null,
                        'data'          => $violenciaNaoIndigena['data'] ?? null,
                        'nome'          => $violenciaNaoIndigena['nome'] ?? null,
                        'numeroSei'     => $violenciaNaoIndigena['numeroSei'] ?? null
                    ]);
                }
            }
            
            // Sincroniza relações N:M
            if ($request->has('aldeias') && is_array($request->aldeias)) {
                $conflito->aldeias()->sync($request->aldeias);
            }
            
            if ($request->has('assuntos') && is_array($request->assuntos)) {
                $conflito->assuntos()->sync($request->assuntos);
            }
            
            if ($request->has('impactos_ambientais') && is_array($request->impactos_ambientais)) {
                $conflito->impactosAmbientais()->sync($request->impactos_ambientais);
            }
            
            if ($request->has('impactos_saude') && is_array($request->impactos_saude)) {
                $conflito->impactosSaude()->sync($request->impactos_saude);
            }
            
            if ($request->has('impactos_socio_economicos') && is_array($request->impactos_socio_economicos)) {
                $conflito->impactosSocioEconomicos()->sync($request->impactos_socio_economicos);
            }
            
            if ($request->has('povos') && is_array($request->povos)) {
                $conflito->povos()->sync($request->povos);
            }
            
            if ($request->has('terras_indigenas') && is_array($request->terras_indigenas)) {
                $conflito->terrasIndigenas()->sync($request->terras_indigenas);
            }
            
            if ($request->has('tipos_conflito') && is_array($request->tipos_conflito)) {
                $conflito->tiposConflito()->sync($request->tipos_conflito);
            }
            
            // Sincronizar categorias de atores
            if ($request->has('categorias_atores') && is_array($request->categorias_atores)) {
                $conflito->categoriasAtores()->sync($request->categorias_atores);
            }
            
            DB::commit();
            
            // Carregar relacionamentos para retornar o conflito completo
            $conflitoCompleto = Conflito::with(['aldeias',
                                                'assuntos',
                                                'atoresIdentificados',
                                                'categoriasAtores',
                                                'impactosAmbientais',
                                                'impactosSaude',
                                                'impactosSocioEconomicos',
                                                'inqueritos',
                                                'localidadesConflito',
                                                'numerosSeiIdentificacaoConflito',
                                                'povos',
                                                'processosJudiciais',
                                                'programasProtecao',
                                                'registrosBOouNF',
                                                'terrasIndigenas',
                                                'tiposConflito',
                                                'violenciasPatrimoniais',
                                                'violenciasPessoasIndigenas',
                                                'violenciasPessoasNaoIndigenas'
                                            ])->find($conflito->idConflito);
            
            return response()->json($conflitoCompleto, Response::HTTP_CREATED);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Erro ao criar conflito',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        
        $conflito = Conflito::with(['aldeias',
                                    'assuntos',
                                    'atoresIdentificados',
                                    'categoriasAtores',
                                    'impactosAmbientais',
                                    'impactosSaude',
                                    'impactosSocioEconomicos',
                                    'inqueritos',
                                    'localidadesConflito',
                                    'numerosSeiIdentificacaoConflito',
                                    'povos',
                                    'processosJudiciais',
                                    'programasProtecao',
                                    'registrosBOouNF',
                                    'terrasIndigenas',
                                    'tiposConflito',
                                    'violenciasPatrimoniais',
                                    'violenciasPessoasIndigenas',
                                    'violenciasPessoasNaoIndigenas'
                                ])->findOrFail($id);
        
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
        
        $auth = Auth::guard('sanctum')->user();
        
        $validator = Validator::make($request->all(), $this->getRegrasValidacao());
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        try {
            DB::beginTransaction();
            
            // Buscar o conflito existente
            $conflito = Conflito::findOrFail($id);
            
            // Atualizar o conflito principal
            $conflitoData = $request->only(['latitude',
                                            'longitude',
                                            'nome',
                                            'relato',
                                            'dataInicioConflito',
                                            'dataAcionamentoMpiConflito',
                                            'observacoes',
                                            'flagHasImpactoAmbiental',
                                            'flagHasImpactoSaude',
                                            'flagHasImpactoSocioEconomico',
                                            'flagHasViolenciaIndigena',
                                            'flagHasMembroProgramaProtecao',
                                            'flagHasBOouNF',
                                            'flagHasInquerito',
                                            'flagHasProcessoJudicial',
                                            'flagHasAssistenciaJuridica',
                                            'flagHasRegiaoPrioritaria',
                                            'flagHasViolenciaPatrimonialIndigena',
                                            'flagHasEventoViolenciaIndigena',
                                            'flagHasAssassinatoPrisaoNaoIndigena',
                                            'tipoInstituicaoAssistenciaJuridica',
                                            'advogadoInstituicaoAssistenciaJuridica',
                                            'regiaoPrioritaria',
                                            'classificacaoGravidadeConflitoDemed',
                                            'atualizacaoClassificacaoGravidadeConflito',
                                            'dataReferenciaMudancaClassificacao',
                                            'estrategiaGeralUtilizadaDemed',
                                            'estrategiaColetiva']);
            
            $conflitoData['updated_by'] = $auth->email;
            
            $conflito->update($conflitoData);
            
            // ATUALIZAR: Atores identificados - deletar e recriar
            if ($request->has('atores_identificados')) {
                $conflito->atoresIdentificados()->delete();
                if (is_array($request->atores_identificados)) {
                    foreach ($request->atores_identificados as $ator) {
                        AtorIdentificadoConflito::create([
                            'idConflito' => $conflito->idConflito,
                            'nome' => (is_array($ator)) ? $ator['nome'] : $ator
                        ]);
                    }
                }
            }
            
            // ATUALIZAR: Números SEI de identificação - deletar e recriar
            if ($request->has('numeros_sei_identificacao_conflito')) {
                $conflito->numerosSeiIdentificacaoConflito()->delete();
                if (is_array($request->numeros_sei_identificacao_conflito)) {
                    foreach ($request->numeros_sei_identificacao_conflito as $numeroSei) {
                        NumeroSeiIdentificacaoConflito::create([
                            'idConflito' => $conflito->idConflito,
                            'numeroSei' => (is_array($numeroSei)) ? $numeroSei['numeroSei'] : $numeroSei
                        ]);
                    }
                }
            }
            
            // ATUALIZAR: Inquéritos - deletar e recriar
            if ($request->has('inqueritos')) {
                $conflito->inqueritos()->delete();
                if (is_array($request->inqueritos)) {
                    foreach ($request->inqueritos as $inquerito) {
                        Inquerito::create([
                            'idConflito' => $conflito->idConflito,
                            'data'       => $inquerito['data'] ?? null,
                            'numero'     => $inquerito['numero'] ?? null,
                            'orgao'      => $inquerito['orgao'] ?? null,
                            'tipoOrgao'  => $inquerito['tipoOrgao'] ?? null,
                            'numeroSei'  => $inquerito['numeroSei'] ?? null
                        ]);
                    }
                }
            }
            
            // ATUALIZAR: Localidades - deletar e recriar
            if ($request->has('localidades_conflito')) {
                $conflito->localidadesConflito()->delete();
                if (is_array($request->localidades_conflito)) {
                    foreach ($request->localidades_conflito as $localidade) {
                        LocalidadeConflito::create([
                            'idConflito' => $conflito->idConflito,
                            'regiao'     => $localidade['regiao'] ?? null,
                            'uf'         => $localidade['uf'] ?? null,
                            'municipio'  => $localidade['municipio'] ?? null
                        ]);
                    }
                }
            }
            
            // ATUALIZAR: Processos judiciais - deletar e recriar
            if ($request->has('processos_judiciais')) {
                $conflito->processosJudiciais()->delete();
                if (is_array($request->processos_judiciais)) {
                    foreach ($request->processos_judiciais as $processo) {
                        ProcessoJudicial::create([
                            'idConflito' => $conflito->idConflito,
                            'data'       => $processo['data'] ?? null,
                            'numero'     => $processo['numero'] ?? null,
                            'tipoPoder'  => $processo['tipoPoder'] ?? null,
                            'orgaoApoio' => $processo['orgaoApoio'] ?? null,
                            'numeroSei'  => $processo['numeroSei'] ?? null
                        ]);
                    }
                }
            }
            
            // ATUALIZAR: Programas de proteção - deletar e recriar
            if ($request->has('programas_protecao')) {
                $conflito->programasProtecao()->delete();
                if (is_array($request->programas_protecao)) {
                    foreach ($request->programas_protecao as $programaProtecao) {
                        ProgramaProtecao::create([
                            'idConflito'   => $conflito->idConflito,
                            'tipoPrograma' => $programaProtecao['tipoPrograma'] ?? null,
                            'uf'           => $programaProtecao['uf'] ?? null,
                            'numeroSei'    => $programaProtecao['numeroSei'] ?? null
                        ]);
                    }
                }
            }
            
            //ATUALIZAR: Registros BO ou NF - deletar e recriar
            if ($request->has('registros_b_oou_n_f') && is_array($request->registros_b_oou_n_f)) {
                $conflito->registrosBOouNF()->delete();
                foreach ($request->registros_b_oou_n_f as $registro) {
                    RegistroBoNf::create([
                        'idConflito' => $conflito->idConflito,
                        'data'       => $registro['data'] ?? null,
                        'numero'     => $registro['numero'] ?? null,
                        'orgao'      => $registro['orgao'] ?? null,
                        'tipoOrgao'  => $registro['tipoOrgao'] ?? null,
                        'numeroSei'  => $registro['numeroSei'] ?? null
                    ]);
                }
            }
            
            // ATUALIZAR: Violências patrimoniais - deletar e recriar
            if ($request->has('violencias_patrimoniais')) {
                $conflito->violenciasPatrimoniais()->delete();
                if (is_array($request->violencias_patrimoniais)) {
                    foreach ($request->violencias_patrimoniais as $violenciaPatrimonial) {
                        ViolenciaPatrimonial::create([
                            'idConflito' => $conflito->idConflito,
                            'tipoViolencia' => $violenciaPatrimonial['tipoViolencia'] ?? null,
                            'data' => $violenciaPatrimonial['data'] ?? null,
                            'numeroSei' => $violenciaPatrimonial['numeroSei'] ?? null
                        ]);
                    }
                }
            }
            
            // ATUALIZAR: Violências contra pessoas indígenas - deletar e recriar
            if ($request->has('violencias_pessoas_indigenas')) {
                $conflito->violenciasPessoasIndigenas()->delete();
                if (is_array($request->violencias_pessoas_indigenas)) {
                    foreach ($request->violencias_pessoas_indigenas as $violenciaIndigena) {
                        ViolenciaPessoaIndigena::create([
                            'idConflito'           => $conflito->idConflito,
                            'tipoViolencia'        => $violenciaIndigena['tipoViolencia'] ?? null,
                            'data'                 => $violenciaIndigena['data'] ?? null,
                            'nome'                 => $violenciaIndigena['nome'] ?? null,
                            'idade'                => $violenciaIndigena['idade'] ?? null,
                            'faixaEtaria'          => $violenciaIndigena['faixaEtaria'] ?? null,
                            'genero'               => $violenciaIndigena['genero'] ?? null,
                            'instrumentoViolencia' => $violenciaIndigena['instrumentoViolencia'] ?? null,
                            'numeroSei'            => $violenciaIndigena['numeroSei'] ?? null
                        ]);
                    }
                }
            }
            
            // ATUALIZAR: Violências contra pessoas não indígenas - deletar e recriar
            if ($request->has('violencias_pessoas_nao_indigenas')) {
                $conflito->violenciasPessoasNaoIndigenas()->delete();
                if (is_array($request->violencias_pessoas_nao_indigenas)) {
                    foreach ($request->violencias_pessoas_nao_indigenas as $violenciaNaoIndigena) {
                        ViolenciaPessoaNaoIndigena::create([
                            'idConflito'    => $conflito->idConflito,
                            'tipoViolencia' => $violenciaNaoIndigena['tipoViolencia'] ?? null,
                            'tipoPessoa'    => $violenciaNaoIndigena['tipoPessoa'] ?? null,
                            'data'          => $violenciaNaoIndigena['data'] ?? null,
                            'nome'          => $violenciaNaoIndigena['nome'] ?? null,
                            'numeroSei'     => $violenciaNaoIndigena['numeroSei'] ?? null
                        ]);
                    }
                }
            }
            
             
            
            // ATUALIZAR: Relações N:M - usar sync (já faz update automático)
            
            if ($request->has('aldeias')) {
                $ids = $this->processarRelacionamento($request->aldeias, 'idAldeia');
                $conflito->aldeias()->sync($ids);
            } else {
                $conflito->aldeias()->sync([]);
            }
            
            if ($request->has('assuntos') && is_array($request->assuntos)) {
                $ids = $this->processarRelacionamento($request->assuntos, 'idAssunto');
                $conflito->assuntos()->sync($ids);
            } else {
                $conflito->assuntos()->sync([]);
            }
            
            if ($request->has('impactos_ambientais') && is_array($request->impactos_ambientais)) {
                $ids = $this->processarRelacionamento($request->impactos_ambientais, 'idImpactoAmbiental');
                $conflito->impactosAmbientais()->sync($ids);
            } else {
                $conflito->impactosAmbientais()->sync([]);
            }
            
            if ($request->has('impactos_saude') && is_array($request->impactos_saude)) {
                $ids = $this->processarRelacionamento($request->impactos_saude, 'idImpactoSaude');
                $conflito->impactosSaude()->sync($ids);
            } else {
                $conflito->impactosSaude()->sync([]);
            }
            
            if ($request->has('impactos_socio_economicos') && is_array($request->impactos_socio_economicos)) {
                $ids = $this->processarRelacionamento($request->impactos_socio_economicos, 'idImpactoSocioEconomico');
                $conflito->impactosSocioEconomicos()->sync($ids);
            } else {
                $conflito->impactosSocioEconomicos()->sync([]);
            }
            
            if ($request->has('povos') && is_array($request->povos)) {
                $ids = $this->processarRelacionamento($request->povos, 'idPovo');
                $conflito->povos()->sync($ids);
            } else {
                $conflito->povos()->sync([]);
            }
            
            if ($request->has('terras_indigenas') && is_array($request->terras_indigenas)) {
                $ids = $this->processarRelacionamento($request->terras_indigenas, 'idTerraIndigena');
                $conflito->terrasIndigenas()->sync($ids);
            } else {
                $conflito->terrasIndigenas()->sync([]);
            }
            
            if ($request->has('tipos_conflito') && is_array($request->tipos_conflito)) {
                $ids = $this->processarRelacionamento($request->tipos_conflito, 'idTipoConflito');
                $conflito->tiposConflito()->sync($ids);
            } else {
                $conflito->tiposConflito()->sync([]);
            }
            
            // ATUALIZAR: Categorias de atores
            if ($request->has('categorias_atores') && is_array($request->categorias_atores)) {
                $ids = $this->processarRelacionamento($request->categorias_atores, 'idCategoriaAtor');
                $conflito->categoriasAtores()->sync($ids);
            } else {
                $conflito->categoriasAtores()->sync([]);
            }
            
            DB::commit();
            
            // Carregar relacionamentos para retornar o conflito completo
            $conflitoCompleto = Conflito::with(['aldeias',
                                                'assuntos',
                                                'atoresIdentificados',
                                                'categoriasAtores',
                                                'impactosAmbientais',
                                                'impactosSaude',
                                                'impactosSocioEconomicos',
                                                'inqueritos',
                                                'localidadesConflito',
                                                'numerosSeiIdentificacaoConflito',
                                                'povos',
                                                'processosJudiciais',
                                                'programasProtecao',
                                                'registrosBOouNF',
                                                'terrasIndigenas',
                                                'tiposConflito',
                                                'violenciasPatrimoniais',
                                                'violenciasPessoasIndigenas',
                                                'violenciasPessoasNaoIndigenas'
                                            ])->find($conflito->idConflito);
            
            return response()->json($conflitoCompleto, Response::HTTP_OK);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erro ao atualizar conflito:', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Erro ao atualizar conflito',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
     *     path="/api/conflito/{id}/localidades",
     *     tags={"Conflitos"},
     *     security={{"sanctum":{}}},
     *     summary="Obter as localideds de um conflito específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Localidades de um conflito",
     *         @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Localidade")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Não autorizado"
     *     )
     * )
     */
    public function getLocalidades($id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $conflito = Conflito::findOrFail($id);
        $localidades = $conflito->localidades()->get();
        
        return response()->json($localidades);
    }
    
    /**
     * Adiciona uma Localidade a um conflito
     *
     * @OA\Post(
     *     path="/api/conflito/{id}/localidade",
     *     tags={"Conflitos"},
     *     security={{"sanctum":{}}},
     *     summary="Associa uma Localidade a um conflito",
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
     *             required={"idLocalidade"},
     *             @OA\Property(property="idLocalidade", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Localidade associado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Conflito")
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Não autorizado"
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Conflito ou Localidade não encontrado"
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Validação falhou"
     *     )
     * )
     */
    public function attachLocalidade(Request $request, $id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $request->validate([
            'idLocalidade' => 'required|integer|exists:localidade,idLocalidade'
        ]);
        
        $conflito = Conflito::findOrFail($id);
        $idLocalidade = $request->input('idLocalidade');
        
        if(!$conflito->exists()){
            return response()->json([
                'message' => 'Conflito não encontrado'
            ], Response::HTTP_NOT_FOUND);
        }
        
        // Verifica se a relação já existe
        if ($conflito->localidades()->where('localidade.idLocalidade', $idLocalidade)->exists()) {
            return response()->json([
                'message' => 'Essa Localidade já está associada ao conflito'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        // Cria a relação
        $conflito->localidades()->attach($idLocalidade);
        
        // Retorna o conflito com os assuntos atualizados
        return response()->json([
            'message' => 'Localidade adicionada com sucesso',
            'data' => $conflito->load('localidades')
        ]);
    }
    
    /**
     * Remove uma Localidade de um conflito
     *
     * @OA\Delete(
     *     path="/api/conflito/{idConflito}/localidade/{idLocalidade}",
     *     tags={"Conflitos"},
     *     security={{"sanctum":{}}},
     *     summary="Remove a associação de uma Localidade com um conflito",
     *     @OA\Parameter(
     *         name="idConflito",
     *         in="path",
     *         description="ID do conflito",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="idLocalidade",
     *         in="path",
     *         description="ID da Localidade",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Localidade desassociada com sucesso",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
    public function detachLocalidade($idConflito, $idLocalidade)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $conflito = Conflito::findOrFail($idConflito);
        
        // Verifica se o assunto existe
        ImpactoSocioEconomico::findOrFail($idLocalidade);
        
        // Remove a relação
        $conflito->localidades()->detach($idLocalidade);
        
        return response()->json([
            'message' => 'Localidade removida com sucesso',
            'data' => $conflito->load('localidades')
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
    
    /**
     * @OA\Get(
     *     path="/api/conflito/por-status/{status}",
     *     tags={"Conflitos"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar conflitos por status",
     *     @OA\Parameter(
     *         name="status",
     *         in="path",
     *         required=true,
     *         description="Status do conflito",
     *         @OA\Schema(type="string", example="ATIVO")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Número de itens por página",
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Número da página",
     *         @OA\Schema(type="integer", minimum=1, example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de conflitos por status",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Conflito")),
     *              @OA\Property(property="message", type="string", example="Conflitos com status ATIVO recuperados com sucesso.")
     *          )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Parâmetros inválidos"
     *     )
     * )
     */
    public function getConflitosPorStatus(Request $request, $status)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        try {
            $validator = validator(['status' => $status], [
                'status' => 'required|string|max:50'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status inválido',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            $perPage = $request->query('per_page', 15);
            $page = $request->query('page');
            
            $query = Conflito::where('status', $status)
            ->with([
                'aldeias',
                'assuntos',
                'atoresIdentificados',
                'categoriasAtores',
                'impactosAmbientais',
                'impactosSaude',
                'impactosSocioEconomicos',
                'inqueritos',
                'numerosSeiIdentificacaoConflito',
                'povos',
                'processosJudiciais',
                'programasProtecao',
                'terrasIndigenas',
                'tiposConflito',
                'violenciasPatrimoniais',
                'violenciasPessoasIndigenas',
                'violenciasPessoasNaoIndigenas'
            ])->orderBy('dataInicioConflito', 'desc');
            
            if (!empty($page)) {
                $conflitos = $query->paginate($perPage);
            } else {
                $conflitos = $query->get();
            }
            
            return response()->json([
                'success' => true,
                'data' => $conflitos,
                'message' => "Conflitos com status {$status} recuperados com sucesso."
                ]);
            
        } catch (\Exception $e) {
            Log::error('Erro em getConflitosPorStatus:', [
                'status' => $status,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar conflitos por status: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * @OA\Get(
     *     path="/api/conflito/por-status-usuario/{status}/{email}",
     *     tags={"Conflitos"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar conflitos por status e usuário",
     *     @OA\Parameter(
     *         name="status",
     *         in="path",
     *         required=true,
     *         description="Status do conflito",
     *         @OA\Schema(type="string", example="ATIVO")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         required=true,
     *         description="Email do usuário",
     *         @OA\Schema(type="string", example="usuario@example.com")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Número de itens por página",
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Número da página",
     *         @OA\Schema(type="integer", minimum=1, example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de conflitos por status e usuário",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Conflito")),
     *              @OA\Property(property="message", type="string", example="Conflitos com status ATIVO do usuário usuario@example.com recuperados com sucesso.")
     *          )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Parâmetros inválidos"
     *     )
     * )
     */
    public function getConflitosPorStatusEUsuario(Request $request, $status, $email)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        try {
            $validator = validator([
                'status' => $status,
                'email' => $email
            ], [
                'status' => 'required|string|max:50',
                'email' => 'required|email|max:255'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parâmetros inválidos',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            $perPage = $request->query('per_page', 15);
            $page = $request->query('page');
            
            $query = Conflito::where('status', $status)
            ->where('created_by', $email)
            ->with([
                'aldeias',
                'assuntos',
                'atoresIdentificados',
                'categoriasAtores',
                'impactosAmbientais',
                'impactosSaude',
                'impactosSocioEconomicos',
                'inqueritos',
                'numerosSeiIdentificacaoConflito',
                'povos',
                'processosJudiciais',
                'programasProtecao',
                'terrasIndigenas',
                'tiposConflito',
                'violenciasPatrimoniais',
                'violenciasPessoasIndigenas',
                'violenciasPessoasNaoIndigenas'
            ])->orderBy('dataInicioConflito', 'desc');
            
            if (!empty($page)) {
                $conflitos = $query->paginate($perPage);
            } else {
                $conflitos = $query->get();
            }
            
            return response()->json([
                'success' => true,
                'data' => $conflitos,
                'message' => "Conflitos com status {$status} do usuário {$email} recuperados com sucesso."
                ]);
            
        } catch (\Exception $e) {
            Log::error('Erro em getConflitosPorStatusEUsuario:', [
                'status' => $status,
                'email' => $email,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar conflitos por status e usuário: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * @OA\Patch(
     *     path="/api/conflito/{id}/set-analise",
     *     summary="Define conflito como EM ANÁLISE",
     *     description="Altera o status do conflito para 'EM ANALISE'",
     *     operationId="setAnalise",
     *     tags={"Conflitos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do conflito",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Conflito definido como EM ANÁLISE com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Conflito definido como EM ANÁLISE com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Conflito não encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function setAnalise($id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $auth = Auth::guard('sanctum')->user();
        
        try {
            $conflito = Conflito::find($id);
            
            if (!$conflito) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conflito não encontrado.'
                ], Response::HTTP_NOT_FOUND);
            }
            
            // Verifica se já está em análise
            if ($conflito->status === 'EM ANALISE') {
                return response()->json([
                    'success' => false,
                    'message' => 'Conflito já está EM ANÁLISE.'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            // Atualiza o status
            $conflito->update([
                'status' => 'EM ANALISE',
                'updated_by' => $auth->email
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $conflito,
                'message' => 'Conflito definido como EM ANÁLISE com sucesso.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro em setAnalise: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao definir conflito como EM ANÁLISE. - '.$e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/conflito/{id}/set-devolvido",
     *     summary="Define conflito como DEVOLVIDO",
     *     description="Altera o status do conflito para 'DEVOLVIDO'",
     *     operationId="setDevolvido",
     *     tags={"Conflitos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do conflito",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"revisao"},
     *             @OA\Property(property="revisao", type="text", example="Texto de revisão do conflito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Conflito definido como DEVOLVIDO com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Conflito definido como DEVOLVIDO com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Conflito não encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Conflito já devolvido"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function setDevolvido(Request $request, $id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $auth = Auth::guard('sanctum')->user();
        
        try {
            $conflito = Conflito::find($id);
            
            if (!$conflito) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conflito não encontrado.'
                ], Response::HTTP_NOT_FOUND);
            }
            
            // Verifica se já está em análise
            if ($conflito->status === 'DEVOLVIDO') {
                return response()->json([
                    'success' => false,
                    'message' => 'Conflito já está DEVOLVIDO.'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            // Atualiza o status
            $conflito->update([
                'status' => 'DEVOLVIDO',
                'revisao' => $request->input('revisao'),
                'updated_by' => $auth->email
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $conflito,
                'message' => 'Conflito definido como DEVOLVIDO com sucesso.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro em setAprovado: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao definir conflito como DEVOLVIDO. - '.$e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * @OA\Patch(
     *     path="/api/conflito/{id}/set-aprovado",
     *     summary="Define conflito como APROVADO",
     *     description="Altera o status do conflito para 'APROVADO'",
     *     operationId="setAprovado",
     *     tags={"Conflitos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do conflito",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Conflito definido como APROVADO com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Conflito definido como APROVADO com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Conflito não encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Conflito já aprovado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function setAprovado($id)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $auth = Auth::guard('sanctum')->user();
        
        try {
            $conflito = Conflito::find($id);
            
            if (!$conflito) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conflito não encontrado.'
                ], Response::HTTP_NOT_FOUND);
            }
            
            // Verifica se já está em análise
            if ($conflito->status === 'APROVADO') {
                return response()->json([
                    'success' => false,
                    'message' => 'Conflito já está APROVADO.'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            // Atualiza o status
            $conflito->update([
                'status' => 'APROVADO',
                'updated_by' => $auth->email
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $conflito,
                'message' => 'Conflito definido como APROVADO com sucesso.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro em setAprovado: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao definir conflito como APROVADO. - '.$e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Regras de validação do formulário de conflito
     * 
     * @param array $conflito
     * @return array
     */
    private function getRegrasValidacao()
    {
        return [
            // Campos obrigatórios básicos
            'latitude'           => ['required', 'numeric', 'between:-90,90'],
            'longitude'          => ['required', 'numeric', 'between:-180,180'],
            'nome'               => ['required', 'string', 'max:255'],
            'relato'             => ['required', 'string'],
            'dataInicioConflito' => ['required', 'date'],
            
            // Flags (valores permitidos: SIM/NÃO)
            'flagHasImpactoAmbiental'               => ['required', 'in:SIM,NÃO'],
            'flagHasImpactoSaude'                   => ['required', 'in:SIM,NÃO'],
            'flagHasImpactoSocioEconomico'          => ['required', 'in:SIM,NÃO'],
            'flagHasViolenciaIndigena'              => ['required', 'in:SIM,NÃO'],
            'flagHasMembroProgramaProtecao'         => ['required', 'in:SIM,NÃO'],
            'flagHasBOouNF'                         => ['required', 'in:SIM,NÃO'],
            'flagHasInquerito'                      => ['required', 'in:SIM,NÃO'],
            'flagHasProcessoJudicial'               => ['required', 'in:SIM,NÃO'],
            'flagHasAssistenciaJuridica'            => ['required', 'in:SIM,NÃO'],
            'flagHasRegiaoPrioritaria'              => ['required', 'in:SIM,NÃO'],
            
            // Flags de violência - CONDICIONAIS
            'flagHasViolenciaPatrimonialIndigena' => [
                'nullable',
                'in:SIM,NÃO',
                function ($attribute, $value, $fail) {
                    $hasViolenciaIndigena = request('flagHasViolenciaIndigena') === 'SIM';
                    if ($hasViolenciaIndigena && empty($value)) {
                        $fail('Quando há violência indígena, é obrigatório informar se há violência patrimonial indígena.');
                    }
                }
                ],
                
            'flagHasEventoViolenciaIndigena' => [
                'nullable',
                'in:SIM,NÃO',
                function ($attribute, $value, $fail) {
                    $hasViolenciaIndigena = request('flagHasViolenciaIndigena') === 'SIM';
                    if ($hasViolenciaIndigena && empty($value)) {
                        $fail('Quando há violência indígena, é obrigatório informar se há evento de violência indígena.');
                    }
                }
                ],
                    
            'flagHasAssassinatoPrisaoNaoIndigena' => [
                'nullable',
                'in:SIM,NÃO',
                function ($attribute, $value, $fail) {
                    $hasViolenciaIndigena = request('flagHasViolenciaIndigena') === 'SIM';
                    if ($hasViolenciaIndigena && empty($value)) {
                        $fail('Quando há violência indígena, é obrigatório informar se há assassinato ou prisão de não indígena.');
                    }
                }
                ],
                        
            // Validações condicionais para arrays
            'impactos_ambientais' => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) {
                    $hasImpacto = request('flagHasImpactoAmbiental') === 'SIM';
                    if ($hasImpacto && empty($value)) {
                        $fail('Quando há impacto ambiental, pelo menos um tipo de impacto deve ser selecionado.');
                    }
                }
                ],
                            
            'impactos_saude' => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) {
                    $hasImpacto = request('flagHasImpactoSaude') === 'SIM';
                    if ($hasImpacto && empty($value)) {
                        $fail('Quando há impacto na saúde, pelo menos um tipo de impacto deve ser selecionado.');
                    }
                }
                ],
                                
            'impactos_socio_economicos' => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) {
                    $hasImpacto = request('flagHasImpactoSocioEconomico') === 'SIM';
                    if ($hasImpacto && empty($value)) {
                        $fail('Quando há impacto socioeconômico, pelo menos um tipo de impacto deve ser selecionado.');
                    }
                }
                ],
                                    
            // Validações condicionais para violências - ATUALIZADAS
            'violenciasPatrimoniais' => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) {
                    $hasViolencia = request('flagHasViolenciaPatrimonialIndigena') === 'SIM';
                    $hasViolenciaIndigena = request('flagHasViolenciaIndigena') === 'SIM';
                    
                    // Só valida se houver violência indígena E violência patrimonial marcada como SIM
                    if ($hasViolenciaIndigena && $hasViolencia && empty($value)) {
                        $fail('Quando há violência patrimonial indígena, pelo menos uma ocorrência deve ser informada.');
                    }
                }
                ],
                                        
            'violenciasPessoasIndigenas' => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) {
                    $hasViolencia = request('flagHasEventoViolenciaIndigena') === 'SIM';
                    $hasViolenciaIndigena = request('flagHasViolenciaIndigena') === 'SIM';
                    
                    // Só valida se houver violência indígena E evento de violência marcada como SIM
                    if ($hasViolenciaIndigena && $hasViolencia && empty($value)) {
                        $fail('Quando há evento de violência indígena, pelo menos uma ocorrência deve ser informada.');
                    }
                }
                ],
                                            
            'violenciasPessoasNaoIndigenas' => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) {
                    $hasViolencia = request('flagHasAssassinatoPrisaoNaoIndigena') === 'SIM';
                    $hasViolenciaIndigena = request('flagHasViolenciaIndigena') === 'SIM';
                    
                    // Só valida se houver violência indígena E assassinato/prisão marcada como SIM
                    if ($hasViolenciaIndigena && $hasViolencia && empty($value)) {
                        $fail('Quando há assassinato ou prisão de não indígena, pelo menos uma ocorrência deve ser informada.');
                    }
                }
                ],
                                                
            // Regras para elementos dos arrays
//             'numerosSeiIdentificacaoConflito' => 'nullable|array',
//             'numerosSeiIdentificacaoConflito.*' => 'string|max:50',
            
//             'atores_identificados' => 'nullable|array',
//             'atores_identificados.*' => 'string|max:200',
            
//             'aldeias' => 'nullable|array',
//             'aldeias.*' => 'required|integer|exists:aldeia,idAldeia',
            
//             'assuntos' => 'nullable|array',
//             'assuntos.*' => 'required|integer|exists:assunto,idAssunto',
            
//             'impactos_ambientais' => 'nullable|array',
//             'impactos_ambientais.*' => 'required|integer|exists:impacto_ambiental,idImpactoAmbiental',
            
//             'impactos_saude' => 'nullable|array',
//             'impactos_saude.*' => 'required|integer|exists:impacto_saude,idImpactoSaude',
            
//             'impactos_socio_economicos' => 'nullable|array',
//             'impactos_socio_economicos.*' => 'required|integer|exists:impacto_socio_economico,idImpactoSocioEconomico',
            
//             'povos' => 'nullable|array',
//             'povos.*' => 'required|integer|exists:povo,idPovo',
            
//             'terras_indigenas' => 'nullable|array',
//             'terras_indigenas.*' => 'required|integer|exists:terra_indigena,idTerraIndigena',
            
//             'tipos_conflito' => 'nullable|array',
//             'tipos_conflito.*' => 'required|integer|exists:tipo_conflito,idTipoConflito',
            
//             'categorias_atores' => 'nullable|array',
//             'categorias_atores.*' => 'required|integer|exists:categoria_ator,idCategoriaAtor',
            
//             'violenciasPatrimoniais.*.tipoViolencia' => 'required|string|max:100',
//             'violenciasPatrimoniais.*.data' => 'required|date',
//             'violenciasPatrimoniais.*.numeroSei' => 'nullable|string|max:50',
            
//             'violenciasPessoasIndigenas.*.tipoViolencia' => 'required|string|max:100',
//             'violenciasPessoasIndigenas.*.data' => 'required|date',
//             'violenciasPessoasIndigenas.*.nome' => 'required|string|max:255',
//             'violenciasPessoasIndigenas.*.idade' => 'nullable|string|max:20',
//             'violenciasPessoasIndigenas.*.faixaEtaria' => 'nullable|string|max:50',
//             'violenciasPessoasIndigenas.*.genero' => 'nullable|string|max:50',
//             'violenciasPessoasIndigenas.*.instrumentoViolencia' => 'nullable|string|max:255',
//             'violenciasPessoasIndigenas.*.numeroSei' => 'nullable|string|max:50',
            
//             'violenciasPessoasNaoIndigenas.*.tipoViolencia' => 'required|string|max:100',
//             'violenciasPessoasNaoIndigenas.*.tipoPessoa' => 'required|string|max:100',
//             'violenciasPessoasNaoIndigenas.*.data' => 'required|date',
//             'violenciasPessoasNaoIndigenas.*.nome' => 'required|string|max:255',
//             'violenciasPessoasNaoIndigenas.*.numeroSei' => 'nullable|string|max:50',
            ];
    }
    
    /**
     * Processa array de relacionamentos que pode vir como objetos ou IDs
     */
    private function processarRelacionamento($dados, $chaveId = 'id') {
        if (empty($dados)) {
            return [];
        }
        
        // Se já é array de IDs simples
        if (!is_array($dados[0]) && !is_object($dados[0])) {
            return array_map('intval', $dados);
        }
        
        // É array de objetos - extrair IDs
        return array_map(function ($item) use ($chaveId) {
            $itemArray = (array) $item;
            return intval($itemArray[$chaveId] ?? $itemArray['id'] ?? null);
        }, $dados);
    }
        
}
