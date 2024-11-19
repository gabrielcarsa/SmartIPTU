<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cliente;
use App\Models\TitularConta;
use App\Models\Lote;
use App\Models\Empreendimento;
use App\Models\TipoDebito;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
        $pagamentosAtrasadosOutros =  DB::table('parcela_conta_pagar')
        ->whereDate('data_vencimento', '<', $hoje)
        ->where('situacao', 0)
        ->where('conta_pagar_id', '!=', null)
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
        $totalDebitos = $debitosPagarAtrasados + $debitosReceberAtrasados;

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
        ->limit('5')
        ->get();

        // Inicializa a variável para armazenar a soma
        $totalSaidas = 0;

        // Itera sobre os resultados do ranking e acumula a soma
        foreach ($rankingSaidas as $saida) {
            $totalSaidas += $saida->total;
        }

        //Total lotes
        $lotesTotal = Lote::all();
        $lotesTotal = $lotesTotal->count();

        //Lotes Empresa
        $lotesEmpresa = Lote::where('cliente_id', 1)->count();

        //Lotes Clientes
        $lotesClientes = Lote::where('cliente_id', '!=', 1)->count();

        //Lotes Escriturados
        $lotesEscriturados = Lote::where('is_escriturado', 1)->count();

        //Clientes sem número
        $clientesSemNumero = Cliente::where(function($query) {
            $query->where('is_contato_verificado', '!=', 1)
                  ->orWhereNull('is_contato_verificado')
                  ->orWhere('is_contato_verificado', 0);
        })->count();

        //Clientes com número
        $clientesComNumero = Cliente::where('is_contato_verificado', 1)->count();

        $data = [
            'pagarHoje' => $pagarHoje,
            'receberHoje' => $receberHoje,
            'pagamentosAtrasadosOutros' => $pagamentosAtrasadosOutros,
            'totalDebitos' => $totalDebitos,
            'debitosPagarAtrasados' => $debitosPagarAtrasados,
            'debitosReceberAtrasados' => $debitosReceberAtrasados,
            'rankingSaidas' => $rankingSaidas,
            'totalSaidas' => $totalSaidas,
            'lotesTotal' => $lotesTotal,
            'lotesClientes' => $lotesClientes,
            'lotesEmpresa' => $lotesEmpresa,
            'lotesEscriturados' => $lotesEscriturados,
            'clientesSemNumero' => $clientesSemNumero,
            'clientesComNumero' => $clientesComNumero,
        ];

        return view('dashboard', compact('data'));
    }
}
