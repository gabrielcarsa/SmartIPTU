<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoriaPagar;
use App\Models\ContaPagar;
use Carbon\Carbon;

class CategoriaPagarController extends Controller
{
    function categoria_pagar(){
        $categoria_pagar = CategoriaPagar::all();
        $total_categoria_pagar = $categoria_pagar->count();
        return view('categoria_pagar/categoria_pagar', compact('categoria_pagar', 'total_categoria_pagar') );
    }
    

    //CADASTRO DE CATEGORIA DE CONTAS A PAGAR
    function cadastrar($usuario, Request $request){

        //Validar para não salvar descrição
        $validated = $request->validate([
            'descricao' => 'required',
        ]);

        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');    

        $categoria_pagar = new CategoriaPagar();
        $categoria_pagar->descricao = $request->input('descricao');
        $categoria_pagar->data_cadastro = Carbon::now()->format('Y-m-d H:i:s');
        $categoria_pagar->cadastrado_usuario_id = $usuario;
        $categoria_pagar->save();
        return redirect()->back()->with('success', 'Cadastro feito com sucesso');
    }

    //EXCLUIR DE CONTAS A PAGAR
    function excluir($id){
        $categoria_pagar = CategoriaPagar::find($id);

         //Verifica se existe
         if (!$categoria_pagar) {
            return redirect()->back()->with('error', 'Descrição não encontrada.');
        }

        // Verifique se existem relacionamentos
        $relacionamentos = ContaPagar::where('categoria_pagar_id', $categoria_pagar->id)->get();
        if ($relacionamentos->count() > 0) {
            return redirect()->back()->with('error', 'Esta descrição está relacionado a alguma conta a pagar e não pode ser excluído.');
        }

        $categoria_pagar->delete();
        return redirect()->back()->with('success', 'Exclusão realizada com sucesso');

    }

    //RETORNA UM JSON COM A CATEGORIA
    function categoria_pagar_json(){
        $categoria_pagar = CategoriaPagar::all();
        return response()->json($categoria_pagar);
    }
}
