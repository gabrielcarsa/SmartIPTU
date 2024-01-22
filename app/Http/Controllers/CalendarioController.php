<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class CalendarioController extends Controller
{
    public function index()
    {
        $eventosDebitos = DB::table('parcela_conta_pagar as p')
        ->select( 
            'p.id as id',
            'p.numero_parcela as numero_parcela',
            'p.data_vencimento as data_vencimento',
            'p.valor_parcela as valor_parcela',
            'p.situacao as situacao',
            'dd.descricao as descricao',  
            'd.quantidade_parcela as quantidade_parcela',
            'c.nome as nome',
            'c.tipo_cadastro as tipo_cadastro',
            'c.razao_social as razao_social',
            'tpd.descricao as tipo_debito_descricao', 
            'l.lote as lote',
            'l.inscricao_municipal as inscricao',
            'e.nome as empreendimento',
            'q.nome as quadra',
        )
        ->selectRaw('CASE WHEN titular_conta_cliente.razao_social IS NOT NULL THEN titular_conta_cliente.razao_social ELSE titular_conta_cliente.nome END AS nome_cliente_ou_razao_social')
        ->join('debito as d', 'p.debito_id', '=', 'd.id')
        ->join('lote as l', 'd.lote_id', '=', 'l.id')
        ->join('quadra as q', 'l.quadra_id', '=', 'q.id')
        ->join('empreendimento as e', 'q.empreendimento_id', '=', 'e.id')
        ->join('cliente as c', 'l.cliente_id', '=', 'c.id')
        ->join('descricao_debito as dd', 'p.descricao_debito_id', '=', 'dd.id')
        ->join('titular_conta as td', 'd.titular_conta_id', '=', 'td.id')
        ->join('tipo_debito as tpd', 'd.tipo_debito_id', '=', 'tpd.id')
        ->leftJoin('cliente AS titular_conta_cliente', 'td.cliente_id', '=', 'titular_conta_cliente.id')
        ->whereColumn('l.cliente_id', '=', 'td.cliente_id')
        ->where('p.situacao', '=', null)
        ->orderBy('p.data_vencimento', 'ASC')
        ->get();

        $eventosOutros =  DB::table('parcela_conta_pagar as p')
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
        ->orderBy('p.data_vencimento', 'ASC')
        ->get();
        return view('calendario/calendario', compact('eventosDebitos', 'eventosOutros'));
    }
}