<?php

namespace App\Http\Controllers;
use App\Models\MovimentacaoFinanceira;
use App\Models\SaldoDiario;
use App\Models\Cliente;
use App\Models\ContaCorrente;
use App\Models\ContaPagar;
use App\Models\ContaReceber;
use App\Models\TitularConta;
use App\Models\ParcelaContaPagar;
use App\Models\ParcelaContaReceber;
use App\Http\Requests\MovimentacaoFinanceiraRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MovimentacaoFinanceiraController extends Controller
{
    function movimentacao_financeira(){
        $hoje = now()->toDateString(); // Obtém a data de hoje no formato 'YYYY-MM-DD'

        //Soma das entradas do dia atual
        $entradas = DB::table('movimentacao_financeira')
            ->whereDate('data_movimentacao', $hoje)
            ->where('tipo_movimentacao', 0)
            ->sum('valor');

        //Soma das saidas do dia atual
        $saidas = DB::table('movimentacao_financeira')
            ->whereDate('data_movimentacao', $hoje)
            ->where('tipo_movimentacao', 1)
            ->sum('valor');

        //Selecionar Titulares de Conta
        $titulares_conta = DB::table('titular_conta as tc')
        ->select(
            'tc.*',
            'c.nome as nome',
            'c.razao_social as razao_social'
        )
        ->join('cliente as c', 'tc.cliente_id', '=', 'c.id')
        ->get();

        $data = [
            'titulares_conta' => $titulares_conta,
            'entradas' => $entradas,
            'saidas' => $saidas
        ];

        return view('movimentacao_financeira/movimentacao_financeira', compact('data'));
    }

     // LISTAGEM DE MOVIMENTAÇÃO FINANCEIRA
     function listar(Request $request){
        //Validação
        $validated = $request->validate([
            'data' => 'required|date',
            'titulares_conta' => 'required|numeric|min:1',
            'conta_corrente' => 'required|numeric|min:1',
        ]);
    

        $hoje = now()->toDateString(); // Obtém a data de hoje no formato 'YYYY-MM-DD'


        //Soma das entradas do dia atual
        $entradas = DB::table('movimentacao_financeira')
            ->whereDate('data_movimentacao', $hoje)
            ->where('tipo_movimentacao', 0)
            ->sum('valor');

        //Soma das saidas do dia atual
        $saidas = DB::table('movimentacao_financeira')
            ->whereDate('data_movimentacao', $hoje)
            ->where('tipo_movimentacao', 1)
            ->sum('valor');

        $movimentacao = MovimentacaoFinanceira::all();    
        
        $titular = $request->input('titulares_conta');
        $conta_corrente = $request->input('conta_corrente');
        $dataRef = $request->input('data');
        $dataFim = $request->input('data_fim');

        // Saldo anterior
        $saldo_anterior = SaldoDiario::orderBy('data', 'desc')
        ->where('data', '<', $dataRef)
        ->where('titular_conta_id', '=', $titular)
        ->where('conta_corrente_id', '=', $conta_corrente)
        ->get(); 

        $saldo_atual = SaldoDiario::where('data', $dataRef)
        ->where('titular_conta_id', '=', $titular)
        ->where('conta_corrente_id', '=', $conta_corrente)
        ->get(); // Saldo do dia

        $total_movimentacao = $movimentacao->count();

        $query = DB::table('movimentacao_financeira as mf')
        ->select(
            'mf.*',
            'cr.descricao as categoria_receber',
            'cp.descricao as categoria_pagar',
            'td.descricao as tipo_debito',
            'c.nome as nome',
            'c.tipo_cadastro as tipo_cadastro',
            'c.razao_social as razao_social',
            'pr.id as id_parcela_receber', 
            'pr.id as id_parcela_receber', 
            'pr.debito_id as parcela_receber_debito', 
            'pg.id as id_parcela_pagar',
            'pg.debito_id as parcela_pagar_debito', 
            'tc.id as titular_conta_id',
            'c2.nome as nome_titular',
            'c2.razao_social as razao_social_titular'
        )
        ->leftjoin('categoria_receber as cr', 'mf.categoria_receber_id', '=', 'cr.id')
        ->leftjoin('categoria_pagar as cp', 'mf.categoria_pagar_id', '=', 'cp.id')
        ->leftjoin('tipo_debito as td', 'mf.tipo_debito_id', '=', 'td.id')
        ->join('cliente as c', 'mf.cliente_fornecedor_id', '=', 'c.id')
        ->leftjoin('parcela_conta_receber as pr', 'pr.movimentacao_financeira_id', '=', 'mf.id')
        ->leftjoin('parcela_conta_pagar as pg', 'pg.movimentacao_financeira_id',  '=', 'mf.id')
        ->join('titular_conta as tc', 'mf.titular_conta_id', '=', 'tc.id')
        ->join('cliente as c2', 'tc.cliente_id', '=', 'c2.id');

        // Filtro
        if (!empty($dataRef) && !empty($conta_corrente) && !empty($titular) && empty($dataFim)) {
            $query->where('data_movimentacao', '=', $dataRef)
            ->where('mf.titular_conta_id', '=', $titular)
            ->where('mf.conta_corrente_id', '=', $conta_corrente)
            ->orderBy('id');
        } else if (!empty($dataRef) && !empty($conta_corrente) && !empty($titular) && !empty($dataFim)) {
            $query->where('data_movimentacao', '>=', $dataRef)
            ->where('data_movimentacao', '<=', $dataFim)
            ->where('mf.titular_conta_id', '=', $titular)
            ->where('mf.conta_corrente_id', '=', $conta_corrente)
            ->orderBy('id');
        }

        // Execute a consulta e obtenha os resultados
        $movimentacao = $query->get();

        //Selecionar Titulares de Conta
        $titulares_conta = DB::table('titular_conta as tc')
        ->select(
            'tc.*',
            'c.nome as nome',
            'c.razao_social as razao_social'
        )
        ->join('cliente as c', 'tc.cliente_id', '=', 'c.id')
        ->get();

        $data = [
            'titulares_conta' => $titulares_conta,
            'saldo_anterior' => $saldo_anterior,
            'saldo_atual' => $saldo_atual,
            'total_movimentacao' => $total_movimentacao,
            'entradas' => $entradas,
            'saidas' => $saidas
        ];

        return view('movimentacao_financeira/movimentacao_financeira', compact('movimentacao', 'data'));
    }

     //RETORNA VIEW PARA CADASTRO DE NOVA MOVIMENTAÇÃO
     function novo(){
        $titular_conta = DB::table('titular_conta as t')
        ->select(
            't.id as id_titular_conta',
            't.cliente_id as cliente_id',
            'c.nome as nome',
            'c.razao_social as razao_social',
        )
        ->leftJoin('cliente AS c', 'c.id', '=', 't.cliente_id')
        ->get();

        $clientes = Cliente::all();


        $data = [
            'titular_conta' => $titular_conta,
            'clientes' => $clientes,
        ];
        return view('movimentacao_financeira/movimentacao_financeira_novo', compact('data'));
    }

    //RETORNA UM JSON COM A CONTA CORRENTE ESPECÍFICA
    function conta_corrente($titular_conta_id){
        $conta_corrente = ContaCorrente::where('titular_conta_id',$titular_conta_id)->get();
        return response()->json($conta_corrente);
    }

    //CADASTRAR MOVIMENTAÇÃO
    function cadastrar($usuario, MovimentacaoFinanceiraRequest $request){
        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');    
        
        // Salve as movimentações
        foreach ($request->input('movimentacoes') as $movimentacaoData) {
         
            $request->merge([
                'valor' => str_replace(['.', ','], ['', '.'], $movimentacaoData['valor']),
            ]);

            //Validar
            $validated = $request->validate([
                "movimentacoes.*.tipo_movimentacao" => 'required|numeric|min:1',
                "movimentacoes.*.categoria_id" => 'required|numeric|min:1',
                "movimentacoes.*.cliente_fornecedor_id" => 'required|numeric|min:1',
                "movimentacoes.*.valor" => 'required|min:0.1',
                "movimentacoes.*.descricao" => 'nullable|string|max:255',
            ]);

            $movimentacao_financeira = new MovimentacaoFinanceira();
            $movimentacao_financeira->cliente_fornecedor_id = $movimentacaoData['cliente_fornecedor_id'];
            $movimentacao_financeira->descricao = $movimentacaoData['descricao'];
            $movimentacao_financeira->data_movimentacao = $request->input('data');
            $movimentacao_financeira->titular_conta_id = $request->input('titular_conta_id');
            $movimentacao_financeira->conta_corrente_id = $request->input('conta_corrente_id');
            
            // No Banco de Dados o 'tipo_movimentacao' é boolean = False (Entrada 0) e True(Saida 1)
            // Porém no input 0 (Selecione), 1 (Entrada) e 2 (Saída)
            $movimentacao_financeira->tipo_movimentacao = ($movimentacaoData['tipo_movimentacao'] == 1) ? 0 : 1;
    
            $valor = floatval(str_replace(',', '.', str_replace('.', '', $movimentacaoData['valor'])));
            $movimentacao_financeira->valor = $valor; // Converter a string diretamente para um número em ponto flutuante
            $valor_movimentacao = $valor; //Armazenar em uma variavel o valor da movimentação
            
            $movimentacao_financeira->data_cadastro = Carbon::now()->format('Y-m-d H:i:s');
            $movimentacao_financeira->cadastrado_usuario_id = $usuario;
    
            //Variavel de saldo para manipulacao e verificacao do saldo
            $saldo = SaldoDiario::where('data', $request->input('data'))
            ->where('titular_conta_id', $request->input('titular_conta_id'))
            ->where('conta_corrente_id', $request->input('conta_corrente_id'))
            ->get(); // Saldo do dia
    
            //Se não houver saldo para aquele dia
            if(!isset($saldo[0]->saldo)){
                //Último saldo cadastrado
                $ultimo_saldo = SaldoDiario::orderBy('data', 'desc')
                ->where('data', '<', $request->input('data'))
                ->where('titular_conta_id', $request->input('titular_conta_id'))
                ->where('conta_corrente_id', $request->input('conta_corrente_id'))
                ->first();
                
                //Cadastrar saldo daquela data com o último saldo para depois fazer a movimentação
                $addSaldo = new SaldoDiario();
                //Se saldo for null
                if($ultimo_saldo == null){
                    $addSaldo->saldo = 0;
                }else{
                    $addSaldo->saldo = $ultimo_saldo->saldo;
                }
                $addSaldo->titular_conta_id = $request->input('titular_conta_id');
                $addSaldo->conta_corrente_id = $request->input('conta_corrente_id');
                $addSaldo->data = $request->input('data');
                $addSaldo->data_cadastro = Carbon::now()->format('Y-m-d H:i:s');
                $addSaldo->save();
    
                $saldo = $addSaldo;
                $valor_desatualizado_saldo =  $saldo->saldo; //Armazenar o ultimo saldo
    
            }else{//Caso houver saldo para aquele dia
                $valor_desatualizado_saldo =  $saldo[0]->saldo; //Armazenar o ultimo saldo
            }
    
            //variavel que será responsavel por alterar-lo
            $saldo_model = SaldoDiario::where('data', $request->input('data'))
            ->where('titular_conta_id', $request->input('titular_conta_id'))
            ->where('conta_corrente_id', $request->input('conta_corrente_id'))
            ->first();
    
            //Verificar se a movimentação é de entrada ou saída
            $tipo_movimentacao = $movimentacaoData['tipo_movimentacao'];
    
            if($tipo_movimentacao == 1){ // ENTRADA
    
                //Adicionando categoria
                $movimentacao_financeira->categoria_receber_id = $movimentacaoData['categoria_id'];
    
                //Atualizando o saldo
                $saldo_model->saldo = $valor_desatualizado_saldo + $valor_movimentacao; 
                $saldo_model->save();
    
                //Atualizar no Contas a Receber
                $contaReceber = new ContaReceber();
                $contaReceber->titular_conta_id = $request->input('titular_conta_id');
                $contaReceber->cliente_id = $movimentacaoData['cliente_fornecedor_id'];
                $contaReceber->categoria_receber_id = $movimentacaoData['categoria_id'];
                $contaReceber->quantidade_parcela = 1;
                $contaReceber->data_vencimento = $request->input('data');
                $contaReceber->valor_parcela = $valor_movimentacao; 
                $contaReceber->descricao = $movimentacaoData['descricao'];
                $contaReceber->data_cadastro = Carbon::now()->format('Y-m-d H:i:s');
                $contaReceber->cadastrado_usuario_id = $usuario;
                $contaReceber->save();

                //Vincular Conta com Movimentacao
                $movimentacao_financeira->conta_receber_id = $contaReceber->id;
    
                // Cadastrar Parcelas
                $qtd_parcelas = 1;
                $contaReceber_id = $contaReceber->id;
                $data_vencimento = $contaReceber->data_vencimento; 
                $dataCarbon = Carbon::createFromFormat('Y-m-d', $data_vencimento);
    
                $parcela = new ParcelaContaReceber();
                $parcela->conta_receber_id = $contaReceber_id;
                $parcela->numero_parcela = 1;
                $parcela->situacao = 1;
                $parcela->valor_parcela = $valor_movimentacao;
                $parcela->valor_recebido = $valor_movimentacao;
                $parcela->cadastrado_usuario_id = $usuario;
                $parcela->data_vencimento = $data_vencimento;
                $parcela->data_recebimento = $request->input('data');
                
                        
            }else{ // SAÍDA
    
                //Adicionando categoria
                $movimentacao_financeira->categoria_pagar_id = $movimentacaoData['categoria_id'];
    
                //Atualizando o saldo
                $saldo_model->saldo = $valor_desatualizado_saldo - $valor_movimentacao; 
                $saldo_model->save();
    
                //Atualizar no Contas a Pagar
                $contaPagar = new ContaPagar();
                $contaPagar->titular_conta_id = $request->input('titular_conta_id');
                $contaPagar->fornecedor_id = $movimentacaoData['cliente_fornecedor_id'];
                $contaPagar->categoria_pagar_id = $movimentacaoData['categoria_id'];
                $contaPagar->quantidade_parcela = 1;
                $contaPagar->data_vencimento = $request->input('data');
                $contaPagar->valor_parcela = $valor_movimentacao; 
                $contaPagar->descricao = $movimentacaoData['descricao'];
                $contaPagar->data_cadastro = Carbon::now()->format('Y-m-d H:i:s');
                $contaPagar->cadastrado_usuario_id = $usuario;
                $contaPagar->save();

                //Vincular Conta com Movimentacao
                $movimentacao_financeira->conta_pagar_id = $contaPagar->id;
    
                // Cadastrar Parcelas
                $qtd_parcelas = 1;
                $contaPagar_id = $contaPagar->id;
                $data_vencimento = $contaPagar->data_vencimento; 
                $dataCarbon = Carbon::createFromFormat('Y-m-d', $data_vencimento);
    
                $parcela = new ParcelaContaPagar();
                $parcela->conta_pagar_id = $contaPagar_id;
                $parcela->numero_parcela = 1;
                $parcela->situacao = 1;
                $parcela->valor_pago = $valor_movimentacao;
                $parcela->valor_parcela = $valor_movimentacao;
                $parcela->cadastrado_usuario_id = $usuario;
                $parcela->data_vencimento = $data_vencimento;
                $parcela->data_pagamento = $request->input('data');

            }
    
            //salvar movimentação
            $movimentacao_financeira->save();

            //Vincular parcela com movimentação
            $parcela->movimentacao_financeira_id = $movimentacao_financeira->id;

            //salvar parcela
            $parcela->save();   

        }

        return redirect('movimentacao_financeira')->with('success', 'Movimentação cadastrada com sucesso');
    }

     //EXPORTANDO TABELA PARA PDF
     function relatorio_pdf(Request $request){

        $titular = $request->input('titular');
        $conta_corrente = $request->input('conta_corrente');
        $dataRef = $request->input('data');
        $dataFim = $request->input('data_fim');

        // Saldo anterior
        $saldo_anterior = SaldoDiario::orderBy('data', 'desc')
        ->where('data', '<', $dataRef)
        ->where('titular_conta_id', '=', $titular)
        ->where('conta_corrente_id', '=', $conta_corrente)
        ->get(); 

        $saldo_atual = SaldoDiario::where('data', $dataRef)
        ->where('titular_conta_id', '=', $titular)
        ->where('conta_corrente_id', '=', $conta_corrente)
        ->get(); // Saldo do dia

        $query = DB::table('movimentacao_financeira as mf')
        ->select(
            'mf.*',
            'cr.descricao as categoria_receber',
            'cp.descricao as categoria_pagar',
            'td.descricao as tipo_debito',
            'c.nome as nome',
            'c.tipo_cadastro as tipo_cadastro',
            'c.razao_social as razao_social',
            'pr.id as id_parcela_receber', 
            'pr.id as id_parcela_receber', 
            'pr.debito_id as parcela_receber_debito', 
            'pg.id as id_parcela_pagar',
            'pg.debito_id as parcela_pagar_debito', 
            'tc.id as titular_conta_id',
            'c2.nome as nome_titular',
            'c2.razao_social as razao_social_titular',
            'cc.apelido as conta_corrente'
        )
        ->leftjoin('categoria_receber as cr', 'mf.categoria_receber_id', '=', 'cr.id')
        ->leftjoin('categoria_pagar as cp', 'mf.categoria_pagar_id', '=', 'cp.id')
        ->leftjoin('tipo_debito as td', 'mf.tipo_debito_id', '=', 'td.id')
        ->join('cliente as c', 'mf.cliente_fornecedor_id', '=', 'c.id')
        ->leftjoin('parcela_conta_receber as pr', 'pr.movimentacao_financeira_id', '=', 'mf.id')
        ->leftjoin('parcela_conta_pagar as pg', 'pg.movimentacao_financeira_id',  '=', 'mf.id')
        ->join('titular_conta as tc', 'mf.titular_conta_id', '=', 'tc.id')
        ->join('cliente as c2', 'tc.cliente_id', '=', 'c2.id')
        ->join('conta_corrente as cc', 'mf.conta_corrente_id', '=', 'cc.id');
    

        // Filtro
        if (!empty($dataRef) && !empty($conta_corrente) && !empty($titular) && empty($dataFim)) {
            $query->where('data_movimentacao', '=', '%' . $dataRef)
            ->where('mf.titular_conta_id', '=', $titular)
            ->where('mf.conta_corrente_id', '=', $conta_corrente)
            ->orderBy('id');
        } else if (!empty($dataRef) && !empty($conta_corrente) && !empty($titular) && !empty($dataFim)) {
            $query->where('data_movimentacao', '>=', $dataRef)
            ->where('data_movimentacao', '<=', $dataFim)
            ->where('mf.titular_conta_id', '=', $titular)
            ->where('mf.conta_corrente_id', '=', $conta_corrente)
            ->orderBy('id');
        }

    
        // Execute a consulta e obtenha os resultados
        $movimentacao = $query->get();

        // Clone a instância original da consulta para evitar alterações na mesma instância
        $queryEntradas = clone $query;
        $querySaidas = clone $query;

        // Adicione esta linha para somar os valores com tipo de movimentação 0 (entrada)
        $valorEntradas = $queryEntradas->where('tipo_movimentacao', 0)->sum('valor');

        // Adicione esta linha para somar os valores com tipo de movimentação 1 (saída)
        $valorSaidas = $querySaidas->where('tipo_movimentacao', 1)->sum('valor');
      
        //Selecionar Titulares de Conta
        $titulares_conta = DB::table('titular_conta as tc')
        ->select(
            'tc.*',
            'c.nome as nome',
            'c.razao_social as razao_social'
        )
        ->join('cliente as c', 'tc.cliente_id', '=', 'c.id')
        ->get();

        $data = [
            'titulares_conta' => $titulares_conta,
            'saldo_anterior' => $saldo_anterior,
            'saldo_atual' => $saldo_atual,
            'valorEntradas' => $valorEntradas,
            'valorSaidas' => $valorSaidas,
            'data' => $dataRef,
            'data_fim' => $dataFim,
        ];
        $pdf = PDF::loadView('movimentacao_financeira.movimentacao_financeira_pdf', compact('data', 'movimentacao'));
        return $pdf->download('movimentacao.pdf');
        //return view('movimentacao_financeira.movimentacao_financeira_pdf', compact('data', 'movimentacao'));
    }
}
