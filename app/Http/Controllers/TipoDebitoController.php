<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoDebito;

class TipoDebitoController extends Controller
{
    function tipo_debito(){
        $lista_tipo_debito = TipoDebito::all();
        $total_lista_tipo_debito = $lista_tipo_debito->count();
        return view('tipo_debito/tipo_debito', compact('lista_tipo_debito', 'total_lista_tipo_debito') );
    }

    //CADASTRO DE TIPO DE DEBITOS
    function cadastrar($usuario, Request $request){

        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');    

        $tipo_debito = new TipoDebito();
        $tipo_debito->descricao = $request->input('descricao');
        $tipo_debito->data_cadastro = date('d-m-Y h:i:s a', time());
        $tipo_debito->cadastrado_usuario_id = $usuario;
        $tipo_debito->save();
        return redirect()->back()->with('success', 'Cadastro feito com sucesso');
    }

    //EXCLUIR TIPO DE DEBITO
    function excluir($id){
        $tipo_debito = TipoDebito::find($id);
        $tipo_debito->delete();
        return redirect()->back()->with('success', 'Exclusão realizada com sucesso');

    }
}
