<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DescricaoDebito;
use App\Models\Debito;
use Carbon\Carbon;

class DescricaoDebitoController extends Controller
{
    function descricao_debito(){
        $descricao_debito = DescricaoDebito::all();
        $total_descricao_debito = $descricao_debito->count();
        return view('descricao_debito/descricao_debito', compact('descricao_debito', 'total_descricao_debito') );
    }
    

    //CADASTRO DE DESCRIÇÃO DE DEBITOS
    function cadastrar($usuario, Request $request){
        
        //Validar para não salvar descrição
        $validated = $request->validate([
            'descricao' => 'required',
        ]);

        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');    

        $descricao_debito = new DescricaoDebito();
        $descricao_debito->descricao = $request->input('descricao');
        $descricao_debito->data_cadastro = Carbon::now()->format('Y-m-d H:i:s');
        $descricao_debito->cadastrado_usuario_id = $usuario;
        $descricao_debito->save();
        return redirect()->back()->with('success', 'Cadastro feito com sucesso');
    }

    //EXCLUIR DE DESCRIÇÃO DE DEBITOS
    function excluir($id){
        $descricao_debito = DescricaoDebito::find($id);

         //Verifica se existe
         if (!$descricao_debito) {
            return redirect()->back()->with('error', 'Descrição não encontrada.');
        }

        // Verifique se existem relacionamentos
        $relacionamentos = Debito::where('descricao_debito_id', $descricao_debito->id)->get();
        if ($relacionamentos->count() > 0) {
            return redirect()->back()->with('error', 'Esta descrição está relacionado a algum débito e não pode ser excluído.');
        }

        $descricao_debito->delete();
        return redirect()->back()->with('success', 'Exclusão realizada com sucesso');

    }
}
