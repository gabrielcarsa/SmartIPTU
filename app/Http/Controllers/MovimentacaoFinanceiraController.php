<?php

namespace App\Http\Controllers;
use App\Models\MovimentacaoFinanceira;
use App\Models\SaldoDiario;
use App\Models\Cliente;
use App\Models\ContaCorrente;
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
        $saldo_atual = SaldoDiario::where('data', $dataRef)->get(); // Saldo do dia


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
     function cadastrar($usuario, Request $request){
        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');    

        $movimentacao_financeira = new MovimentacaoFinanceira();
        $movimentacao_financeira->data = $request->input('data');
        $movimentacao_financeira->cliente_fornecedor_id = $request->input('cliente_fornecedor_id');
        $movimentacao_financeira->descricao = $request->input('descricao');
        $movimentacao_financeira->tipo_movimentacao = $request->input('tipo_movimentacao');
        $movimentacao_financeira->titular_conta_id = $request->input('titular_conta_id');
        $movimentacao_financeira->conta_corrente_id = $request->input('conta_corrente_id');
        
        $valor = str_replace(',', '.', $request->input('valor'));
        $movimentacao_financeira->valor = (double) $valor; // Converter a string diretamente para um número em ponto flutuante
      
        $movimentacao_financeira->data_cadastro = date('d-m-Y h:i:s a', time());
        $movimentacao_financeira->cadastrado_usuario_id = $usuario;

        //Atualizar saldo do dia

        //Cadastrar no Contas a Pagar ou Receber

        
        $movimentacao_financeira->save();

        return redirect('movimentacao_financeira/listar?'.$movimentacao_financeira->data)->with('success', 'Movimentação cadastrada com sucesso');
    }
}
