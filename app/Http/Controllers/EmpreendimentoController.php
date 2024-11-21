<?php

namespace App\Http\Controllers;
use App\Models\Empreendimento;
use App\Models\User;
use App\Models\Quadra;
use App\Models\Lote;
use Illuminate\Http\Request;
use App\Http\Requests\EmpreendimentoRequest;
use Illuminate\Support\Facades\DB;
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
        $hoje = now()->toDateString(); // Obtém a data de hoje no formato 'YYYY-MM-DD'

        $debitosPagarAtrasados =  DB::table('parcela_conta_pagar')
        ->join('debito', 'parcela_conta_pagar.debito_id', '=', 'debito.id')
        ->join('lote', 'debito.lote_id', '=', 'lote.id')
        ->join('quadra', 'lote.quadra_id', '=', 'quadra.id')
        ->join('empreendimento', 'quadra.empreendimento_id', '=', 'empreendimento.id')
        ->whereDate('parcela_conta_pagar.data_vencimento', '<', $hoje)
        ->where('parcela_conta_pagar.situacao', 0)
        ->where('parcela_conta_pagar.debito_id', '!=', null)
        ->where('empreendimento.id', $empreendimento->id)
        ->where('lote.is_escriturado', '!=', true) 
        ->sum('parcela_conta_pagar.valor_parcela');
    
        $debitosReceberAtrasados =  DB::table('parcela_conta_receber')
        ->join('debito', 'parcela_conta_receber.debito_id', '=', 'debito.id')
        ->join('lote', 'debito.lote_id', '=', 'lote.id')
        ->join('quadra', 'lote.quadra_id', '=', 'quadra.id')
        ->join('empreendimento', 'quadra.empreendimento_id', '=', 'empreendimento.id')
        ->whereDate('parcela_conta_receber.data_vencimento', '<', $hoje)
        ->where('parcela_conta_receber.situacao', 0)
        ->where('parcela_conta_receber.debito_id', '!=', null)
        ->where('empreendimento.id', $empreendimento->id) 
        ->where('lote.is_escriturado', '!=', true) 
        ->sum('parcela_conta_receber.valor_parcela');

        
        $resultado = Lote::with('quadra', 'cliente', 'debito')
        ->whereHas('quadra', function ($query) use ($id) {
            $query->where('empreendimento_id', $id);
        })
        ->select('lote.*', 'quadra.nome as quadra_nome') 
        ->join('quadra', 'lote.quadra_id', '=', 'quadra.id')
        ->orderByRaw('CAST(SUBSTRING_INDEX(quadra.nome, " ", -1) AS UNSIGNED)')
        ->orderByRaw('CAST(lote.lote AS UNSIGNED)')
        ->get();

        //Lotes Empresa
        $lotesEmpresa = Lote::where('cliente_id', 1)
        ->whereHas('quadra', function ($query) use ($id) {
            $query->where('empreendimento_id', $id);
        })->count();

        //Lotes Clientes
        $lotesClientes = Lote::where('cliente_id', '!=', 1)
        ->whereHas('quadra', function ($query) use ($id) {
            $query->where('empreendimento_id', $id);
        })->count();

        //Lotes Escriturados
        $lotesEscriturados = Lote::where('is_escriturado', 1)
        ->whereHas('quadra', function ($query) use ($id) {
            $query->where('empreendimento_id', $id);
        })->count();

        $total_lotes = $resultado->count();

        $data = [
            'debitosPagarAtrasados' => $debitosPagarAtrasados,
            'debitosReceberAtrasados' => $debitosReceberAtrasados,
            'empreendimento' => $empreendimento,
            'lotesEmpresa' => $lotesEmpresa,
            'lotesClientes' => $lotesClientes,
            'lotesEscriturados' => $lotesEscriturados,
        ];

        return view('empreendimento/empreendimento_gestao', compact('resultado', 'total_lotes'), compact('data') );
    }
}