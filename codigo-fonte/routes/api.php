<?php
use App\Http\Controllers\AssuntoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConflitoController;
use App\Http\Controllers\ImpactoAmbientalController;
use App\Http\Controllers\ImpactoSaudeController;
use App\Http\Controllers\ImpactoSocioEconomicoController;
use App\Http\Controllers\OrgaoController;
use App\Http\Controllers\PovoController;
use App\Http\Controllers\SituacaoFundiariaController;
use App\Http\Controllers\TerraIndigenaController;
use App\Http\Controllers\CategoriaAtorController;
use App\Http\Controllers\TipoConflitoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\AtorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TipoResponsavelController;
use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\AldeiaController;
use App\Http\Controllers\DashboardController;

Route::get('/healthcheck', HealthCheckController::class);
Route::post('/login', [
    AuthController::class,
    'login'
]);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

Route::prefix('dashboard')->group(function () {
    // Dados completos do dashboard
    Route::get('/dados', [DashboardController::class, 'getDadosDashboard']);

    // Dados do dashboard com filtro por período
    Route::get('/dados-filtrados', [DashboardController::class, 'getDadosDashboardComFiltro']);

    // Dados específicos com filtros avançados
    Route::get('/dados-avancados', [DashboardController::class, 'getDadosFiltradosAvancados']);

    // Métricas em tempo real (sem cache)
    Route::get('/metricas-tempo-real', [DashboardController::class, 'getMetricasTempoReal']);

    // Endpoints individuais
    Route::get('/totais-gerais', [DashboardController::class, 'getTotaisGerais']);
    Route::get('/distribuicao-geografica', [DashboardController::class, 'getDistribuicaoGeografica']);
    Route::get('/conflitos-por-uf', [DashboardController::class, 'getConflitosPorUF']);
    Route::get('/conflitos-por-regiao', [DashboardController::class, 'getConflitosPorRegiao']);
    Route::get('/conflitos-por-municipio', [DashboardController::class, 'getConflitosPorMunicipio']);
    Route::get('/conflitos-por-ano', [DashboardController::class, 'getConflitosPorAno']);
    Route::get('/estatisticas-violencias', [DashboardController::class, 'getEstatisticasViolencias']);

    // Administração
    Route::get('/limpar-cache', [DashboardController::class, 'clearCache']);
    Route::get('/health-check', [DashboardController::class, 'healthCheck']);
});

// Rotas adicionais
Route::prefix('aldeia')->group(function () {
    Route::get('/', [AldeiaController::class, 'index']);
    Route::get('/paginadas', [AldeiaController::class, 'getAldeiasPaginated']);
    Route::post('/', [AldeiaController::class, 'store']);
    Route::get('/{id}', [AldeiaController::class, 'show']);
    Route::put('/{id}', [AldeiaController::class, 'update']);
    Route::patch('/{id}', [AldeiaController::class, 'update']);
    Route::get('/pesquisar/buscar-texto', [AldeiaController::class, 'getAllByTexto']);
    Route::delete('/{id}', [AldeiaController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::prefix('assunto')->group(function () {
    Route::get('/', [AssuntoController::class, 'index']);
    Route::post('/', [AssuntoController::class, 'store']);
    Route::get('/{id}', [AssuntoController::class, 'show']);
    Route::put('/{id}', [AssuntoController::class, 'update']);
    Route::patch('/{id}', [AssuntoController::class, 'update']);
    Route::get('/pesquisar/buscar-texto', [AssuntoController::class, 'getAllByTexto']);
    Route::delete('/{id}', [AssuntoController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::prefix('ator')->group(function () {
    Route::get('/', [AtorController::class, 'index']);
    Route::post('/', [AtorController::class, 'store']);
    Route::get('/{id}', [AtorController::class, 'show']);
    Route::put('/{id}', [AtorController::class, 'update']);
    Route::patch('/{id}', [AtorController::class, 'update']);
    Route::delete('/{id}', [AtorController::class, 'destroy']);
    Route::get('/pesquisar/buscar-texto', [AtorController::class, 'getAllByTexto']);
    Route::get('/conflito/{idConflito}', [AtorController::class, 'getAllByConflito']);
})->middleware('auth:sanctum');

Route::get('/conflito/dashboard', [ConflitoController::class,'getAllDashboard']);
Route::post('/conflito/export-dashboard', [ConflitoController::class,'exportDashboard'])->name('conflito.export-dashboard');

Route::prefix('conflito')->group(function () {
    Route::post('/', [ConflitoController::class, 'store']);
    Route::get('/', [ConflitoController::class, 'index']);
    Route::get('/{id}', [ConflitoController::class, 'show']);
    Route::put('/{id}', [ConflitoController::class, 'update']);
    Route::patch('/{id}', [ConflitoController::class, 'update']);
    Route::delete('/{id}', [ConflitoController::class, 'destroy']);

    Route::get('/{id}/assuntos', [ConflitoController::class, 'getAssuntos']);
    Route::post('/{id}/assunto', [ConflitoController::class, 'attachAssunto']);
    Route::delete('/{idConflito}/assunto/{idAssunto}', [ConflitoController::class, 'detachAssunto']);

    Route::get('/{id}/impactos-ambientais', [ConflitoController::class, 'getImpactosAmbientais']);
    Route::post('/{id}/impacto-ambiental', [ConflitoController::class, 'attachImpactoAmbiental']);
    Route::delete('/{idConflito}/impacto-ambiental/{idImpactoAmbiental}', [ConflitoController::class, 'detachImpactoAmbiental']);

    Route::get('/{id}/impactos-saude', [ConflitoController::class, 'getImpactosSaude']);
    Route::post('/{id}/impacto-saude', [ConflitoController::class, 'attachImpactoSaude']);
    Route::delete('/{idConflito}/impacto-saude/{idImpactoSaude}', [ConflitoController::class, 'detachImpactoSaude']);

    Route::get('/{id}/impactos-socio-economicos', [ConflitoController::class, 'getImpactosSocioEconomicos']);
    Route::post('/{id}/impacto-socio-economico', [ConflitoController::class, 'attachImpactoSocioEconomico']);
    Route::delete('/{idConflito}/impacto-socio-economico/{idImpactoSaude}', [ConflitoController::class, 'detachImpactoSocioEconomico']);

    Route::get('/{id}/localidades', [ConflitoController::class,'getLocalidades']);
    Route::post('/{id}/localidade', [ConflitoController::class,'attachLocalidade']);
    Route::delete('/{idConflito}/localidade/{idLocalidade}', [ConflitoController::class,'detachLocalidade']);

    Route::get('/{id}/terras-indigenas', [ConflitoController::class,'getTerrasIndigenas']);
    Route::post('/{id}/terra-indigena', [ConflitoController::class,'attachTerraIndigena']);
    Route::delete('/{idConflito}/terra-indigena/{idTerraIndigena}', [ConflitoController::class,'detachTerraIndigena']);

    Route::get('/{id}/povos', [ConflitoController::class,'getPovos']);
    Route::post('/{id}/povo', [ConflitoController::class,'attachPovo']);
    Route::delete('/{idConflito}/povo/{idPovo}', [ConflitoController::class,'detachPovo']);

    Route::get('/{id}/tipos-conflito', [ConflitoController::class,'getTiposConflito']);
    Route::post('/{id}/tipo-conflito', [ConflitoController::class,'attachTipoConflito']);
    Route::delete('/{idConflito}/tipo-conflito/{idTipoConflito}', [ConflitoController::class,'detachTipoConflito']);

    Route::get('/por-status/{status}', [ConflitoController::class,'getConflitosPorStatus']);
    Route::get('/por-status-usuario/{status}/{email}', [ConflitoController::class,'getConflitosPorStatusEUsuario']);

    Route::patch('/{id}/set-analise', [ConflitoController::class,'setAnalise']);
    Route::patch('/{id}/set-aprovado', [ConflitoController::class,'setAprovado']);
    Route::patch('/{id}/set-devolvido', [ConflitoController::class,'setDevolvido']);

    Route::get('/conflitos-por-ator/{nomeAtor}', [ConflitoController::class, 'getConflitosPorAtor']);
    Route::post('/export', [ConflitoController::class, 'export'])->name('conflito.export');
})->middleware('auth:sanctum');

Route::prefix('impacto-ambiental')->group(function () {
    Route::get('/', [ImpactoAmbientalController::class, 'index']);
    Route::post('/', [ImpactoAmbientalController::class, 'store']);
    Route::get('/{id}', [ImpactoAmbientalController::class, 'show']);
    Route::put('/{id}', [ImpactoAmbientalController::class, 'update']);
    Route::patch('/{id}', [ImpactoAmbientalController::class, 'update']);
    Route::get('/pesquisar/buscar-texto', [ImpactoAmbientalController::class, 'getAllByTexto']);
    Route::delete('/{id}', [ImpactoAmbientalController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::prefix('impacto-saude')->group(function () {
    Route::get('/', [ImpactoSaudeController::class, 'index']);
    Route::post('/', [ImpactoSaudeController::class, 'store']);
    Route::get('/{id}', [ImpactoSaudeController::class, 'show']);
    Route::put('/{id}', [ImpactoSaudeController::class, 'update']);
    Route::patch('/{id}', [ImpactoSaudeController::class, 'update']);
    Route::get('/pesquisar/buscar-texto', [ImpactoSaudeController::class, 'getAllByTexto']);
    Route::delete('/{id}', [ImpactoSaudeController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::prefix('impacto-socio-economico')->group(function () {
    Route::get('/', [ImpactoSocioEconomicoController::class, 'index']);
    Route::post('/', [ImpactoSocioEconomicoController::class, 'store']);
    Route::get('/{id}', [ImpactoSocioEconomicoController::class, 'show']);
    Route::put('/{id}', [ImpactoSocioEconomicoController::class, 'update']);
    Route::patch('/{id}', [ImpactoSocioEconomicoController::class, 'update']);
    Route::get('/pesquisar/buscar-texto', [ImpactoSocioEconomicoController::class, 'getAllByTexto']);
    Route::delete('/{id}', [ImpactoSocioEconomicoController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::prefix('orgao')->group(function () {
    Route::get('/',                       [OrgaoController::class, 'index']);
    Route::post('/',                      [OrgaoController::class, 'store']);
    Route::get('/{id}',                   [OrgaoController::class, 'show']);
    Route::put('/{id}',                   [OrgaoController::class, 'update']);
    Route::get('/pesquisar/buscar-texto', [OrgaoController::class, 'getAllByTexto']);
    Route::delete('/{id}',                [OrgaoController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::prefix('perfil')->group(function () {
    Route::get('/', [PerfilController::class, 'index']);
    Route::post('/', [PerfilController::class, 'store']);
    Route::get('/{id}', [PerfilController::class, 'show']);
    Route::put('/{id}', [PerfilController::class, 'update']);
    Route::patch('/{id}', [PerfilController::class, 'update']);
    Route::get('/pesquisar/buscar-texto', [PerfilController::class, 'getAllByTexto']);
    Route::delete('/{id}', [PerfilController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::prefix('povo')->group(function () {
    Route::get('/', [PovoController::class, 'index']);
    Route::post('/', [PovoController::class, 'store']);
    Route::get('/{id}', [PovoController::class, 'show']);
    Route::put('/{id}', [PovoController::class, 'update']);
    Route::patch('/{id}', [PovoController::class, 'update']);
    Route::get('/pesquisar/buscar-texto', [PovoController::class, 'getAllByTexto']);
    Route::delete('/{id}', [PovoController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::prefix('situacao-fundiaria')->group(function () {
    Route::get('/', [SituacaoFundiariaController::class, 'index']);
    Route::post('/', [SituacaoFundiariaController::class, 'store']);
    Route::get('/{id}', [SituacaoFundiariaController::class, 'show']);
    Route::put('/{id}', [SituacaoFundiariaController::class, 'update']);
    Route::patch('/{id}', [SituacaoFundiariaController::class, 'update']);
    Route::get('/pesquisar/buscar-texto', [SituacaoFundiariaController::class, 'getAllByTexto']);
    Route::delete('/{id}', [SituacaoFundiariaController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::get('/terra-indigena/paginar', [
    TerraIndigenaController::class,
    'getAllPage'
])->middleware('auth:sanctum');

Route::prefix('terra-indigena')->group(function () {
    Route::get('/', [TerraIndigenaController::class, 'index']);
    Route::post('/', [TerraIndigenaController::class, 'store']);
    Route::get('/{id}', [TerraIndigenaController::class, 'show']);
    Route::put('/{id}', [TerraIndigenaController::class, 'update']);
    Route::patch('/{id}', [TerraIndigenaController::class, 'update']);
    Route::get('/pesquisar/buscar-texto', [TerraIndigenaController::class, 'getAllByTexto']);
    Route::delete('/{id}', [TerraIndigenaController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::prefix('categoria-ator')->group(function () {
    Route::get('/', [CategoriaAtorController::class, 'index']);
    Route::post('/', [CategoriaAtorController::class, 'store']);
    Route::get('/{id}', [CategoriaAtorController::class, 'show']);
    Route::put('/{id}', [CategoriaAtorController::class, 'update']);
    Route::patch('/{id}', [CategoriaAtorController::class, 'update']);
    Route::get('/pesquisar/buscar-texto', [TerraIndigenaController::class, 'getAllByTexto']);
    Route::delete('/{id}', [CategoriaAtorController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::prefix('tipo-conflito')->group(function () {
    Route::get('/', [TipoConflitoController::class, 'index']);
    Route::post('/', [TipoConflitoController::class, 'store']);
    Route::get('/{id}', [TipoConflitoController::class, 'show']);
    Route::put('/{id}', [TipoConflitoController::class, 'update']);
    Route::patch('/{id}', [TipoConflitoController::class, 'update']);
    Route::get('/pesquisar/buscar-texto', [TipoConflitoController::class, 'getAllByTexto']);
    Route::delete('/{id}', [TipoConflitoController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::prefix('tipo-responsavel')->group(function () {
    Route::get('/', [TipoResponsavelController::class, 'index']);
    Route::post('/', [TipoResponsavelController::class, 'store']);
    Route::get('/{id}', [TipoResponsavelController::class, 'show']);
    Route::put('/{id}', [TipoResponsavelController::class, 'update']);
    Route::patch('/{id}', [TipoResponsavelController::class, 'update']);
    Route::get('/pesquisar/buscar-texto', [TipoResponsavelController::class, 'getAllByTexto']);
    Route::delete('/{id}', [TipoResponsavelController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::prefix('usuario')->group(function () {
    Route::get('/', [UsuarioController::class, 'index']);
    Route::post('/', [UsuarioController::class, 'store']);
    Route::get('/{id}', [UsuarioController::class, 'show']);
    Route::put('/{id}', [UsuarioController::class, 'update']);
    Route::delete('/{id}', [UsuarioController::class, 'destroy']);
    Route::patch('/alterar-senha', [UsuarioController::class, 'alterarSenha']);
    Route::get('/pesquisar/buscar-texto', [UsuarioController::class, 'getAllByTexto']);
    Route::delete('/{id}', [UsuarioController::class, 'destroy']);
})->middleware('auth:sanctum');

