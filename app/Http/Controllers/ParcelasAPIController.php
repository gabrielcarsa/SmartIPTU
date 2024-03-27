<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\MovimentacaoFinanceira;
use App\Models\SaldoDiario;
use App\Models\ContaCorrente;
use App\Models\TitularConta;
use App\Models\Cliente;
use App\Models\CategoriaPagar;

class ParcelasAPIController extends Controller
{
    public function paraPagarReceberHoje(Request $request){
        $key = $request->query('key');

        if($key == "AmbienteAplicativo01"){
            $hoje = now()->toDateString(); // Obtém a data de hoje no formato 'YYYY-MM-DD'

            //Soma das parcelas a Pagar no dia de Hoje
            $pagarHoje =  DB::table('parcela_conta_pagar')
            ->whereDate('data_vencimento', $hoje)
            ->where('situacao', 0)
            ->sum('valor_parcela');
    
            //Soma das parcelas a Receber no dia de Hoje
            $receberHoje =  DB::table('parcela_conta_receber')
            ->whereDate('data_vencimento', $hoje)
            ->where('situacao', 0)
            ->sum('valor_parcela');
    
            //Valor de Debitos a pagar atrasados
            $debitosEmpresa =  DB::table('parcela_conta_pagar')
            ->whereDate('data_vencimento', '<', $hoje)
            ->where('situacao', 0)
            ->where('debito_id', '!=', null)
            ->sum('valor_parcela');
    
            //Valor de Debitos a receber atrasados
            $debitosCliente =  DB::table('parcela_conta_receber')
            ->whereDate('data_vencimento', '<', $hoje)
            ->where('situacao', 0)
            ->where('debito_id', '!=', null)
            ->sum('valor_parcela');

            //Valor Contas a Pagar Empresa
            $totalContasPagarEmpresa =  DB::table('parcela_conta_pagar as p')
            ->join('conta_pagar as cp', 'p.conta_pagar_id', '=', 'cp.id')
            ->join('titular_conta as td', 'cp.titular_conta_id', '=', 'td.id')
            ->whereDate('p.data_vencimento', '<=', $hoje)
            ->where('td.id', 1)
            ->where('p.situacao', 0)
            ->where('p.debito_id', '=', null)
            ->sum('p.valor_parcela');

            //Valor Contas a Pagar Empresa
            $totalContasPagarPessoal =  DB::table('parcela_conta_pagar as p')
            ->join('conta_pagar as cp', 'p.conta_pagar_id', '=', 'cp.id')
            ->join('titular_conta as td', 'cp.titular_conta_id', '=', 'td.id')
            ->whereDate('p.data_vencimento', '<=', $hoje)
            ->where('td.id', 2)
            ->where('p.situacao', 0)
            ->where('p.debito_id', '=', null)
            ->sum('p.valor_parcela');

            //Valor Contas a Pagar Empresa
            $totalRescisao =  DB::table('parcela_conta_pagar as p')
            ->join('conta_pagar as cp', 'p.conta_pagar_id', '=', 'cp.id')
            ->join('titular_conta as td', 'cp.titular_conta_id', '=', 'td.id')
            ->whereDate('p.data_vencimento', '<=', $hoje)
            ->where('p.situacao', 0)
            ->where('p.debito_id', '=', null)
            ->where('cp.categoria_pagar_id', 1)
            ->sum('p.valor_parcela');
    
            $data = [
                'pagarHoje' => $pagarHoje,
                'receberHoje' => $receberHoje,
                'debitosEmpresa' => $debitosEmpresa,
                'debitosCliente' => $debitosCliente,
                'totalContasPagarEmpresa' => $totalContasPagarEmpresa,
                'totalContasPagarPessoal' => $totalContasPagarPessoal,
                'totalRescisao' => $totalRescisao,
            ];
    
            return response()->json($data);
        }else{
            $data = "Chave inválida";
            return response()->json($data);

        }
    }


    public function categoria_pagar(Request $request){
        $key = $request->query('key');

        if($key == "AmbienteAplicativo01"){
            $categoriasPagar = CategoriaPagar::all();
    
            return response()->json($categoriasPagar);
        }else{
            $data = "Chave inválida";
            return response()->json($data);

        }
    }


    public function calendario_financeiro_pagar(Request $request){
        $key = $request->query('key');
        $categoria = $request->query('categoria');

        if($key == "AmbienteAplicativo01"){
            $dataSolicitada = $request->query('data_solicitada');

            $contasPagarOutros =  DB::table('parcela_conta_pagar as p')
            ->select(
                'p.id as id',
                'p.numero_parcela as numero_parcela',
                'p.data_vencimento as data_vencimento',
                'p.valor_parcela as valor_parcela',
                'p.situacao as situacao_parcela',
                'p.valor_pago as parcela_valor_pago',
                'p.data_pagamento as data_pagamento',
                'p.data_baixa as data_baixa',
                'p.cadastrado_usuario_id as parcela_cadastrado_usuario_id',
                'p.alterado_usuario_id as parcela_alterado_usuario_id',
                'p.usuario_baixa_id as parcela_usuario_baixa_id',
                'p.data_alteracao as parcela_data_alteracao',
                'cp.quantidade_parcela as quantidade_parcela',
                'ctp.descricao as descricao',
                'c.nome as nome',
                'c.tipo_cadastro as tipo_cadastro',
                'c.razao_social as razao_social',
            )
            ->selectRaw('CASE WHEN titular_conta_cliente.razao_social IS NOT NULL THEN titular_conta_cliente.razao_social ELSE titular_conta_cliente.nome END AS nome_cliente_ou_razao_social')
            ->join('conta_pagar as cp', 'p.conta_pagar_id', '=', 'cp.id')
            ->join('cliente as c', 'cp.fornecedor_id', '=', 'c.id')
            ->join('categoria_pagar as ctp', 'cp.categoria_pagar_id', '=', 'ctp.id')
            ->join('titular_conta as td', 'cp.titular_conta_id', '=', 'td.id')
            ->leftJoin('cliente AS titular_conta_cliente', 'td.cliente_id', '=', 'titular_conta_cliente.id')
            ->where('p.situacao', '=', 0)
            ->where('p.data_vencimento', '<=', $dataSolicitada)
            ->orderBy('p.data_vencimento', 'ASC');

            if($categoria == '- Todas Categorias -'){
                $contasPagarOutros = $contasPagarOutros->get();
            }else{
                $contasPagarOutros = $contasPagarOutros->where('ctp.descricao', $categoria)->get();
            }
    
           
            // Inicialize o valor total como zero
            $valorTotalContasPagar = 0;
    
            // Itere sobre as parcelas e some seus valores
            foreach ($contasPagarOutros as $parcela) {
                $valorTotalContasPagar += $parcela->valor_parcela;
            }
    
    
            $data = [
                'contasPagarOutros' => $contasPagarOutros,
                'valorTotalContasPagar' => $valorTotalContasPagar
                
            ];
    
            return response()->json($data);
        }else{
            $data = "Chave inválida";
            return response()->json($data);

        }
        

    }

    public function calendario_financeiro_receber(Request $request){
        $key = $request->query('key');

        if($key == "AmbienteAplicativo01"){
            $dataSolicitada = $request->query('data_solicitada');

            $contasReceberOutros =  DB::table('parcela_conta_receber as p')
            ->select(
                'p.id as id',
                'p.numero_parcela as numero_parcela',
                'p.data_vencimento as data_vencimento',
                'p.valor_parcela as valor_parcela',
                'p.situacao as situacao_parcela',
                'p.valor_recebido as parcela_valor_recebido',
                'p.data_recebimento as data_recebimento',
                'p.data_baixa as data_baixa',
                'p.cadastrado_usuario_id as parcela_cadastrado_usuario_id',
                'p.alterado_usuario_id as parcela_alterado_usuario_id',
                'p.usuario_baixa_id as parcela_usuario_baixa_id',
                'p.data_alteracao as parcela_data_alteracao',
                'cr.quantidade_parcela as quantidade_parcela',
                'ctr.descricao as descricao',
                'c.nome as nome',
                'c.tipo_cadastro as tipo_cadastro',
                'c.razao_social as razao_social',
            )
            ->selectRaw('CASE WHEN titular_conta_cliente.razao_social IS NOT NULL THEN titular_conta_cliente.razao_social ELSE titular_conta_cliente.nome END AS nome_cliente_ou_razao_social')
            ->join('conta_receber as cr', 'p.conta_receber_id', '=', 'cr.id')
            ->join('cliente as c', 'cr.cliente_id', '=', 'c.id')
            ->join('categoria_receber as ctr', 'cr.categoria_receber_id', '=', 'ctr.id')
            ->join('titular_conta as td', 'cr.titular_conta_id', '=', 'td.id')
            ->leftJoin('cliente AS titular_conta_cliente', 'td.cliente_id', '=', 'titular_conta_cliente.id')
            ->where('p.situacao', '=', 0)
            ->where('p.data_vencimento', $dataSolicitada)
            ->orderBy('p.data_vencimento', 'ASC')
            ->get();
    
            // Inicialize o valor total como zero
            $valorTotalContasReceber = 0;
    
            // Itere sobre as parcelas e some seus valores
            foreach ($contasReceberOutros as $parcela) {
                $valorTotalContasReceber += $parcela->valor_parcela;
            }
            $data = [
                'contasReceberOutros' => $contasReceberOutros,
                'valorTotalContasReceber' => $valorTotalContasReceber
                
            ];
    
            return response()->json($data);
        }else{
            $data = "Chave inválida";
            return response()->json($data);

        }
       

    }

    public function titulares_conta(Request $request){
        $key = $request->query('key');

        if($key == "AmbienteAplicativo01"){
            //Selecionar Titulares de Conta
            $titulares_conta = DB::table('titular_conta as tc')
            ->select(
                'tc.*',
                'c.nome as nome',
                'c.razao_social as razao_social'
            )
            ->join('cliente as c', 'tc.cliente_id', '=', 'c.id')
            ->get();

            return response()->json($titulares_conta);
        }else{
            $data = "Chave inválida";
            return response()->json($data);

        }
        

    }
    
    //RETORNA UM JSON COM A CONTA CORRENTE ESPECÍFICA
    function conta_corrente(Request $request){
        $key = $request->query('key');
        $TitularNomeOuRazaoSocial = $request->query('titular_conta');

        $cliente_id = Cliente::where('razao_social', $TitularNomeOuRazaoSocial)->first();
        if (!$cliente_id) {
            $data = "Titular não encontrado";
            return response()->json($data);
        }

        $titular_conta_id = TitularConta::where('cliente_id', $cliente_id->id)->first();
      
        if (!$titular_conta_id) {
            $data = "Titular de conta não encontrado";
            return response()->json($data);
        }

        if($key == "AmbienteAplicativo01"){
            $conta_corrente = ContaCorrente::where('titular_conta_id', $titular_conta_id->id)->get();
            return response()->json($conta_corrente);
        }else{
            $data = "Chave inválida";
            return response()->json($data);

        }

    }

    public function movimentacao_financeira_listar(Request $request){
        $key = $request->query('key');

        if($key == "AmbienteAplicativo01"){
            $movimentacao = MovimentacaoFinanceira::all();    
        
            $titular = $request->input('titular_conta');
            $conta_corrente = $request->input('conta_corrente');
            $dataRef = $request->input('data');
            $dataFim = $request->input('data_fim');
         

            $cliente_id = Cliente::where('razao_social', $titular)->first();
            if (!$cliente_id) {
                $data = "Titular não encontrado";
                return response()->json($data);
            }
    
            $titular_conta_id = TitularConta::where('cliente_id', $cliente_id->id)->first();
            if (!$titular_conta_id) {
                $data = "Titular de conta não encontrado";
                return response()->json($data);
            }
    
    
            $conta_corrente = ContaCorrente::where('apelido', $conta_corrente)->first();
     
            if (!$conta_corrente) {
                $data = "Conta Corrente não encontrada";
                return response()->json($data);
            }
    
            // Saldo anterior
            $saldo_anterior = SaldoDiario::orderBy('data', 'desc')
            ->where('data', '<', $dataRef)
            ->where('titular_conta_id', '=', $titular_conta_id->id)
            ->where('conta_corrente_id', '=', $conta_corrente->id)
            ->get(); 
    
            $saldo_atual = SaldoDiario::where('data', '=', $dataRef)
            ->where('titular_conta_id', '=', $titular_conta_id->id)
            ->where('conta_corrente_id', '=', $conta_corrente->id)
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
            ->join('cliente as c2', 'tc.cliente_id', '=', 'c2.id')
            ->orderBy('mf.ordem');
    
            // Filtro
            if (!empty($dataRef) && !empty($conta_corrente) && !empty($titular_conta_id) && !empty($dataFim)) {
                $query->where('data_movimentacao', '>=', $dataRef)
                ->where('data_movimentacao', '<=', $dataFim)
                ->where('mf.titular_conta_id', '=', $titular_conta_id->id)
                ->where('mf.conta_corrente_id', '=', $conta_corrente->id)
                ->orderBy('id');
            }
    
            // Execute a consulta e obtenha os resultados
            $movimentacao = $query->get();

    
            $data = [
                'saldo_anterior' => $saldo_anterior,
                'saldo_atual' => $saldo_atual,
                'total_movimentacao' => $total_movimentacao,
                'movimentacao' => $movimentacao
            ];
    
            return response()->json($data);
        }else{
            $data = "Chave inválida";
            return response()->json($data);

        }
      
    }
}
