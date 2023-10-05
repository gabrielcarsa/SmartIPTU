<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoriaReceber;


class CategoriaReceberController extends Controller
{
    function categoria_receber(){
        $categoria_receber = CategoriaReceber::all();
        $total_categoria_receber = $categoria_receber->count();
        return view('categoria_receber/categoria_receber', compact('categoria_receber', 'total_categoria_receber') );
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
