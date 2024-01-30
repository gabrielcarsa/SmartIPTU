<?php

namespace App\Http\Controllers;
use App\Models\Empreendimento;
use App\Models\User;
use App\Models\Quadra;
use App\Models\Lote;
use Illuminate\Http\Request;
use App\Http\Requests\EmpreendimentoRequest;
use Carbon\Carbon;

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
    function cadastrar($usuario, EmpreendimentoRequest $request){

        // Validar campos
        $validated = $request->validated();

        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');    

        $empreendimento = new Empreendimento();
        $empreendimento->nome = $request->input('nome');
        $empreendimento->matricula = $request->input('matricula');
        $empreendimento->cidade = $request->input('cidade');
        $empreendimento->estado = $request->input('estado');
        $empreendimento->data_cadastro = Carbon::now()->format('Y-m-d H:i:s');
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
    function alterar($id, $usuario, EmpreendimentoRequest $request){ 
        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');

        // Validar campos
        $validated = $request->validated();

        $empreendimento = Empreendimento::find($id);

        if (!$empreendimento) {
            return redirect()->back()->with('error', 'Empreendimento não encontrado');
        }

        $empreendimento->nome = $request->input('nome');
        $empreendimento->matricula = $request->input('matricula');
        $empreendimento->cidade = $request->input('cidade');
        $empreendimento->estado = $request->input('estado');      
        $empreendimento->data_alteracao = Carbon::now()->format('Y-m-d H:i:s');
        $empreendimento->alterado_usuario_id = $usuario;
        $empreendimento->save();
    
        return redirect('empreendimento/editar/'.$id)->with('success', 'Empreendimento atualizado com sucesso');
    }
    
    //EXCLUIR EMPREENDIMENTO
    function excluir($id){
        $empreendimento = Empreendimento::find($id);
        $empreendimento->delete();
        return redirect("empreendimento")->with('success', 'Empreendimento excluido com sucesso');
    }

     // GESTÃO EMPREENDIMENTO
     function gestao($id, Request $request){

        $empreendimento = Empreendimento::find($id);
        
        $resultado = Quadra::select(
            'quadra.id as quadra_id',
            'quadra.nome as quadra_nome',
            'quadra.empreendimento_id as quadra_empreendimento_id',
            'lote.id as lote_id',
            'lote.lote as lote',
            'lote.inscricao_municipal as inscricao_municipal',
            'lote.data_venda as data_venda',
            'lote.negativar as negativar',
            'lote.quadra_id as lote_quadra_id',
            'lote.cliente_id as lote_cliente_id',
            'cliente.nome as nome_cliente',
            'cliente.razao_social as razao_social__cliente',
            'cliente.telefone1 as tel1',
            'cliente.telefone2 as tel2',
        )
        ->leftJoin('lote', 'quadra.id', '=', 'lote.quadra_id')
        ->join('cliente', 'cliente.id', '=', 'lote.cliente_id')
        ->where('quadra.empreendimento_id', '=', $id)
        ->orderByRaw('CAST(SUBSTRING_INDEX(quadra.nome, " ", -1) AS UNSIGNED), CAST(lote.lote AS UNSIGNED)')
        ->get();

        $total_lotes = $resultado->count();

        return view('empreendimento/empreendimento_gestao', compact('resultado', 'total_lotes'), compact('empreendimento') );
    }
}