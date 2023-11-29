<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoDebito;
use App\Models\Debito;
use App\Models\ContaReceber;
use App\Models\ContaPagar;



class TipoDebitoController extends Controller
{
    function tipo_debito(){
        $lista_tipo_debito = TipoDebito::all();
        $total_lista_tipo_debito = $lista_tipo_debito->count();
        return view('tipo_debito/tipo_debito', compact('lista_tipo_debito', 'total_lista_tipo_debito') );
    }

    //CADASTRO DE TIPO DE DEBITOS
    function cadastrar($usuario, Request $request){

        //Validar para não salvar descrição
        $validated = $request->validate([
            'descricao' => 'required',
        ]);

        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');    

        $tipo_debito = new TipoDebito();
        $tipo_debito->descricao = $request->input('descricao');
        $tipo_debito->data_cadastro = Carbon::now()->format('Y-m-d H:i:s');
        $tipo_debito->cadastrado_usuario_id = $usuario;
        $tipo_debito->save();
        return redirect()->back()->with('success', 'Cadastro feito com sucesso');
    }

    //EXCLUIR TIPO DE DEBITO
    function excluir($id){
        $tipo_debito = TipoDebito::find($id);

        //Verifica se existe
        if (!$tipo_debito) {
            return redirect()->back()->with('error', 'Tipo de débito não encontrado.');
        }

        // Verifique se existem relacionamentos
        $relacionamentos = Debito::where('tipo_debito_id', $tipo_debito->id)->get();
        if ($relacionamentos->count() > 0) {
            return redirect()->back()->with('error', 'Este tipo de débito está relacionado a alguma conta ou débito e não pode ser excluído.');
        }

        $tipo_debito->delete();
        return redirect()->back()->with('success', 'Exclusão realizada com sucesso');

    }
}
