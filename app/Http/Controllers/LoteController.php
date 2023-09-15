<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lote;
use App\Models\Quadra;
use App\Models\User;
use App\Models\Cliente;


class LoteController extends Controller
{
     //RETORNA VIEW PARA ADICIONAR LOTE
     function novo($empreendimento_id){
        $quadras = Quadra::where('empreendimento_id', $empreendimento_id)
        ->select('id as quadra_id', 'nome as quadra_nome')
        ->get();
        //$quadras = Quadra::select('quadra.id as quadra_id, quadra_nome')->where('quadra.empreendimento_id', '=', $empreendimento_id);
        $clientes = Cliente::all();
        return view('lote/lote_novo', compact('empreendimento_id', 'quadras'), compact('clientes'));
    }

     //CADASTRO DE LOTE
     function cadastrar($usuario, $empreendimento_id, Request $request){
        // Validar campos
        //$validated = $request->validated();

        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');    

        $lote = new Lote();
        $lote->lote = $request->input('lote');
        $lote->quadra_id = $request->input('quadra');
        $lote->cliente_id = $request->input('responsabilidade');
        $lote->data_cadastro = date('d-m-Y h:i:s a', time());
        $lote->cadastrado_usuario_id = $usuario;
        $lote->save();
        return redirect('empreendimento/gestao/'.$empreendimento_id)->with('success', 'Lote cadastrado com sucesso');
    }
}
