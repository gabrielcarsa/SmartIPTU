<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;

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
});