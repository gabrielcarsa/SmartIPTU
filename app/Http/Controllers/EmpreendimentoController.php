<?php

namespace App\Http\Controllers;
use App\Models\Empreendimento;
use App\Models\User;
use Illuminate\Http\Request;

class EmpreendimentoController extends Controller
{
    // LISTAGEM DE EMPREENDIMENTOS
    function listar(){
        $empreendimentos = Empreendimento::all();
        $total_empreendimentos = $empreendimentos->count();

        return view('empreendimento/empreendimento_listagem', compact('empreendimentos', 'total_empreendimentos') );
    }

    //RETORNA VIEW PARA ADICIONAR EMPREENDIMENTO
    function novo(){
        return view('empreendimento/empreendimento_novo');
    }

    //CADASTRO DE EMPREENDIMENTO
    function cadastrar($usuario, Request $request){

        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');    

        $empreendimento = new Empreendimento();
        $empreendimento->nome = $request->input('nome');
        $empreendimento->matricula = $request->input('matricula');
        $empreendimento->cidade = $request->input('cidade');
        $empreendimento->estado = $request->input('estado');
        $empreendimento->data_cadastro = date('d-m-Y h:i:s a', time());
        $empreendimento->cadastrado_usuario_id = $usuario;
        $empreendimento->save();
        return redirect('empreendimento')->with('success', 'Empreendimento cadastrado com sucesso');
    }

    //RETORNA VIEW ALTERAR EMPREENDIMENTO  
    function editar($id){
        $empreendimento = Empreendimento::find($id);
        
        $cadastrado_por_user_id = $empreendimento['cadastrado_usuario_id'];
        $alterado_por_user_id = $empreendimento['alterado_usuario_id'];

        $cadastrado_por_user = User::find($cadastrado_por_user_id);
        $alterado_por_user = User::find($alterado_por_user_id);

        return view('empreendimento/empreendimento_novo', compact('empreendimento', 'alterado_por_user'), compact('cadastrado_por_user'));
    }


    //ALTERAR EMPREENDIMENTO      
    function alterar($id, $usuario, Request $request){ 
        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');

       $empreendimento = Empreendimento::find($id);

       if (!$empreendimento) {
           return redirect()->back()->with('error', 'Empreendimento não encontrado');
       }

       $empreendimento->nome = $request->input('nome');
       $empreendimento->matricula = $request->input('matricula');
       $empreendimento->cidade = $request->input('cidade');
       $empreendimento->estado = $request->input('estado');      
       $empreendimento->data_alteracao = date('d-m-Y h:i:s a', time());
       $empreendimento->alterado_usuario_id = $usuario;
       $empreendimento->save();
   
       return redirect('empreendimento/editar/'.$id)->with('success', 'Cliente atualizado com sucesso');
   }
    
    //EXCLUIR EMPREENDIMENTO
    function excluir($id){
        $empreendimento = Empreendimento::find($id);
        $empreendimento->delete();
        return redirect("empreendimento")->with('success', 'Empreendimento excluido com sucesso');
    }

}