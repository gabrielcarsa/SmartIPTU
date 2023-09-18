<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lote;
use App\Models\Quadra;
use App\Models\User;
use App\Models\Cliente;
use App\Http\Requests\LoteRequest;


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
     function cadastrar($usuario, $empreendimento_id, loteRequest $request){
        // Validar campos
        //$validated = $request->validated();

        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');    

        $lote = new Lote();
        $lote->lote = $request->input('lote');
        $lote->quadra_id = $request->input('quadra_id');
        $lote->cliente_id = $request->input('cliente_id');
        $lote->matricula = $request->input('matricula');
        $lote->inscricao_municipal = $request->input('inscricao_municipal');
        $lote->valor = $request->input('valor');
        $lote->endereco = $request->input('endereco');
        $lote->metros_quadrados = $request->input('metros_quadrados');
        $lote->metragem_frente = $request->input('metragem_frente');
        $lote->metragem_fundo = $request->input('metragem_fundo');
        $lote->metragem_direita = $request->input('metragem_direita');
        $lote->metragem_esquerda = $request->input('metragem_esquerda');
        $lote->metragem_fundo = $request->input('metragem_fundo');
        $lote->confrontacao_frente = $request->input('confrontacao_frente');
        $lote->confrontacao_fundo = $request->input('confrontacao_fundo');
        $lote->confrontacao_direita = $request->input('confrontacao_direita');
        $lote->confrontacao_esquerda = $request->input('confrontacao_esquerda');
        $lote->data_cadastro = date('d-m-Y h:i:s a', time());
        $lote->cadastrado_usuario_id = $usuario;
        $lote->save();
        return redirect('empreendimento/gestao/'.$empreendimento_id)->with('success', 'Lote cadastrado com sucesso');
    }
}
