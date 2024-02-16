<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ParcelasAPIController extends Controller
{
    public function paraPagarReceberHoje(Request $request){
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

        $data = [
            'pagarHoje' => $pagarHoje,
            'receberHoje' => $receberHoje,
            'debitosEmpresa' => $debitosEmpresa,
            'debitosCliente' => $debitosCliente,
        ];

        return response()->json($data);
    }

    public function calendario_financeiro_pagar(Request $request){

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
        ->where('p.data_vencimento', $dataSolicitada)
        ->orderBy('p.data_vencimento', 'ASC')
        ->get();

        $data = [
            'contasPagarOutros' => $contasPagarOutros,
            
        ];

        return response()->json($data);

    }

    public function calendario_financeiro_receber(Request $request){

        $dataSolicitada = $request->query('data_solicitada');

        $contasreceberOutros =  DB::table('parcela_conta_receber as p')
        ->select(
            'p.id as id',
            'p.numero_parcela as numero_parcela',
            'p.data_vencimento as data_vencimento',
            'p.valor_parcela as valor_parcela',
            'p.situacao as situacao_parcela',
            'p.valor_recebido as parcela_valor_recebido',
            'p.data_pagamento as data_pagamento',
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

        $data = [
            'contasReceberOutros' => $contasReceberOutros,
            
        ];

        return response()->json($data);

    }
}
