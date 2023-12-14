<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prescricao;
use App\Models\User;


class PrescricaoController extends Controller
{
    function prescricao($lote_id){
        $prescricoes = Prescricao::where('lote_id', $lote_id)->get();
        $total_prescricoes = $prescricoes->count();
        return view('prescricao/prescricao_listagem', compact('lote_id', 'prescricoes'), compact('total_prescricoes'));
    }

     //CADASTRO DE PRESCRIÇÃO
     function cadastrar($usuario, $lote_id, Request $request){
        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');    

        $prescricao = new Prescricao();
        $prescricao->processo = $request->input('processo');
        $prescricao->lote_id = $lote_id;
        $prescricao->entrada_pedido = $request->input('entrada_pedido');
        $prescricao->anos_referencia = $request->input('anos_referencia');
        $prescricao->observacao = $request->input('observacao');
        $prescricao->data_cadastro = date('d-m-Y h:i:s a', time());
        $prescricao->cadastrado_usuario_id = $usuario;
        $prescricao->save();
        return redirect('prescricao/'.$lote_id)->with('success', 'Prescrição cadastrado com sucesso');
    }


     //RETORNA VIEW ALTERAR PRESCRICAO      
     function editar($id){        
        $prescricao = Prescricao::where('id', $id)->first();

        $cadastrado_por_user_id = $prescricao['cadastrado_usuario_id'];
        $alterado_por_user_id = $prescricao['alterado_usuario_id'];

        $cadastrado_por_user = User::find($cadastrado_por_user_id);
        $alterado_por_user = User::find($alterado_por_user_id);

        $data = [
            'prescricao' => $prescricao,
            'cadastrado_por_user' => $cadastrado_por_user,
            'alterado_por_user' => $alterado_por_user,
        ];

        return view('prescricao/prescricao_novo', compact('data'));
    }

     //CADASTRO DE PRESCRIÇÃO
     function alterar($id, $usuario, Request $request){
        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');    

        $prescricao = Prescricao::find($id);
        $prescricao->processo = $request->input('processo');
        $prescricao->entrada_pedido = $request->input('entrada_pedido');
        $prescricao->anos_referencia = $request->input('anos_referencia');
        $prescricao->observacao = $request->input('observacao');
        $prescricao->data_alteracao = date('d-m-Y h:i:s a', time());
        $prescricao->alterado_usuario_id = $usuario;
        $prescricao->save();
        return redirect('prescricao/'.$prescricao->lote_id)->with('success', 'Prescrição alterado com sucesso');
    }

      //EXCLUIR PRESCRIÇÃO
      function excluir($id){
        $prescricao = Prescricao::find($id);
        $lote_id = $prescricao->lote_id;

    
        if (!$prescricao) {
            return redirect()->back()->with('error', 'Processo de prescrição não encontrado');
        }
    
        $prescricao->delete();
    
        return redirect("prescricao/".$lote_id)->with('success', 'Prescrição excluída com sucesso');
    }
}
