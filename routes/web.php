<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EmpreendimentoController;
use App\Http\Controllers\QuadraController;
use App\Http\Controllers\LoteController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    //ROTAS CLIENTE
    Route::get('/cliente',[ClienteController::class, 'cliente']);
    Route::get('/cliente/listar',[ClienteController::class, 'listar']);
    Route::get('/cliente/novo',[ClienteController::class, 'novo']);
    Route::post('/cliente/cadastrar/{usuario}',[ClienteController::class, 'cadastrar']);
    Route::get('/cliente/editar/{id}',[ClienteController::class, 'editar']);
    Route::post('/cliente/alterar/{id}/{usuario}',[ClienteController::class, 'alterar']);
    Route::get('/cliente/excluir/{id}/',[ClienteController::class, 'excluir']);
    Route::get('/cliente/relatorio_pdf',[ClienteController::class, 'relatorio_pdf']);

    //ROTAS EMPREENDIMETO
    Route::get('/empreendimento',[EmpreendimentoController::class, 'listar']);
    Route::get('/empreendimento/novo',[EmpreendimentoController::class, 'novo']);
    Route::post('/empreendimento/cadastrar/{usuario}',[EmpreendimentoController::class, 'cadastrar']);
    Route::get('/empreendimento/editar/{id}',[EmpreendimentoController::class, 'editar']);
    Route::post('/empreendimento/alterar/{id}/{usuario}',[EmpreendimentoController::class, 'alterar']);
    Route::get('/empreendimento/excluir/{id}',[EmpreendimentoController::class, 'excluir']);
    Route::get('/empreendimento/gestao/{id}',[EmpreendimentoController::class, 'gestao']);

    //ROTAS QUADRA
    Route::get('/quadra/novo/{empreendimento_id}',[QuadraController::class, 'novo']);
    Route::post('/quadra/cadastrar/{usuario}/{empreendimento_id}',[QuadraController::class, 'cadastrar']);
    Route::get('/quadra/excluir/{id}/{empreendimento_id}',[QuadraController::class, 'excluir']);

    //ROTAS LOTE
    Route::get('/lote/novo/{empreendimento_id}',[LoteController::class, 'novo']);
    Route::post('/lote/cadastrar/{usuario}/{empreendimento_id}',[LoteController::class, 'cadastrar']);
});