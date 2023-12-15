<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cliente;
use App\Models\TitularConta;
use App\Models\Empreendimento;
use App\Models\TipoDebito;
use Carbon\Carbon;


class DashboardController extends Controller
{
    function dashboard(){
        $hoje = now()->toDateString(); // Obtém a data de hoje no formato 'YYYY-MM-DD'
        $hojeAux = Carbon::now(); // Obtém a data e hora atual com Carbon
        $data30diasAtras = $hojeAux->subDays(30); //Data 30 dias atrás

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

        //Total de parcelas a pagar atrasadas
        $pagamentosAtrasados =  DB::table('parcela_conta_pagar')
        ->whereDate('data_vencimento', '<', $hoje)
        ->where('situacao', 0)
        ->sum('valor_parcela');

        //Valor de Debitos a pagar atrasados
        $debitosPagarAtrasados =  DB::table('parcela_conta_pagar')
        ->whereDate('data_vencimento', '<', $hoje)
        ->where('situacao', 0)
        ->where('debito_id', '!=', null)
        ->sum('valor_parcela');

        //Valor de Debitos a receber atrasados
        $debitosReceberAtrasados =  DB::table('parcela_conta_receber')
        ->whereDate('data_vencimento', '<', $hoje)
        ->where('situacao', 0)
        ->where('debito_id', '!=', null)
        ->sum('valor_parcela');

        //Valor total de debitos atrasados a receber e pagar
        $totalDividaDebitos = $debitosPagarAtrasados + $debitosReceberAtrasados;

        //Ranking maiores saídas dos últimos 30 dias
        $rankingSaidas = DB::table('movimentacao_financeira as mf')
        ->selectRaw('CASE WHEN cp.descricao IS NOT NULL THEN cp.descricao ELSE td.descricao END AS categoria')
        ->selectRaw('SUM(mf.valor) as total')
        ->leftJoin('tipo_debito as td', 'td.id', '=', 'mf.tipo_debito_id')
        ->leftJoin('categoria_pagar as cp', 'cp.id', '=', 'mf.categoria_pagar_id')
        ->where('mf.tipo_movimentacao', '=', 1)
        ->whereDate('mf.data_movimentacao', '>', $data30diasAtras)
        ->groupBy('categoria')
        ->orderBy('mf.valor', 'desc')
        ->get();

        // Inicializa a variável para armazenar a soma
        $totalSaidas = 0;

        // Itera sobre os resultados do ranking e acumula a soma
        foreach ($rankingSaidas as $saida) {
            $totalSaidas += $saida->total;
        }

        //Alimentar gráfico de Receber Débitos por ano
        $receberPorAnos = DB::table('parcela_conta_receber as p')
            ->selectRaw("EXTRACT(YEAR FROM p.data_vencimento) as ano_vencimento")
            ->selectRaw('SUM(p.valor_parcela) as total_debitos')
            ->join('debito as d', 'p.debito_id', '=', 'd.id')
            ->join('lote as l', 'd.lote_id', '=', 'l.id')
            ->join('quadra as q', 'l.quadra_id', '=', 'q.id')
            ->join('empreendimento as e', 'q.empreendimento_id', '=', 'e.id')
            ->join('cliente as c', 'l.cliente_id', '=', 'c.id')
            ->join('descricao_debito as dd', 'd.descricao_debito_id', '=', 'dd.id')
            ->join('titular_conta as td', 'd.titular_conta_id', '=', 'td.id')
            ->join('tipo_debito as tpd', 'd.tipo_debito_id', '=', 'tpd.id')
            ->leftJoin('cliente AS titular_conta_cliente', 'td.cliente_id', '=', 'titular_conta_cliente.id')
            ->join('users as uc', 'uc.id', '=', 'p.cadastrado_usuario_id') // Usuario que cadastrou a parcela
            ->leftJoin('users as ua', 'ua.id', '=', 'p.alterado_usuario_id') // Usuário que alterou, usando LEFT JOIN para permitir nulos
            ->leftJoin('users as ub', 'ub.id', '=', 'p.usuario_baixa_id') // Usuário que baixou, usando LEFT JOIN para permitir nulos
            ->whereColumn('l.cliente_id', '<>', 'td.cliente_id')
            ->whereNull('p.situacao')
            ->groupBy('ano_vencimento')
            ->orderBy('ano_vencimento', 'ASC')
            ->get();
        $data = [
            'pagarHoje' => $pagarHoje,
            'receberHoje' => $receberHoje,
            'pagamentosAtrasados' => $pagamentosAtrasados,
            'totalDividaDebitos' => $totalDividaDebitos,
            'debitosPagarAtrasados' => $debitosPagarAtrasados,
            'debitosReceberAtrasados' => $debitosReceberAtrasados,
            'rankingSaidas' => $rankingSaidas,
            'totalSaidas' => $totalSaidas,
            'receberPorAnos' => $receberPorAnos->toArray(),
        ];

        return view('dashboard', compact('data'));



    }
}
