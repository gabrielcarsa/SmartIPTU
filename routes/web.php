<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EmpreendimentoController;
use App\Http\Controllers\QuadraController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\DebitoController;
use App\Http\Controllers\ParcelaController;
use App\Http\Controllers\PrescricaoController;
use App\Http\Controllers\ContaReceberController;
use App\Http\Controllers\ContaPagarController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\TipoDebitoController;
use App\Http\Controllers\DescricaoDebitoController;
use App\Http\Controllers\CategoriaReceberController;
use App\Http\Controllers\CategoriaPagarController;
use App\Http\Controllers\TitularContaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MovimentacaoFinanceiraController;
use App\Http\Controllers\ContaCorrenteController;
use App\Http\Controllers\ScrapingIptuController;
use App\Http\Controllers\ImportarController;
use App\Http\Controllers\CobrancaController;


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

        //DASHBOARD
        Route::get('/dashboard',[DashboardController::class, 'dashboard'])->name('dashboard');

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
        Route::get('/lote/editar/{id}',[LoteController::class, 'editar']);
        Route::post('/lote/alterar/{id}/{usuario}',[LoteController::class, 'alterar']);
        Route::get('/lote/excluir/{id}',[LoteController::class, 'excluir']);
        Route::get('/lote/gestao/{id}',[LoteController::class, 'gestao']);
        Route::get('/lote/nova_venda/{id}',[LoteController::class, 'nova_venda'])->name('nova_venda');
        Route::post('/lote/cadastrar_venda/{id}/{usuario}',[LoteController::class, 'cadastrar_venda']);


        //ROTAS DEBITO
        Route::get('/debito/novo/{lote_id}',[DebitoController::class, 'novo'])->name('debito_novo');
        Route::post('/debito/cadastrar/{usuario}/{lote_id}',[DebitoController::class, 'cadastrar']);
        Route::get('/parcela/reajustar',[DebitoController::class, 'reajustar_view'])->name('parcela_reajustar');
        Route::post('/parcela/definir_reajuste/{usuario}',[DebitoController::class, 'reajustar']);
        Route::get('/parcela/alterar_vencimento',[DebitoController::class, 'alterar_vencimento'])->name('alterar_vencimento');
        Route::post('/parcela/definir_data_vencimento/{usuario}',[DebitoController::class, 'definir_alteracao_data']);
        Route::get('/parcela/baixar_parcela',[DebitoController::class, 'baixar_parcela_view'])->name('baixar_parcela');
        Route::post('/parcela/definir_baixar_parcela/{usuario}',[DebitoController::class, 'definir_baixar_parcela']);
        Route::get('/debito/cadastrar_scraping',[DebitoController::class, 'cadastrar_scraping'])->name('cadastrar_scraping');



        //ROTAS PRESCRIÇÃO
        Route::get('/prescricao/{lote_id}',[PrescricaoController::class, 'prescricao'])->name('prescricao');
        Route::get('/prescricao/novo/{lote_id}',function ($lote_id) {return view('prescricao/prescricao_novo', ['lote_id' => $lote_id]);})->name('prescricao_novo');
        Route::post('/prescricao/cadastrar/{usuario}/{lote_id}',[PrescricaoController::class, 'cadastrar']);
        Route::get('/prescricao/editar/{id}',[PrescricaoController::class, 'editar']);
        Route::post('/prescricao/alterar/{id}/{usuario}',[PrescricaoController::class, 'alterar']);
        Route::get('/prescricao/excluir/{id}',[PrescricaoController::class, 'excluir']);
 
        //ROTAS CONTAS RECEBER
        Route::get('/contas_receber/nova_receita',[ContaReceberController::class, 'conta_receber_novo'])->name('nova_receita');
        Route::post('/contas_receber/cadastrar/{usuario}',[ContaReceberController::class, 'cadastrar']);
        Route::get('/contas_receber',[ContaReceberController::class, 'contas_receber'])->name('contas_receber');//FINANCEIRO
        Route::get('/contas_receber/listar',[ContaReceberController::class, 'contas_receber_listagem']);
        Route::get('/contas_receber/reajustar',[ContaReceberController::class, 'reajustar_view'])->name('receber_reajustar');
        Route::post('/contas_receber/definir_reajuste/{usuario}',[ContaReceberController::class, 'reajustar']);
        Route::get('/contas_receber/alterar_vencimento',[ContaReceberController::class, 'alterar_vencimento'])->name('receber_alterar_vencimento');
        Route::post('/contas_receber/definir_data_vencimento/{usuario}',[ContaReceberController::class, 'definir_alteracao_data']);
        Route::get('/contas_receber/baixar_parcela',[ContaReceberController::class, 'baixar_parcela_view'])->name('receber_baixar_parcela');
        Route::post('/contas_receber/definir_baixar_parcela/{usuario}',[ContaReceberController::class, 'definir_baixar_parcela']);
        Route::get('/contas_receber/estornar_recebimento',[ContaReceberController::class, 'estornar_recebimento_view'])->name('estornar_recebimento');
        Route::post('/contas_receber/estornar_recebimento/{usuario}',[ContaReceberController::class, 'estornar_recebimento']);
        Route::get('/contas_receber/estornar_parcela',[ContaReceberController::class, 'estornar_parcela_view'])->name('estornar_parcela');
        Route::post('/contas_receber/estornar_parcela/{usuario}',[ContaReceberController::class, 'estornar_parcela']);

        
        //ROTAS CONTAS PAGAR
        Route::get('/contas_pagar',[ContaPagarController::class, 'contas_pagar'])->name('contas_pagar');//FINANCEIRO
        Route::get('/contas_pagar/listar',[ContaPagarController::class, 'contas_pagar_listagem']);
        Route::get('/contas_pagar/nova_despesa',[ContaPagarController::class, 'conta_pagar_novo'])->name('nova_despesa');
        Route::post('/contas_pagar/cadastrar/{usuario}',[ContaPagarController::class, 'cadastrar']);
        Route::get('/contas_pagar/reajustar',[ContaPagarController::class, 'reajustar_view'])->name('pagar_reajustar');
        Route::post('/contas_pagar/definir_reajuste/{usuario}',[ContaPagarController::class, 'reajustar']);
        Route::get('/contas_pagar/alterar_vencimento',[ContaPagarController::class, 'alterar_vencimento'])->name('pagar_alterar_vencimento');
        Route::post('/contas_pagar/definir_data_vencimento/{usuario}',[ContaPagarController::class, 'definir_alteracao_data']);
        Route::get('/contas_pagar/baixar_parcela',[ContaPagarController::class, 'baixar_parcela_view'])->name('pagar_baixar_parcela');
        Route::post('/contas_pagar/definir_baixar_parcela/{usuario}',[ContaPagarController::class, 'definir_baixar_parcela']);
        Route::get('/contas_pagar/estornar_pagamento',[ContaPagarController::class, 'estornar_pagamento_view'])->name('estornar_pagamento');
        Route::post('/contas_pagar/estornar_pagamento/{usuario}',[ContaPagarController::class, 'estornar_pagamento']);

        //ROTA CALENDÁRIO
        Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario');

        //ROTAS TIPO DE DÉBITOS
        Route::get('/tipo_debito',[TipoDebitoController::class, 'tipo_debito'])->name('tipo_debito');
        Route::post('/tipo_debito/cadastrar/{usuario}',[TipoDebitoController::class, 'cadastrar']);
        Route::get('/tipo_debito/excluir/{id}',[TipoDebitoController::class, 'excluir']);

        //ROTAS PARA TITULAR DA CONTA
        Route::get('/titular_conta',[TitularContaController::class, 'titular_conta'])->name('titular_conta');
        Route::post('/titular_conta/cadastrar/{usuario}',[TitularContaController::class, 'cadastrar']);
        Route::get('/titular_conta/excluir/{id}',[TitularContaController::class, 'excluir']);

        //ROTAS PARA CONTA CORRENTE
        Route::get('/conta_corrente/{titular_id}',[ContaCorrenteController::class, 'listar']);
        Route::get('/conta_corrente/novo/{titular_id}',[ContaCorrenteController::class, 'novo']);
        Route::post('/conta_corrente/cadastrar/{titular_id}/{usuario}',[ContaCorrenteController::class, 'cadastrar']);


        //ROTAS DESCRIÇÃO DÉBITO
        Route::get('/descricao_debito',[DescricaoDebitoController::class, 'descricao_debito'])->name('descricao_debito');
        Route::post('/descricao_debito/cadastrar/{usuario}',[DescricaoDebitoController::class, 'cadastrar']);
        Route::get('/descricao_debito/excluir/{id}',[DescricaoDebitoController::class, 'excluir']);

        //ROTAS CATEGORIA CONTAS A PAGAR
        Route::get('/categoria_pagar',[CategoriaPagarController::class, 'categoria_pagar'])->name('categoria_pagar');
        Route::post('/categoria_pagar/cadastrar/{usuario}',[CategoriaPagarController::class, 'cadastrar']);
        Route::get('/categoria_pagar/excluir/{id}',[CategoriaPagarController::class, 'excluir']);
        Route::get('/categoria_pagar/json', [CategoriaPagarController::class, 'categoria_pagar_json']);

        //ROTAS CATEGORIA CONTAS A RECEBER
        Route::get('/categoria_receber',[CategoriaReceberController::class, 'categoria_receber'])->name('categoria_receber');
        Route::post('/categoria_receber/cadastrar/{usuario}',[CategoriaReceberController::class, 'cadastrar']);
        Route::get('/categoria_receber/excluir/{id}',[CategoriaReceberController::class, 'excluir']);
        Route::get('/categoria_receber/json', [CategoriaReceberController::class, 'categoria_receber_json']);

        //ROTAS MOVIMENTAÇÃO FINANCEIRA
        Route::get('/movimentacao_financeira',[MovimentacaoFinanceiraController::class, 'movimentacao_financeira'])->name('movimentacao_financeira');
        Route::get('/movimentacao_financeira/listar',[MovimentacaoFinanceiraController::class, 'listar']);
        Route::get('/movimentacao_financeira/novo/',[MovimentacaoFinanceiraController::class, 'novo'])->name('nova_movimentacao');
        Route::get('/movimentacao_financeira/conta_corrente/{titular_conta_id}', [MovimentacaoFinanceiraController::class, 'conta_corrente']);
        Route::post('/movimentacao_financeira/cadastrar/{usuario}',[MovimentacaoFinanceiraController::class, 'cadastrar']);
        Route::get('/movimentacao_financeira/relatorio_pdf',[MovimentacaoFinanceiraController::class, 'relatorio_pdf']);

        //SCRAPING  
        Route::get('/scraping/{inscricao_municipal}/{lote_id}',[ScrapingIptuController::class, 'iptuCampoGrande'])->name('iptuCampoGrande');
        Route::get('/scraping/{inscricao_municipal}/{lote_id}/{user_id}',[ScrapingIptuController::class, 'iptuCampoGrandeAdicionarDireto'])->name('iptuCampoGrandeAdicionarDireto');
        
        //SUBIR PLANILHAS DE DADOS
        Route::post('/importar_lotes/{user_id}/{empreendimento_id}', [ImportarController::class, 'importarLotesCSV'])->name('importarLotesCSV');

        // ROTAS PARA COBRANÇA
        Route::get('/cobranca',[CobrancaController::class, 'gestao_cobranca'])->name('cobranca');

    });