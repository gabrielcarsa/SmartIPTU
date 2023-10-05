<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DescricaoDebito;

class DescricaoDebitoController extends Controller
{
    function descricao_debito(){
        $descricao_debito = DescricaoDebito::all();
        $total_descricao_debito = $descricao_debito->count();
        return view('descricao_debito/descricao_debito', compact('descricao_debito', 'total_descricao_debito') );
    }
    

    //CADASTRO DE DESCRIÇÃO DE DEBITOS
    function cadastrar($usuario, Request $request){
        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');    

        $descricao_debito = new DescricaoDebito();
        $descricao_debito->descricao = $request->input('descricao');
        $descricao_debito->data_cadastro = date('d-m-Y h:i:s a', time());
        $descricao_debito->cadastrado_usuario_id = $usuario;
        $descricao_debito->save();
        return redirect()->back()->with('success', 'Cadastro feito com sucesso');
    }

    //EXCLUIR DE DESCRIÇÃO DE DEBITOS
    function excluir($id){
        $descricao_debito = DescricaoDebito::find($id);
        $descricao_debito->delete();
        return redirect()->back()->with('success', 'Exclusão realizada com sucesso');

    }
}
