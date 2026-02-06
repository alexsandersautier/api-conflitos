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
use Maatwebsite\Excel\Facades\Excel;
use \Maatwebsite\Excel\Concerns\FromCollection;
use \Maatwebsite\Excel\Concerns\WithHeadings;
use \Maatwebsite\Excel\Concerns\WithMapping;
use \Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Exports\ConflitosExport;
use Throwable; // Importante para capturar erros fatais

/**
 *  @OA\Schema(
 *     schema="Conflito",
 *     type="object",
 *     
 *     @OA\Property(property="nome", type="string", example="nome do conflito"),
 *     @OA\Property(property="relato", type="text", example="Relato do conflito"),
 *     @OA\Property(property="dataInicioConflito", type="date", format="yyyy-mm-dd", example="2025-04-13"),
 *     @OA\Property(property="dataAcionamentoMpiConflito", type="date", format="yyyy-mm-dd", example="2025-04-13"),
 *     @OA\Property(property="latitude", type="string", example="41.40338"),
 *     @OA\Property(property="longitude", type="string", example="2.17403"),
 *     @OA\Property(property="observacoes", type="text", example="Relato do conflito"),
 *     
 *     @OA\Property(property="flagHasImpactoAmbiental", type="string", example="SIM"),
 *     @OA\Property(property="flagHasImpactoSaude", type="string", example="NÃO"),
 *     @OA\Property(property="flagHasImpactoSocioEconomico", type="string", example="SIM"),
 *     @OA\Property(property="flagHasViolenciaIndigena", type="string", example="NÃO"),
 *     @OA\Property(property="flagHasMembroProgramaProtecao", type="string", example="SIM"),
 *     @OA\Property(property="flagHasBOouNF", type="string", example="NÃO"),
 *     @OA\Property(property="flagHasInquerito", type="string", example="SIM"),
 *     @OA\Property(property="flagHasProcessoJudicial", type="string", example="NÃO"),
 *     @OA\Property(property="flagHasAssistenciaJuridica", type="string", example="SIM"),
 *     @OA\Property(property="flagHasRegiaoPrioritaria", type="string", example="NÃO"),
 *     @OA\Property(property="flagHasViolenciaPatrimonialIndigena", type="string", example="SIM"),
 *     @OA\Property(property="flagHasEventoViolenciaIndigena", type="string", example="NÃO"),
 *     @OA\Property(property="flagHasAssassinatoPrisaoNaoIndigena", type="string", example="SIM"),
 *     
 *     @OA\Property(property="tipoInstituicaoAssistenciaJuridica", type="string", example="Defensoria Pública do Estado (DPE)"),
 *     @OA\Property(property="advogadoInstituicaoAssistenciaJuridica", type="string", example="Defensoria Pública do Estado do Amazonas"),
 *     @OA\Property(property="regiaoPrioritaria", type="string", example="Sul e extremo sul da Bahia"),
 *     @OA\Property(property="classificacaoGravidadeConflitoDemed", type="string", example="Pouca Urgência"),
 *     @OA\Property(property="atualizacaoClassificacaoGravidadeConflito", type="string", enum={"Pouca Urgência", "Urgência", "Não Urgente", "Emergência"}, example="Pouca Urgência"),
 *     @OA\Property(property="dataReferenciaMudancaClassificacao", type="date", format="yyyy-mm-dd", example="2025-04-13"),
 *     @OA\Property(property="estrategiaGeralUtilizadaDemed", type="string", example="Realização de reuniões pontuais com os atores envolvidos"),
 *     @OA\Property(property="estrategiaColetiva", type="text", example="Participação do MPI na Assembleia do Conselho Territorial Tupiniquim Guarani e formulação de minuta através de deliberações tomadas em conjunto com CONJUR e SEGAT a partir das estratégias de diálogo com os povos considerando a ida ao território em
 06/09/2025."),
 *     @OA\Property(property="status", type="string", example="CADASTRADO"),
 *     @OA\Property(property="revisao", type="text", example="txto de revisão do conflito"),
 *     
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
     * path="/api/conflito",
     * tags={"Conflitos"},
     * summary="Lista e filtra dados de conflitos",
     * description="Retorna uma lista paginada de conflitos com base em múltiplos filtros (localidade, tipo de violência, dados jurídicos, etc). Requer token Bearer.",
     * security={{"sanctum":{}}},
     *
     * @OA\Parameter(name="page",                           in="query", description="Número da página",                                                 required=false, @OA\Schema(type="integer", default=1)),
     * @OA\Parameter(name="per_page",                       in="query", description="Itens por página",                                                 required=false, @OA\Schema(type="integer", default=15)),
     * @OA\Parameter(name="search",                         in="query", description="Busca textual (Nome ou Relato do conflito)",                       required=false, @OA\Schema(type="string")),
     * @OA\Parameter(name="sort_by",                        in="query", description="Campo para ordenação",                                             required=false, @OA\Schema(type="string", enum={"nome", "dataInicioConflito", "dataAcionamentoMpiConflito", "created_at", "updated_at"}, default="dataInicioConflito")),
     * @OA\Parameter(name="sort_order",                     in="query", description="Direção da ordenação",                                             required=false, @OA\Schema(type="string", enum={"asc", "desc"}, default="desc")),
     * @OA\Parameter(name="regiao",                         in="query", description="Região (Norte, Nordeste, etc)",                                    required=false, @OA\Schema(type="string", enum={"Amazônia", "Centro-Oeste", "Nordeste", "Sudeste", "Sul"})),
     * @OA\Parameter(name="uf",                             in="query", description="Sigla da UF (Ex: AM, PA)",                                         required=false, @OA\Schema(type="string", minLength=2, maxLength=2)),
     * @OA\Parameter(name="municipio",                      in="query", description="Nome do município",                                                required=false, @OA\Schema(type="string")),
     * @OA\Parameter(name="terraIndigena",                  in="query", description="ID ou Nome da Terra Indígena",                                     required=false, @OA\Schema(type="string")),
     * @OA\Parameter(name="povo",                           in="query", description="ID do Povo Indígena",                                              required=false, @OA\Schema(type="integer")),
     * @OA\Parameter(name="estrategiaGeralUtilizadaDemed",  in="query", description="Filtro por Estratégia DEMED",                                      required=false, @OA\Schema(type="string")),
     * @OA\Parameter(name="processoSei",                    in="query", description="Número do Processo SEI",                                           required=false, @OA\Schema(type="string")),
     * @OA\Parameter(name="categoriaAtor",                  in="query", description="ID da Categoria do Ator",                                          required=false, @OA\Schema(type="integer")),
     * @OA\Parameter(name="assunto",                        in="query", description="ID do Assunto",                                                    required=false, @OA\Schema(type="integer")),
     * @OA\Parameter(name="impactoAmbiental",               in="query", description="ID do Tipo de Impacto Ambiental",                                  required=false, @OA\Schema(type="integer")),
     * @OA\Parameter(name="impactoSaude",                   in="query", description="ID do Tipo de Impacto à Saúde",                                    required=false, @OA\Schema(type="integer")),
     * @OA\Parameter(name="impactoSocioEconomico",          in="query", description="ID do Tipo de Impacto Socioeconômico",                             required=false, @OA\Schema(type="integer")),
     * @OA\Parameter(name="tipoViolenciaPatrimonial",       in="query", description="Tipo de Violência Patrimonial",                                    required=false, @OA\Schema(type="string")),
     * @OA\Parameter(name="tipoViolenciaPessoaIndigena",    in="query", description="Tipo de Violência contra Pessoa Indígena",                         required=false, @OA\Schema(type="string")),
     * @OA\Parameter(name="tipoViolenciaPessoaNaoIndigena", in="query", description="Tipo de Violência contra Pessoa Não Indígena",                     required=false, @OA\Schema(type="string")),
     * @OA\Parameter(name="programaProtecao",               in="query", description="ID do Programa de Proteção",                                       required=false, @OA\Schema(type="integer")),
     * @OA\Parameter(name="classificacaoGravidade",         in="query", description="Classificação da gravidade/urgência",                              required=false, @OA\Schema(type="string", enum={"Emergência", "Pouca Urgência", "Urgência", "Não Urgente"})),
     * @OA\Parameter(name="boletimOcorrencia",              in="query", description="Número do B.O. ou Documento SEI",                                  required=false, @OA\Schema(type="string")),
     * @OA\Parameter(name="inquerito",                      in="query", description="Número do Inquérito ou Documento SEI",                             required=false, @OA\Schema(type="string")),
     * @OA\Parameter(name="processoJudicial",               in="query", description="Número do Processo Judicial ou Documento SEI",                     required=false, @OA\Schema(type="string")),
     * @OA\Parameter(name="assistenciaJuridica",            in="query", description="Tipo de Instituição Jurídica ou Advogado de Instituição Jurídica", required=false, @OA\Schema(type="string")),
     * @OA\Response(response=200, description="Sucesso",
     *      @OA\JsonContent(
     *          @OA\Property(property="success",      type="boolean", example=true),
     *          @OA\Property(property="message",      type="string", example="Conflitos recuperados com sucesso."),
     *          @OA\Property(property="data",         type="object", description="Objeto de paginação do Laravel ou Array de dados se houver busca textual",
     *          @OA\Property(property="current_page", type="integer", example=1),
     *          @OA\Property(property="data",         type="array", @OA\Items(ref="#/components/schemas/Conflito")),
     *          @OA\Property(property="total",        type="integer", example=50)
     *      )
     *  )
     * ),
     * @OA\Response(response=401, description="Não autorizado",
     *  @OA\JsonContent(
     *      @OA\Property(property="message", type="string", example="Não autorizado")
     *  )
     * ),
     * @OA\Response(response=422, description="Erro de Validação",
     *  @OA\JsonContent(
     *      @OA\Property(property="success", type="boolean", example=false),
     *      @OA\Property(property="message", type="string", example="Parâmetros inválidos"),
     *      @OA\Property(property="errors", type="object")
     *  )
     * )
     * )
     */
    public function index(Request $request)
    {
        // 1. Verificação de Autenticação
        if (!Auth::guard('sanctum')->check()) {
            return response()->json(['message' => 'Não autorizado'], Response::HTTP_UNAUTHORIZED);
        }
        
        try {
            // 2. Validação (Adicionados novos campos)
            $validator = validator($request->all(), [
                'per_page'                      => 'nullable|integer|min:1|max:100',
                'page'                          => 'nullable|integer|min:1',
                'sort_by'                       => 'nullable|string',
                'sort_order'                    => 'nullable|string|in:asc,desc',
                
                // Filtros de Texto / IDs
                'search'                        => 'nullable|string|max:255',
                'estrategiaGeralUtilizadaDemed' => 'nullable|string|max:255',
                'uf'                            => 'nullable|string|size:2',
                'municipio'                     => 'nullable|string|max:255',
                'terraIndigena'                 => 'nullable|integer',
                'povo'                          => 'nullable|integer',
                'processoSei'                   => 'nullable|string|max:255',
                
                // Filtros de Categorias/Tipos (Ids ou Strings)
                'categoriaAtor'                  => 'nullable|string',
                'assunto'                        => 'nullable|integer',
                'tipoConflito'                   => 'nullable|integer',
                'impactoAmbiental'               => 'nullable|integer',
                'impactoSaude'                   => 'nullable|integer',
                'impactoSocioEconomico'          => 'nullable|integer',
                'tipoViolenciaPatrimonial'       => 'nullable|string',
                'tipoViolenciaPessoaIndigena'    => 'nullable|string',
                'tipoViolenciaPessoaNaoIndigena' => 'nullable|string',
                'programaProtecao'               => 'nullable|string',
                
                // Filtros Jurídicos/Policiais (Geralmente Booleanos ou números)
                'boletimOcorrencia'             => 'nullable|string',
                'inquerito'                     => 'nullable|string',
                'processoJudicial'              => 'nullable|string',
                'assistenciaJuridica'           => 'nullable|string',
                'classificacaoGravidade'        => 'nullable|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parâmetros inválidos',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            // 3. Definição de Variáveis
            $perPage   = $request->per_page ?? 15;
            $sortBy    = $request->sort_by ?? 'dataInicioConflito';
            $sortOrder = $request->sort_order ?? 'desc';
            $page      = $request->page ?? 1;
            
            // 4. Query Base com Eager Loading
            $query = Conflito::with([
                                    'aldeias',
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
                                ]);
            
            // =================================================================
            // 5. Aplicação dos Filtros
            // =================================================================
            
            // Busca Geral (Nome OU Relato) - Corrigido para usar grupo lógico (OR)
            if ($request->filled('search')) {
                $term = trim($request->search);
                $query->where(function($q) use ($term) {
                    $q->where('nome', 'LIKE', "%{$term}%")
                    ->orWhere('relato', 'LIKE', "%{$term}%");
                });
            }
            
            // Estratégia DEMED (Coluna direta)
            if ($request->filled('estrategiaGeralUtilizadaDemed')) {
                $query->where('estrategiaGeralUtilizadaDemed', $request->estrategiaGeralUtilizadaDemed);
            }
            
            // Classificação / Gravidade (Coluna direta)
            if ($request->filled('classificacaoGravidade')) {
                $query->where('classificacaoGravidadeConflitoDemed', $request->classificacaoGravidade)
                ->orWhere('atualizacaoClassificacaoGravidadeConflito', $request->classificacaoGravidade);
            }
            
            // --- RELACIONAMENTOS ---
            
            // Localidade (UF e Município)
            if ($request->filled('regiao') || $request->filled('uf') || $request->filled('municipio')) {
                $query->whereHas('localidadesConflito', function($q) use ($request) {
                    if ($request->filled('regiao')) {
                        $q->where('regiao', $request->regiao);
                    }
                    if ($request->filled('uf')) {
                        $q->where('uf', $request->uf);
                    }
                    if ($request->filled('municipio')) {
                        $q->where('municipio', $request->municipio);
                    }
                });
            }
            
            // Terra Indígena (Por Nome)
            if ($request->filled('terraIndigena')) {
                $query->whereHas('terrasIndigenas', function($q) use ($request) {
                    $q->where('terra_indigena.idTerraIndigena', $request->terraIndigena);
                });
            }
            
            // Povo (Por ID)
            if ($request->filled('povo')) {
                $query->whereHas('povos', function($q) use ($request) {
                    $q->where('povo.idPovo', $request->povo);
                });
            }
            
            // Categoria de Atores
            if ($request->filled('categoriaAtor')) {
                $query->whereHas('categoriasAtores', function($q) use ($request) {
                    $q->where('categoria_ator.idCategoriaAtor', $request->categoriaAtor);
                });
            }
            
            // Assunto
            if ($request->filled('assunto')) {
                $query->whereHas('assuntos', function($q) use ($request) {
                    $q->where('assunto.idAssunto', $request->assunto);
                });
            }
            
            // Processo SEI (Por número)
            if ($request->filled('processoSei')) {
                $query->whereHas('numerosSeiIdentificacaoConflito', function($q) use ($request) {
                    $q->where('numeroSei', $request->processoSei);
                });
            }

            // Tipos de Impacto (Ambiental, Saúde, Socioeconômico)
            if ($request->filled('impactoAmbiental')) {
                $query->whereHas('impactosAmbientais', function($q) use ($request) {
                    $q->where('impacto_ambiental.idImpactoAmbiental', $request->impactoAmbiental);
                });
            }
            if ($request->filled('impactoSaude')) {
                $query->whereHas('impactosSaude', function($q) use ($request) {
                    $q->where('impacto_saude.idImpactoSaude', $request->impactoSaude);
                });
            }
            if ($request->filled('impactoSocioEconomico')) {
                $query->whereHas('impactosSocioEconomicos', function($q) use ($request) {
                    $q->where('impacto_socio_economico.idImpactoSocioEconomico', $request->impactoSocioEconomico);
                });
            }
            
            // Tipos de Violência
            if ($request->filled('violenciaPatrimonial')) {
                $query->whereHas('violenciasPatrimoniais', function($q) use ($request) {
                    $q->where('tipoViolencia', $request->tipoViolenciaPatrimonial);
                });
            }
            if ($request->filled('violenciaPessoaIndigena')) {
                $query->whereHas('violenciasPessoasIndigenas', function($q) use ($request) {
                    $q->where('tipoViolencia', $request->tipoViolenciaPessoaIndigena);
                });
            }
            if ($request->filled('violenciaPessoaNaoIndigena')) {
                $query->whereHas('violenciasPessoasNaoIndigenas', function($q) use ($request) {
                    $q->where('tipoViolencia', trim($request->tipoViolenciaPessoaNaoIndigena));
                });
            }
            
            // Programa de Proteção
            if ($request->filled('programaProtecao')) {
                if ($request->programaProtecao === 'SEM INFORMAÇÃO') {
                    // Quando vier SEM INFORMAÇÃO, filtrar todos com flagHasMembroProgramaProtecao = 'NÃO'
                    $query->where('flagHasMembroProgramaProtecao', 'NÃO');
                } else {
                    $query->whereHas('programasProtecao', function($q) use ($request) {
                        $q->where('tipoPrograma', $request->programaProtecao);
                    });
                }
            }
            
            // Jurídico / Policial
            // Nota: Assumindo que existem tabelas/relacionamentos. Se forem campos booleanos simples na tabela Conflito, alterar para $query->where('tem_inquerito', true).
            
            if ($request->filled('boletimOcorrencia')) {
                // Exemplo: Filtrar por número do BO em tabela relacionada ou flag
                $query->whereHas('registrosBOouNF', function($q) use ($request) {
                    $q->where('numero', $request->boletimOcorrencia)
                    ->orWhere('numeroSei', $request->boletimOcorrencia);
                });
            }
            
            if ($request->filled('inquerito')) {
                $query->whereHas('inqueritos', function($q) use ($request) {
                    $q->where('numero', $request->inquerito)
                    ->orWhere('numeroSei', $request->inquerito);
                });
            }
            
            if ($request->filled('processoJudicial')) {
                $query->whereHas('processosJudiciais', function($q) use ($request) {
                    $q->where('numero', $request->processoJudicial)
                    ->orWhere('numeroSei', $request->processoJudicial);
                });
            }
            
            if ($request->filled('assistenciaJuridica')) {
                // Assumindo campo booleano na tabela conflito
                $query->where('tipoInstituicaoAssistenciaJuridica', 'LIKE', "%{$request->assistenciaJuridica}%")
                ->orWhere('advogadoInstituicaoAssistenciaJuridica', 'LIKE', "%{$request->assistenciaJuridica}%");
            }

            if ($request->filled('tipoConflito')) {
                $tipoId = $request->tipoConflito;

                $query->whereHas('tiposConflito', function($q) use ($tipoId) {
                    $q->where('tipo_conflito.idTipoConflito', $tipoId);
                });
            }
            
            // Sempre carregar todos os tipos de conflito relacionados
            $query->with('tiposConflito');

            // 6. Ordenação
            $query->orderBy($sortBy, $sortOrder);
            
            // 7. Execução e Retorno
            // Mantendo sua lógica original: Se tiver busca (search), retorna tudo; senão, pagina.
            // Nota: Idealmente a busca textual também deveria ser paginada, mas mantive sua regra de negócio.
            
            if ($request->filled('search')) {
                $conflitos = $query->get();
                $dataResponse = ['data' => $conflitos];
            } else {
                $conflitos = $query->paginate($perPage, ['*'], 'page', $page);
                $dataResponse = $conflitos;
            }

            
            return response()->json([
                'success' => true,
                'data'    => $dataResponse,
                'message' => 'Conflitos recuperados com sucesso.',
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro em ConflitoController@index:', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno ao filtrar conflitos.'
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
            $conflitos = Conflito::with(['tiposConflito'])->get();
                        
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
     *             required={"latitude",
     *                       "longitude",
     *                       "nome",
     *                       "relato",
     *                       "dataInicioConflito",
     *                       "dataAcionamentoMpiConflito",
     *                       "observacoes",
     *                       "flagHasImpactoAmbiental",
     *                       "flagHasImpactoSaude",
     *                       "flagHasImpactoSocioEconomico",
     *                       "flagHasViolenciaIndigena",
     *                       "flagHasMembroProgramaProtecao",
     *                       "flagHasBOouNF",
     *                       "flagHasInquerito",
     *                       "flagHasProcessoJudicial",
     *                       "flagHasAssistenciaJuridica",
     *                       "flagHasRegiaoPrioritaria",
     *                       "flagHasViolenciaPatrimonialIndigena",
     *                       "flagHasEventoViolenciaIndigena",
     *                       "flagHasAssassinatoPrisaoNaoIndigena",
     *                       "tipoInstituicaoAssistenciaJuridica",
     *                       "advogadoInstituicaoAssistenciaJuridica",
     *                       "regiaoPrioritaria",
     *                       "classificacaoGravidadeConflitoDemed",
     *                       "atualizacaoClassificacaoGravidadeConflito",
     *                       "dataReferenciaMudancaClassificacao",
     *                       "estrategiaGeralUtilizadaDemed",
     *                       "estrategiaColetiva",
     *                       "status",
     *                       "assuntosConflito"},
     *             @OA\Property(property="latitude", type="string", example="41.40338"),
     *             @OA\Property(property="longitude", type="string", example="2.17403"),
     *             @OA\Property(property="nome", type="string", example="nome do conflito"),
     *             @OA\Property(property="relato", type="string", example="Relato do conflito"),
     *             @OA\Property(property="dataConflito", type="date", format="yyyy-mm-dd", example="2025-04-13"),
     *             @OA\Property(property="dataAcionamentoMpiConflito", type="date", format="yyyy-mm-dd", example="2025-04-13"),
     *             @OA\Property(property="observacoes", type="string", example="Observações do conflito"),
     *             @OA\Property(property="flagHasImpactoAmbiental", type="string", example="SIM"),
     *             @OA\Property(property="flagHasImpactoSaude", type="string", example="NÃO"),
     *             @OA\Property(property="flagHasImpactoSocioEconomico", type="string", example="SIM"),
     *             @OA\Property(property="flagHasViolenciaIndigena", type="string", example="NÃO"),
     *             @OA\Property(property="flagHasMembroProgramaProtecao", type="string", example="SIM"),
     *             @OA\Property(property="flagHasBOouNF", type="string", example="NÃO"),
     *             @OA\Property(property="flagHasInquerito", type="string", example="SIM"),
     *             @OA\Property(property="flagHasProcessoJudicial", type="string", example="NÃO"),
     *             @OA\Property(property="flagHasAssistenciaJuridica", type="string", example="SIM"),
     *             @OA\Property(property="flagHasRegiaoPrioritaria", type="string", example="NÃO"),
     *             @OA\Property(property="flagHasViolenciaPatrimonialIndigena", type="string", example="SIM"),
     *             @OA\Property(property="flagHasEventoViolenciaIndigena", type="string", example="NÃO"),
     *             @OA\Property(property="flagHasAssassinatoPrisaoNaoIndigena", type="string", example="SIM"),
     *             @OA\Property(property="tipoInstituicaoAssistenciaJuridica", type="string", example="Advogada(o) da União"),
     *             @OA\Property(property="advogadoInstituicaoAssistenciaJuridica", type="string", example="Consultoria Jurídica/MPI"),
     *             @OA\Property(property="regiaoPrioritaria", type="string", example="Sul do Mato Grosso do Sul"),
     *             @OA\Property(property="classificacaoGravidadeConflitoDemed", type="string", example="URGENCIA"),
     *             @OA\Property(property="atualizacaoClassificacaoGravidadeConflito", type="string", example="EMERGÊNCIA"),
     *             @OA\Property(property="dataReferenciaMudancaClassificacao", type="date", format="yyyy-mm-dd", example="2025-04-13"),
     *             @OA\Property(property="estrategiaGeralUtilizadaDemed", type="string", example="Acompanhamento do conflito com ações complexas"),
     *             @OA\Property(property="estrategiaColetiva", type="string", example="Descrição da estratégia coletiva"),
     *             @OA\Property(property="status", type="string", example="EM ANALISE"),
     *             @OA\Property(property="aldeias", type="array", description="Coleção de IDs de aldeias relacionados ao conflito.", 
     *              @OA\Items(type="integer", format="int64"), example={2860, 125, 43}),
     *             @OA\Property(property="assuntos", type="array", description="Coleção de assuntos relacionados ao conflito.",
     *              @OA\Items(type="integer", format="int64"), example={1, 2, 7}),
     *             @OA\Property(property="terras_indigenas", type="array", description="Coleção de Terras Indigenas relacionados ao conflito.",
     *              @OA\Items(type="integer", format="int64"), example={12, 15}),
     *             @OA\Property(property="povos", type="array", description="Coleção de povos relacionados ao conflito.",
     *              @OA\Items(type="integer", format="int64"), example={1, 2, 7}),
     *             @OA\Property(property="tipos_conflito", type="array", description="Coleção de tipos de Conflito relacionados ao conflito.",
     *              @OA\Items(type="integer", format="int64"), example={1, 2}),
     *             @OA\Property(property="categorias_atores", type="array", description="Coleção de tipos de Conflito relacionados ao conflito.",
     *              @OA\Items(type="integer", format="int64"), example={2, 7}),
     *             @OA\Property(property="impactos_ambientais", type="array", description="Coleção de tipos de Conflito relacionados ao conflito.",
     *              @OA\Items(type="integer", format="int64"), example={2, 3}),
     *             @OA\Property(property="impactos_saude", type="array", description="Coleção de tipos de Conflito relacionados ao conflito.",
     *              @OA\Items(type="integer", format="int64"), example={5, 15}),
     *             @OA\Property(property="impactos_socio_economicos", type="array", description="Coleção de tipos de Conflito relacionados ao conflito.",
     *              @OA\Items(type="integer", format="int64"), example={12, 15}),
     *             @OA\Property(property="atores_identificados", type="array", description="Coleção de atores identificados relacionados ao conflito.",
     *              @OA\Items(type="string"), example={"João da Silva", "Maria das Graças"}),
     *             @OA\Property(property="inqueritos", type="array", description="Coleção de inqueritos relacionados ao conflito.",
     *              @OA\Items(
     *                        @OA\Property(property="data",type="date",format="yyyy-mm-dd",description="Data do inquérito"),
     *                        @OA\Property(property="numero",type="string",description="Número do inquérito"),
     *                        @OA\Property(property="numeroSei",type="string",description="Número do documento SEI"),
     *                        @OA\Property(property="orgao",type="string",description="Órgão que registrou o inquérito"),
     *                        @OA\Property(property="tipoOrgao",type="string",description="Tipo do Órgão")
     *              ),
     *              example={
     *                          {"data": "2010-01-01",
     *                          "numero": "1.30.014.000051/2010-78",
     *                          "numeroSei": "48782219",
     *                          "orgao": "Procuradoria da República em Angra dos Reis/RJ",
     *                          "tipoOrgao": "Ministério Público Federal"}
     *                      }),
     *             @OA\Property(property="localidades_conflito", type="array", description="Coleção de localidades relacionadas ao conflito.",
     *              @OA\Items(
     *                        @OA\Property(property="regiao",type="string",description="Região da Localidade"),
     *                        @OA\Property(property="municipio",type="string",description="Municícipio da Localidade"),
     *                        @OA\Property(property="uf",type="string",description="UF da Localidade")
     *              ),
     *              example={
     *                          {"municipio": "Paraty", 
     *                          "regiao": "Sudeste", 
     *                          "uf": "RJ"}
     *                      }),
     *             @OA\Property(property="numeros_sei_identificacao_conflito", type="array", description="Coleção de processos SEI relacionados ao conflito.",
     *              @OA\Items(type="string"), example={"15000.000637/2025-30","15000.002375/2024-67","14021.038584/2025-19"}),
     *             @OA\Property(property="processos_judiciais", type="array", description="Coleção de tipos de Conflito relacionados ao conflito.",
     *              @OA\Items(
     *                        @OA\Property(property="data",type="date",format="yyyy-mm-dd",description="Data do inquérito"),
     *                        @OA\Property(property="numero",type="string",description="Número do inquérito"),
     *                        @OA\Property(property="numeroSei",type="string",description="Número do documento SEI"),
     *                        @OA\Property(property="orgao",type="string",description="Órgão que registrou o inquérito"),
     *                        @OA\Property(property="tipoPoder",type="string",description="Tipo de Poder")
     *              ),
     *              example={
     *                          {"data": "2022-01-01",
     *                          "numero": "1002052-25.2022.4.01.4103",
     *                          "numeroSei": "44379030",
     *                          "orgaoApoio": "Vara Federal Cível e Criminal da Subseção Judicial de Vilhena/RO",
     *                          "tipoPoder": "Justiça Federal"}
     *                      }),
     *             @OA\Property(property="programas_protecao", type="array", description="Coleção de programas de proteção relacionados ao conflito.",
     *              @OA\Items(
     *                        @OA\Property(property="numeroSei",type="string",description="Número do documento SEI"),
     *                        @OA\Property(property="tipoPrograma",type="string",description="Tipo de programa"),
     *                        @OA\Property(property="uf",type="string",description="UF do programa")
     *              ),
     *              example={
     *                          {"numeroSei": "48782219",
     *                          "tipoPrograma": "Programa de Proteção aos Defensores dos Direitos Humanos (PPDDH)",
     *                          "uf": "RJ"}
     *                      }),
     *             @OA\Property(property="registros_b_oou_n_f", type="array", description="Coleção de registros de BO relacionados ao conflito.",
     *              @OA\Items(
     *                        @OA\Property(property="data",type="date",format="yyyy-mm-dd",description="Data do BO"),
     *                        @OA\Property(property="numero",type="string",description="Número do BO"),
     *                        @OA\Property(property="numeroSei",type="string",description="Número do documento SEI"),
     *                        @OA\Property(property="orgao",type="string",description="Órgão que registrou o BO"),
     *                        @OA\Property(property="tipoOrgao",type="string",description="Tipo do Órgão")
     *              ),
     *              example={
     *                          {"data": "2020-09-21",
     *                          "numero": "219/2020",
     *                          "numeroSei": "48781613",
     *                          "orgao": "Delegacia de Polícia Federal de Angra dos Reis/RJ",
     *                          "tipoOrgao": "Polícia Federal"}
     *                      }),
     *             @OA\Property(property="violencias_patrimoniais", type="array", description="Coleção de violências patrimoniais ao conflito.",
     *              @OA\Items(
     *                        @OA\Property(property="data",type="date",format="yyyy-mm-dd",description="Data do incidente de violência"),
     *                        @OA\Property(property="numeroSei",type="string",description="Número do documento SEI"),
     *                        @OA\Property(property="tipoViolencia",type="string",description="Tipo de violência")
     *              ),
     *              example={
     *                          {"data": "2023-01-23",
     *                          "numeroSei": "48782219",
     *                          "tipoViolencia": "Invasão"}
     *                      }),
     *             @OA\Property(property="violencias_pessoas_indigenas", type="array", description="Coleção de violências contra pessoas indigenas relacionados ao conflito.",
     *              @OA\Items(
     *                        @OA\Property(property="data",type="date",format="yyyy-mm-dd",description="Data do incidente de violência"),
     *                        @OA\Property(property="faixaEtaria",type="string",description="Faixa Etaria"),
     *                        @OA\Property(property="genero",type="string",description="Gênero"),
     *                        @OA\Property(property="idade",type="integer",format="int64",description="Idade"),
     *                        @OA\Property(property="instrumentoViolencia",type="string",description="Instrumento de Violencia"),
     *                        @OA\Property(property="nome",type="string",description="Nome"),
     *                        @OA\Property(property="numeroSei",type="string",description="Número do documento SEI"),
     *                        @OA\Property(property="tipoViolencia",type="string",description="Tipo de violência")
     *              ),
     *              example={
     *                          {"data": "2023-01-23",
     *                          "faixaEtaria": "Jovem adulto (18-25 anos)",
     *                          "genero": "Homem",
     *                          "idade": 20,
     *                          "instrumentoViolencia": "Ameaça verbal e/ou escrita",
     *                          "nome": "João Silva",
     *                          "numeroSei": "48782219",
     *                          "tipoViolencia": "Ameaça à integridade pessoal"}
     *                      }),
     *             @OA\Property(property="violencias_pessoas_nao_indigenas", type="array", description="Coleção de violências contra pessoas não indigenas relacionados ao conflito.",
     *              @OA\Items(
     *                        @OA\Property(property="data",type="date",format="yyyy-mm-dd",description="Data do incidente de violência"),
     *                        @OA\Property(property="nome",type="string",description="Número do BO"),
     *                        @OA\Property(property="numeroSei",type="string",description="Número do documento SEI"),
     *                        @OA\Property(property="tipoPessoa",type="string",description="Tipo de Pessoa"),
     *                        @OA\Property(property="tipoViolencia",type="string",description="Tipo de violência")
     *              ),
     *              example={
     *                          {"data": "2021-08-18",
     *                          "nome": "Edvaldo Deoclides de Oliveira",
     *                          "numeroSei": "36607122",
     *                          "tipoPessoa": "Empresário",
     *                          "tipoViolencia": "Assassinato de não indígena"}
     *                      })
     *          )
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
        $terrasIndigenas = $conflito->terrasIndigenas()->get();
        
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
        if ($conflito->terrasIndigenas()->where('terra_indigena_conflito.idTerraIndigena', $idTerraIndigena)->exists()) {
            return response()->json([
                'message' => 'Este Povo já está associado ao conflito'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        // Cria a relação
        $conflito->terrasIndigenas()->attach($idTerraIndigena);
        
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
        $conflito->terrasIndigenas()->detach($idTerraIndigena);
        
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
                'revised_by' => $auth->email
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
     * @OA\Get(
     *     path="/api/conflito/conflitos-por-ator/{nomeAtor}",
     *     tags={"Conflitos"},
     *     security={ {"sanctum": {} } },
     *     summary="Recuperar conflitos por ator",
     *     @OA\Parameter(
     *         name="nomeAtor",
     *         in="path",
     *         required=true,
     *         description="Nome exato do ator identificado",
     *         @OA\Schema(type="string", example="Nome Exato do Ator")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de conflitos encontrados",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Conflito")
     *             ),
     *             @OA\Property(property="message", type="string", example="Conflitos vinculados ao ator recuperados com sucesso.")
     *         )
     *     )
     * )
     */
    public function getConflitosPorAtor($nomeAtor)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        try {
            // Buscar IDs dos conflitos que possuem o ator com nome exato
            $conflitoIds = AtorIdentificadoConflito::where('nome', '=', trim($nomeAtor))
            ->pluck('idConflito')
            ->toArray();
            
            if (empty($conflitoIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum conflito encontrado para o ator especificado'
                ], Response::HTTP_NOT_FOUND);
            }
            
            // Buscar os conflitos apenas com os campos básicos
            $conflitos = Conflito::whereIn('idConflito', $conflitoIds)->get();
            
            return response()->json([
                'success' => true,
                'data' => $conflitos,
                'message' => 'Conflitos vinculados ao ator recuperados com sucesso.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro em getConflitosPorAtorExatoJoin:', [
                'nomeAtor' => $nomeAtor,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar conflitos por ator: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * @OA\Post(
     *     path="/api/conflito/export",
     *     tags={"Conflitos"},
     *     security={ {"sanctum": {} } },
     *     summary="Exportar conflitos para Excel",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="search", type="string", example="Retomada"),
     *             @OA\Property(property="municipio", type="string", example="Retomada"),
     *             @OA\Property(property="terraIndigena", type="integer", format="int64", example="182"),
     *             @OA\Property(property="povo", type="integer", format="int64", example="72"),
     *             @OA\Property(property="tipoConflito", type="integer", format="int64", example="1"),
     *             @OA\Property(property="tipoViolenciaIndigena", type="string", example="Massacre de indígenas"),
     *             @OA\Property(property="estrategiaGeralUtilizadaDemed", type="string", example="Acompanhamento do conflito com ações complexas"),
     *             @OA\Property(property="status", type="string", example="APROVADO"),
     *             @OA\Property(property="created_by", type="string", example="usuario@email.com"),
     *             @OA\Property(property="sort_by", type="string", example="dataInicioConflito"),
     *             @OA\Property(property="sort_order", type="string", example="desc")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Arquivo Excel gerado com sucesso",
     *         @OA\MediaType(
     *             mediaType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao exportar dados"
     *     )
     * )
     */
    public function export(Request $request)
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json(['message' => 'Não autorizado'], 401);
        }
        
        try {
            // 1. LIMPEZA DE BUFFER (Crucial para Excel)
            // Remove qualquer HTML, espaço em branco ou log que já tenha sido gerado
            if (ob_get_length() > 0) {
                ob_end_clean();
            }
            
            $filters = $request->all();
            $fileName = 'conflitos_' . date('Y-m-d_H-i-s') . '.xlsx';
            
            // 2. GERAÇÃO
            return Excel::download(new ConflitosExport($filters), $fileName);
            
        } catch (Throwable $e) {
            // ^^^ MUDANÇA CRÍTICA: 'Throwable' captura Erros Fatais e Exceptions
            
            // Log detalhado para você descobrir o que está quebrando
            Log::error('Falha crítica na exportação Excel:', [
                'erro' => $e->getMessage(),
                'arquivo' => $e->getFile(),
                'linha' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Retorna JSON limpo para o Swagger não mostrar HTML bagunçado
            return response()->json([
                'success' => false,
                'message' => 'Erro interno ao gerar o Excel. Verifique os logs (storage/logs/laravel.log).',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * @OA\Post(
     *     path="/api/conflito/export-dashboard",
     *     tags={"Conflitos"},
     *     summary="Exportar dados do dashboard para Excel",
     *     @OA\Response(
     *         response=200,
     *         description="Arquivo Excel gerado com sucesso",
     *         @OA\MediaType(
     *             mediaType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao exportar dados"
     *     )
     * )
     */
    public function exportDashboard(Request $request)
    {
        try {
            // 1. Query base do dashboard (Carregando os relacionamentos necessários)
            $conflitos = Conflito::with([
                'tiposConflito',
                'localidadesConflito',
                'numerosSeiIdentificacaoConflito'
            ])->get();
            
            // 2. Criação da classe anônima para exportação
            $export = new class($conflitos) implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize {
                
                private $conflitos;
                
                public function __construct($conflitos)
                {
                    $this->conflitos = $conflitos;
                }
                
                public function collection()
                {
                    return $this->conflitos;
                }
                
                public function headings(): array
                {
                    return [
                        'ID',
                        'Nome',
                        'Data Início',
                        'Status',
                        'Classificação Gravidade',
                        'Localidades',
                        'Processos SEI',
                        'Tipos de Conflito',
                        'Latitude',
                        'Longitude',
                        'Data Criação'
                    ];
                }
                
                public function map($conflito): array
                {
                    return [
                        $conflito->idConflito,
                        $conflito->nome,
                        $conflito->dataInicioConflito,
                        $conflito->status,
                        $conflito->classificacaoGravidadeConflitoDemed,
                        
                        // Coluna Localidades
                        $conflito->localidadesConflito->map(function($localidade) {
                            return "({$localidade->regiao}/{$localidade->uf}/{$localidade->municipio})";
                        })->implode('; '),
                        collect($conflito->numerosSeiIdentificacaoConflito)->pluck('numeroSei')->implode('; '),
                        $conflito->tiposConflito->pluck('nome')->implode('; '),
                        $conflito->latitude,
                        $conflito->longitude,
                        $conflito->created_at
                        ];
                }
            };
            
            // 3. Download do arquivo
            return Excel::download(
                $export,
                'dashboard_conflitos_' . date('Y-m-d_H-i-s') . '.xlsx'
                );
            
        } catch (\Exception $e) {
            // Log do erro real
            Log::error('Erro ao exportar dashboard:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            // Retorno JSON em caso de falha
            return response()->json([
                'success' => false,
                'message' => 'Erro ao exportar dados do dashboard: ' . $e->getMessage()
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
