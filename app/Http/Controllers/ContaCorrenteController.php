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
    function novo(){
        return view('conta_corrente/conta_corrente_novo', compact('titular_id'));
    }

    //CADASTRO DE CONTA CORRENTE
    function cadastro(){

        // Validar campos
        $validated = $request->validated();

        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');    

        $empreendimento = new Empreendimento();
        $empreendimento->nome = $request->input('nome');
        $empreendimento->matricula = $request->input('matricula');
        $empreendimento->cidade = $request->input('cidade');
        $empreendimento->estado = $request->input('estado');
        $empreendimento->data_cadastro = date('d-m-Y h:i:s a', time());
        $empreendimento->cadastrado_usuario_id = $usuario;
        $empreendimento->save();
        return redirect('empreendimento')->with('success', 'Empreendimento cadastrado com sucesso');
    }
}
