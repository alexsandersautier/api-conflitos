<?php

use App\Http\Controllers\AssuntoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConflitoController;
use App\Http\Controllers\EpisodioController;
use App\Http\Controllers\ImpactoAmbientalController;
use App\Http\Controllers\ImpactoSaudeController;
use App\Http\Controllers\ImpactoSocioEconomicoController;
use App\Http\Controllers\OrgaoController;
use App\Http\Controllers\PovoController;
use App\Http\Controllers\ProcessoSeiController;
use App\Http\Controllers\SituacaoFundiariaController;
use App\Http\Controllers\TerraIndigenaController;
use App\Http\Controllers\TipoAtorController;
use App\Http\Controllers\TipoConflitoController;
use App\Http\Controllers\TipoInqueritoPolicialController;
use App\Http\Controllers\TipoProcessoSeiController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\InqueritoPolicialController;
use App\Http\Controllers\LiderancaAmeacadaController;
use App\Http\Controllers\AtorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrigemDadoController;
use App\Http\Controllers\TipoResponsavelController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);
});

Route::prefix('assunto')->group(function () {
    Route::get('/',        [AssuntoController::class, 'index']);
    Route::post('/',       [AssuntoController::class, 'store']);
    Route::get('/{id}',    [AssuntoController::class, 'show']);
    Route::put('/{id}',    [AssuntoController::class, 'update']);
    Route::patch('/{id}',  [AssuntoController::class, 'update']);
    Route::delete('/{id}', [AssuntoController::class, 'destroy']);
})->middleware('auth:sanctum');


Route::prefix('ator')->group(function () {
    Route::get('/',        [AtorController::class, 'index']);
    Route::post('/',       [AtorController::class, 'store']);
    Route::get('/{id}',    [AtorController::class, 'show']);
    Route::put('/{id}',    [AtorController::class, 'update']);
    Route::patch('/{id}',  [AtorController::class, 'update']);
    Route::delete('/{id}', [AtorController::class, 'destroy']);
    
    Route::get('/conflito/{idConflito}', [AtorController::class, 'getAllByConflito']);
})->middleware('auth:sanctum');

Route::prefix('conflito')->group(function () {
    Route::get('/',        [ConflitoController::class, 'index']);
    Route::get('/page',    [ConflitoController::class, 'getAllPage']);
    Route::post('/',       [ConflitoController::class, 'store']);
    Route::get('/{id}',    [ConflitoController::class, 'show']);
    Route::put('/{id}',    [ConflitoController::class, 'update']);
    Route::patch('/{id}',  [ConflitoController::class, 'update']);
    Route::delete('/{id}', [ConflitoController::class, 'destroy']);

    
    Route::get('/{id}/terras-indigenas',                                    [ConflitoController::class, 'getTerrasIndigenas']);
    Route::post('/{id}/terra-indigena',                                     [ConflitoController::class, 'attachTerraIndigena']);
    Route::delete('/{idConflito}/terra-indigena/{idTerraIndigena}',         [ConflitoController::class, 'detachTerraIndigena']);
    
    Route::get('/{id}/povos',                                               [ConflitoController::class, 'getPovos']);
    Route::post('/{id}/povo',                                               [ConflitoController::class, 'attachPovo']);
    Route::delete('/{idConflito}/povo/{idPovo}',                            [ConflitoController::class, 'detachPovo']);
    
    Route::get('/{id}/assuntos',                                            [ConflitoController::class, 'getAssuntos']);
    Route::post('/{id}/assunto',                                            [ConflitoController::class, 'attachAssunto']);
    Route::delete('/{idConflito}/assunto/{idAssunto}',                      [ConflitoController::class, 'detachAssunto']);
    
    Route::get('/{id}/tipos-conflito',                                      [ConflitoController::class, 'getTiposConflito']);
    Route::post('/{id}/tipo-conflito',                                      [ConflitoController::class, 'attachTipoConflito']);
    Route::delete('/{idConflito}/tipo-conflito/{idTipoConflito}',           [ConflitoController::class, 'detachTipoConflito']);
        
    Route::get('/{id}/inqueritos-policiais',                                [ConflitoController::class, 'getInqueritosPoliciais']);
    Route::post('/{id}/inquerito-policial',                                 [ConflitoController::class, 'attachInqueritoPolicial']);
    Route::delete('/{idConflito}/inquerito-policial/{idInqueritoPolicial}', [ConflitoController::class, 'detachInqueritoPolicial']);
    
    Route::get('/{id}/impactos-ambientais',                                 [ConflitoController::class, 'getImpactosAmbientais']);
    Route::post('/{id}/impacto-ambiental',                                  [ConflitoController::class, 'attachImpactoAmbiental']);
    Route::delete('/{idConflito}/impacto-ambiental/{idImpactoAmbiental}',   [ConflitoController::class, 'detachImpactoAmbiental']);
    
    Route::get('/{id}/impactos-saude',                                      [ConflitoController::class, 'getImpactosSaude']);
    Route::post('/{id}/impacto-saude',                                      [ConflitoController::class, 'attachImpactoSaude']);
    Route::delete('/{idConflito}/impacto-saude/{idImpactoSaude}',           [ConflitoController::class, 'detachImpactoSaude']);
    
    Route::get('/{id}/impactos-socio-economicos',                           [ConflitoController::class, 'getImpactosSocioEconomicos']);
    Route::post('/{id}/impacto-socio-economico',                            [ConflitoController::class, 'attachImpactoSocioEconomico']);
    Route::delete('/{idConflito}/impacto-socio-economico/{idImpactoSaude}', [ConflitoController::class, 'detachImpactoSocioEconomico']);
})->middleware('auth:sanctum');

Route::prefix('episodio')->group(function () {
    Route::get('/',        [EpisodioController::class, 'index']);
    Route::post('/',       [EpisodioController::class, 'store']);
    Route::get('/{id}',    [EpisodioController::class, 'show']);
    Route::put('/{id}',    [EpisodioController::class, 'update']);
    Route::patch('/{id}',  [EpisodioController::class, 'update']);
    Route::delete('/{id}', [EpisodioController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::prefix('impacto-ambiental')->group(function () {
    Route::get('/',        [ImpactoAmbientalController::class, 'index']);
    Route::post('/',       [ImpactoAmbientalController::class, 'store']);
    Route::get('/{id}',    [ImpactoAmbientalController::class, 'show']);
    Route::put('/{id}',    [ImpactoAmbientalController::class, 'update']);
    Route::patch('/{id}',  [ImpactoAmbientalController::class, 'update']);
    Route::delete('/{id}', [ImpactoAmbientalController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::prefix('impacto-saude')->group(function () {
    Route::get('/',        [ImpactoSaudeController::class, 'index']);
    Route::post('/',       [ImpactoSaudeController::class, 'store']);
    Route::get('/{id}',    [ImpactoSaudeController::class, 'show']);
    Route::put('/{id}',    [ImpactoSaudeController::class, 'update']);
    Route::patch('/{id}',  [ImpactoSaudeController::class, 'update']);
    Route::delete('/{id}', [ImpactoSaudeController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::prefix('impacto-socio-economico')->group(function () {
    Route::get('/',        [ImpactoSocioEconomicoController::class, 'index']);
    Route::post('/',       [ImpactoSocioEconomicoController::class, 'store']);
    Route::get('/{id}',    [ImpactoSocioEconomicoController::class, 'show']);
    Route::put('/{id}',    [ImpactoSocioEconomicoController::class, 'update']);
    Route::patch('/{id}',  [ImpactoSocioEconomicoController::class, 'update']);
    Route::delete('/{id}', [ImpactoSocioEconomicoController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::prefix('inquerito-policial')->group(function () {
    Route::get('/',        [InqueritoPolicialController::class, 'index']);
    Route::post('/',       [InqueritoPolicialController::class, 'store']);
    Route::get('/{id}',    [InqueritoPolicialController::class, 'show']);
    Route::put('/{id}',    [InqueritoPolicialController::class, 'update']);
    Route::patch('/{id}',  [InqueritoPolicialController::class, 'update']);
    Route::delete('/{id}', [InqueritoPolicialController::class, 'destroy']);
    
    Route::get('/conflito/{idConflito}', [InqueritoPolicialController::class, 'getAllByConflito']);
})->middleware('auth:sanctum');

Route::prefix('lideranca-ameacada')->group(function () {
    Route::get('/',        [LiderancaAmeacadaController::class, 'index']);
    Route::post('/',       [LiderancaAmeacadaController::class, 'store']);
    Route::get('/{id}',    [LiderancaAmeacadaController::class, 'show']);
    Route::put('/{id}',    [LiderancaAmeacadaController::class, 'update']);
    Route::patch('/{id}',  [LiderancaAmeacadaController::class, 'update']);
    Route::delete('/{id}', [LiderancaAmeacadaController::class, 'destroy']);
    
    Route::get('/conflito/{idConflito}', [LiderancaAmeacadaController::class, 'getAllByConflito']);
})->middleware('auth:sanctum');
    
Route::prefix('origem-dado')->group(function () {
    Route::get('/',        [OrigemDadoController::class, 'index']);
    Route::post('/',       [OrigemDadoController::class, 'store']);
    Route::get('/{id}',    [OrigemDadoController::class, 'show']);
    Route::put('/{id}',    [OrigemDadoController::class, 'update']);
    Route::delete('/{id}', [OrigemDadoController::class, 'destroy']);
    
    Route::get('/conflito/{idConflito}', [OrigemDadoController::class, 'getAllByConflito']);
})->middleware('auth:sanctum');

Route::prefix('orgao')->group(function () {
    Route::get('/',        [OrgaoController::class, 'index']);
    Route::post('/',       [OrgaoController::class, 'store']);
    Route::get('/{id}',    [OrgaoController::class, 'show']);
    Route::put('/{id}',    [OrgaoController::class, 'update']);
    Route::delete('/{id}', [OrgaoController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::prefix('perfil')->group(function () {
    Route::get('/',        [PerfilController::class, 'index']);
    Route::post('/',       [PerfilController::class, 'store']);
    Route::get('/{id}',    [PerfilController::class, 'show']);
    Route::put('/{id}',    [PerfilController::class, 'update']);
    Route::patch('/{id}',  [PerfilController::class, 'update']);
    Route::delete('/{id}', [PerfilController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::prefix('povo')->group(function () {
    Route::get('/',        [PovoController::class, 'index']);
    Route::post('/',       [PovoController::class, 'store']);
    Route::get('/{id}',    [PovoController::class, 'show']);
    Route::put('/{id}',    [PovoController::class, 'update']);
    Route::patch('/{id}',  [PovoController::class, 'update']);
    Route::delete('/{id}', [PovoController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::prefix('processo-sei')->group(function () {
    Route::get('/',        [ProcessoSeiController::class, 'index']);
    Route::post('/',       [ProcessoSeiController::class, 'store']);
    Route::get('/{id}',    [ProcessoSeiController::class, 'show']);
    Route::put('/{id}',    [ProcessoSeiController::class, 'update']);
    Route::patch('/{id}',  [ProcessoSeiController::class, 'update']);
    Route::delete('/{id}', [ProcessoSeiController::class, 'destroy']);
    
    Route::get('/conflito/{idConflito}', [ProcessoSeiController::class, 'getAllByConflito']);
})->middleware('auth:sanctum');

Route::prefix('situacao-fundiaria')->group(function () {
    Route::get('/',        [SituacaoFundiariaController::class, 'index']);
    Route::post('/',       [SituacaoFundiariaController::class, 'store']);
    Route::get('/{id}',    [SituacaoFundiariaController::class, 'show']);
    Route::put('/{id}',    [SituacaoFundiariaController::class, 'update']);
    Route::patch('/{id}',  [SituacaoFundiariaController::class, 'update']);
    Route::delete('/{id}', [SituacaoFundiariaController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::prefix('terra-indigena')->group(function () {
    Route::get('/',        [TerraIndigenaController::class, 'index']);
    Route::post('/',       [TerraIndigenaController::class, 'store']);
    Route::get('/{id}',    [TerraIndigenaController::class, 'show']);
    Route::put('/{id}',    [TerraIndigenaController::class, 'update']);
    Route::patch('/{id}',  [TerraIndigenaController::class, 'update']);
    Route::delete('/{id}', [TerraIndigenaController::class, 'destroy']);
    
    Route::get('/page',        [TerraIndigenaController::class, 'getAllPage']);
})->middleware('auth:sanctum');

Route::prefix('tipo-ator')->group(function () {
    Route::get('/',        [TipoAtorController::class, 'index']);
    Route::post('/',       [TipoAtorController::class, 'store']);
    Route::get('/{id}',    [TipoAtorController::class, 'show']);
    Route::put('/{id}',    [TipoAtorController::class, 'update']);
    Route::patch('/{id}',  [TipoAtorController::class, 'update']);
    Route::delete('/{id}', [TipoAtorController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::prefix('tipo-conflito')->group(function () {
    Route::get('/',        [TipoConflitoController::class, 'index']);
    Route::post('/',       [TipoConflitoController::class, 'store']);
    Route::get('/{id}',    [TipoConflitoController::class, 'show']);
    Route::put('/{id}',    [TipoConflitoController::class, 'update']);
    Route::patch('/{id}',  [TipoConflitoController::class, 'update']);
    Route::delete('/{id}', [TipoConflitoController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::prefix('tipo-inquerito-policial')->group(function () {
    Route::get('/',        [TipoInqueritoPolicialController::class, 'index']);
    Route::post('/',       [TipoInqueritoPolicialController::class, 'store']);
    Route::get('/{id}',    [TipoInqueritoPolicialController::class, 'show']);
    Route::put('/{id}',    [TipoInqueritoPolicialController::class, 'update']);
    Route::patch('/{id}',  [TipoInqueritoPolicialController::class, 'update']);
    Route::delete('/{id}', [TipoInqueritoPolicialController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::prefix('tipo-processo-sei')->group(function () {
    Route::get('/',        [TipoProcessoSeiController::class, 'index']);
    Route::post('/',       [TipoProcessoSeiController::class, 'store']);
    Route::get('/{id}',    [TipoProcessoSeiController::class, 'show']);
    Route::put('/{id}',    [TipoProcessoSeiController::class, 'update']);
    Route::patch('/{id}',  [TipoProcessoSeiController::class, 'update']);
    Route::delete('/{id}', [TipoProcessoSeiController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::prefix('tipo-responsavel')->group(function () {
    Route::get('/',        [TipoResponsavelController::class, 'index']);
    Route::post('/',       [TipoResponsavelController::class, 'store']);
    Route::get('/{id}',    [TipoResponsavelController::class, 'show']);
    Route::put('/{id}',    [TipoResponsavelController::class, 'update']);
    Route::patch('/{id}',  [TipoResponsavelController::class, 'update']);
    Route::delete('/{id}', [TipoResponsavelController::class, 'destroy']);
})->middleware('auth:sanctum');

Route::prefix('usuario')->group(function () {
    Route::get('/',        [UsuarioController::class, 'index']);
    Route::post('/',       [UsuarioController::class, 'store']);
    Route::get('/{id}',    [UsuarioController::class, 'show']);
    Route::put('/{id}',    [UsuarioController::class, 'update']);
    Route::delete('/{id}', [UsuarioController::class, 'destroy']);
    
    Route::patch('/alterar-senha',        [UsuarioController::class, 'alterarSenha']);
    Route::get('/pesquisar/buscar-texto', [UsuarioController::class, 'getAllByTexto']);
})->middleware('auth:sanctum');

