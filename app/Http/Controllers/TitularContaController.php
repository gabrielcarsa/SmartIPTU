<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TitularConta;
use App\Models\Cliente;
use App\Models\ContaReceber;
use App\Models\ContaPagar;
use App\Models\Debito;
use Illuminate\Support\Facades\DB;


class TitularContaController extends Controller
{
    function titular_conta(){
        $titulares_contas = DB::table('cliente as c')
        ->select(
            'tc.id as id',
        )
        ->selectRaw('CASE WHEN c.razao_social IS NOT NULL THEN c.razao_social ELSE c.nome END AS nome_cliente_ou_razao_social')
        ->join('titular_conta as tc', 'c.id', '=', 'tc.cliente_id')
        ->get();
        $total_titular_conta = $titulares_contas->count();

        $clientes = Cliente::all();

        $data = [
            'total_titular_conta' => $total_titular_conta,
            'titulares_contas' => $titulares_contas,
            'clientes' => $clientes,
        ];

        return view('titular_conta/titular_conta', compact('data'));
    }

    //CADASTRO DE TITULAR DE CONTA
    function cadastrar($usuario, Request $request){
        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');    

        $titular_conta = new TitularConta();
        $titular_conta->cliente_id = $request->input('cliente_id');
        $titular_conta->data_cadastro = date('d-m-Y h:i:s a', time());
        $titular_conta->cadastrado_usuario_id = $usuario;
        $titular_conta->save();
        return redirect()->back()->with('success', 'Cadastro feito com sucesso');
    }

    //EXCLUIR TITULAR CONTA
    function excluir($id){
        $titular_conta = TitularConta::find($id);

        //Verifica se existe
        if (!$titular_conta) {
            return redirect()->back()->with('error', 'Titular não encontrado.');
        }
        // Verifique se existem relacionamentos
        $relacionamentosDebito = Debito::where('titular_conta_id', $titular_conta->id)->get();
        $relacionamentosReceber = ContaReceber::where('titular_conta_id', $titular_conta->id)->get();
        $relacionamentosPagar = ContaPagar::where('titular_conta_id', $titular_conta->id)->get();
        if ($relacionamentosDebito->count() > 0 || $relacionamentosReceber->count() > 0  || $relacionamentosPagar->count() > 0 ) {
            return redirect()->back()->with('error', 'Este Titular está relacionado a alguma conta e não pode ser excluído.');
        }

        $titular_conta->delete();
        return redirect()->back()->with('success', 'Exclusão realizada com sucesso');

    }
}