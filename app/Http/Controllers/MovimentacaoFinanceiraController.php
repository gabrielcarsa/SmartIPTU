<?php

namespace App\Http\Controllers;
use App\Models\MovimentacaoFinanceira;
use App\Models\SaldoDiario;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MovimentacaoFinanceiraController extends Controller
{
    function movimentacao_financeira(){
        return view('movimentacao_financeira/movimentacao_financeira');
    }

     // LISTAGEM DE MOVIMENTAÇÃO FINANCEIRA
     function listar(Request $request){
        $movimentacao = MovimentacaoFinanceira::all();

        //Datas 
        $dataRef = $request->input('data');
        $diaAnterior = Carbon::parse($dataRef)->subDay(); // Subtrai um dia da data fornecida

        $saldo_anterior = SaldoDiario::where('data', $diaAnterior->toDateString())->get(); // Saldo do dia anterior
        $saldo_atual = SaldoDiario::where('data', $dataRef)->get(); // Saldo do dia anterior


        $total_movimentacao = $movimentacao->count();
        $query = DB::table('movimentacao_financeira as mf');

        // Verifique se o campo "nome" está preenchido no formulário
        if ($request->filled('data')) {
            $query->select(
                'mf.*',
                'c.nome as nome',
                'c.tipo_cadastro as tipo_cadastro',
                'c.razao_social as razao_social',
            )
            ->join('cliente as c', 'mf.cliente_fornecedor_id', '=', 'c.id')
            ->where('data_movimentacao', '=', '%' . $dataRef);
        }

        // Execute a consulta e obtenha os resultados
        $movimentacao = $query->get();


        $data = [
            'saldo_anterior' => $saldo_anterior,
            'saldo_atual' => $saldo_atual,
            'total_movimentacao' => $total_movimentacao,
        ];

        return view('movimentacao_financeira/movimentacao_financeira', compact('movimentacao', 'data'));
    }
}
