<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoriaPagar;

class CategoriaPagarController extends Controller
{
    function categoria_pagar(){
        $categoria_pagar = CategoriaPagar::all();
        $total_categoria_pagar = $categoria_pagar->count();
        return view('categoria_pagar/categoria_pagar', compact('categoria_pagar', 'total_categoria_pagar') );
    }
    

    //CADASTRO DE CATEGORIA DE CONTAS A PAGAR
    function cadastrar($usuario, Request $request){
        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');    

        $categoria_pagar = new CategoriaPagar();
        $categoria_pagar->descricao = $request->input('descricao');
        $categoria_pagar->data_cadastro = date('d-m-Y h:i:s a', time());
        $categoria_pagar->cadastrado_usuario_id = $usuario;
        $categoria_pagar->save();
        return redirect()->back()->with('success', 'Cadastro feito com sucesso');
    }

    //EXCLUIR DE CONTAS A PAGAR
    function excluir($id){
        $categoria_pagar = CategoriaPagar::find($id);
        $categoria_pagar->delete();
        return redirect()->back()->with('success', 'Exclusão realizada com sucesso');

    }
}
