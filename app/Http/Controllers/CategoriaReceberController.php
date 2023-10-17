<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoriaReceber;
use App\Models\ContaReceber;



class CategoriaReceberController extends Controller
{
    function categoria_receber(){
        $categoria_receber = CategoriaReceber::all();
        $total_categoria_receber = $categoria_receber->count();
        return view('categoria_receber/categoria_receber', compact('categoria_receber', 'total_categoria_receber') );
    }
    

    //CADASTRO DE CATEGORIA DE CONTAS A RECEBER
    function cadastrar($usuario, Request $request){

        //Validar para não salvar descrição
        $validated = $request->validate([
            'descricao' => 'required',
        ]);

        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');    

        $categoria_receber = new CategoriaReceber();
        $categoria_receber->descricao = $request->input('descricao');
        $categoria_receber->data_cadastro = date('d-m-Y h:i:s a', time());
        $categoria_receber->cadastrado_usuario_id = $usuario;
        $categoria_receber->save();
        return redirect()->back()->with('success', 'Cadastro feito com sucesso');
    }

    //EXCLUIR DE CONTAS A PAGAR
    function excluir($id){
        $categoria_receber = CategoriaReceber::find($id);

         //Verifica se existe
         if (!$categoria_receber) {
            return redirect()->back()->with('error', 'Descrição não encontrada.');
        }

        // Verifique se existem relacionamentos
        $relacionamentos = ContaReceber::where('categoria_receber_id', $categoria_receber->id)->get();
        if ($relacionamentos->count() > 0) {
            return redirect()->back()->with('error', 'Esta descrição está relacionado a alguma conta a receber e não pode ser excluído.');
        }
        $categoria_receber->delete();
        return redirect()->back()->with('success', 'Exclusão realizada com sucesso');

    }
}
