<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContaCorrente;

class ContaCorrenteController extends Controller
{
    //LISTAR TODAS AS CONTAS CORRENTES RELACIONADAS A TITULAR
    function listar($titular_id){
        $contas_corrente = ContaCorrente::where('titular_conta_id', $titular_id)->get();
        return view('conta_corrente/conta_corrente', compact('contas_corrente', 'titular_id'));
    }

    //RETORNA VIEW PARA CADASTRO DE CONTA CORRENTE
    function novo($titular_id){
        return view('conta_corrente/conta_corrente_novo', compact('titular_id'));
    }

    //CADASTRO DE CONTA CORRENTE
    function cadastrar(Request $request, $titular_id, $usuario){

        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');    

        $conta_corrente = new ContaCorrente();
        $conta_corrente->apelido = $request->input('apelido');
        $conta_corrente->banco = $request->input('banco');
        $conta_corrente->agencia = $request->input('agencia');
        $conta_corrente->digito_agencia = $request->input('digitoAgencia');
        $conta_corrente->carteira = $request->input('carteira');
        $conta_corrente->dias_baixa = $request->input('baixa');
        $conta_corrente->numero_conta = $request->input('numeroConta');
        $conta_corrente->digito_conta = $request->input('digitoConta');
        $conta_corrente->titular_conta_id = $titular_id;
        $conta_corrente->data_cadastro = Carbon::now()->format('Y-m-d H:i:s');
        $conta_corrente->cadastrado_usuario_id = $usuario;
        $conta_corrente->save();
        return redirect('conta_corrente/'.$titular_id)->with('success', 'Conta Corrente cadastrada com sucesso');
    }
}
