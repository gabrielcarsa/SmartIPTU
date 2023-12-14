<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lote;
use App\Models\Quadra;
use App\Models\User;
use App\Models\Debito;
use App\Models\TitularConta;
use App\Models\Cliente;
use App\Http\Requests\LoteRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class LoteController extends Controller
{
     //RETORNA VIEW PARA ADICIONAR LOTE
     function novo($empreendimento_id){
        $quadras = Quadra::where('empreendimento_id', $empreendimento_id)
        ->select('id as quadra_id', 'nome as quadra_nome')
        ->get();
        //$quadras = Quadra::select('quadra.id as quadra_id, quadra_nome')->where('quadra.empreendimento_id', '=', $empreendimento_id);
        $clientes = Cliente::orderBy('nome')->get();
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
        $lote->metragem_esquina = $request->input('metragem_esquina');
        $lote->confrontacao_frente = $request->input('confrontacao_frente');
        $lote->confrontacao_fundo = $request->input('confrontacao_fundo');
        $lote->confrontacao_direita = $request->input('confrontacao_direita');
        $lote->confrontacao_esquerda = $request->input('confrontacao_esquerda');
        $lote->data_cadastro = Carbon::now()->format('Y-m-d H:i:s');
        $lote->cadastrado_usuario_id = $usuario;
        $lote->save();
        return redirect('empreendimento/gestao/'.$empreendimento_id)->with('success', 'Lote cadastrado com sucesso');
    }

     //RETORNA VIEW ALTERAR LOTE      
     function editar($id){
        $lote = Lote::find($id);
        
        $cliente_nome = Cliente::select('nome as nome_cliente', 'razao_social as razao_social_cliente')
        ->where('id', $lote->cliente_id)
        ->first();

        $quadra_nome = Quadra::where('id', $lote->quadra_id)->first();

        $cadastrado_por_user_id = $lote['cadastrado_usuario_id'];
        $alterado_por_user_id = $lote['alterado_usuario_id'];

        $cadastrado_por_user = User::find($cadastrado_por_user_id);
        $alterado_por_user = User::find($alterado_por_user_id);

        $clientes = Cliente::orderBy('nome')->get();

        $data = [
            'cliente_nome' => $cliente_nome,
            'quadra_nome' => $quadra_nome,
            'cadastrado_por_user' => $cadastrado_por_user,
            'alterado_por_user' => $alterado_por_user,
        ];

        return view('lote/lote_novo', compact('data', 'lote'), compact('clientes'));
    }

    //ALTERAR LOTE
    function alterar($id, $usuario, LoteRequest $request){
        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');

        $lote = Lote::find($id);

        if (!$lote) {
            return redirect()->back()->with('error', 'Lote não encontrado');
        }

        $lote->lote = $request->input('lote');
        $lote->matricula = $request->input('matricula');
        $lote->cliente_id = $request->input('cliente_id');
        $lote->inscricao_municipal = $request->input('inscricao_municipal');
        $lote->valor = $request->input('valor');
        $lote->endereco = $request->input('endereco');
        $lote->metros_quadrados = $request->input('metros_quadrados');
        $lote->metragem_frente = $request->input('metragem_frente');
        $lote->metragem_fundo = $request->input('metragem_fundo');
        $lote->metragem_direita = $request->input('metragem_direita');
        $lote->metragem_esquerda = $request->input('metragem_esquerda');
        $lote->metragem_esquina = $request->input('metragem_esquina');
        $lote->confrontacao_frente = $request->input('confrontacao_frente');
        $lote->confrontacao_fundo = $request->input('confrontacao_fundo');
        $lote->confrontacao_direita = $request->input('confrontacao_direita');
        $lote->confrontacao_esquerda = $request->input('confrontacao_esquerda');
        $lote->data_alteracao = Carbon::now()->format('Y-m-d H:i:s');
        $lote->alterado_usuario_id = $usuario;
        $lote->save();

        return redirect()->back()->with('success', 'Lote cadastrado com sucesso');

    }

    //EXCLUIR LOTE
    function excluir($id){
        $lote = Lote::find($id);
    
        if (!$lote) {
            return redirect()->back()->with('error', 'Lote não encontrado');
        }
    
        $quadra = Quadra::find($lote->quadra_id);
    
        if (!$quadra) {
            return redirect()->back()->with('error', 'Quadra não encontrada');
        }
    
        $empreendimento_id = $quadra->empreendimento_id;
    
        $lote->delete();
    
        return redirect("empreendimento/gestao/".$empreendimento_id)->with('success', 'Lote excluído com sucesso');
    }

    //RETORNA VIEW PARA GESTÃO DO LOTE
    function gestao($id){
        $empresa = TitularConta::find(1);
        $lote = Lote::find($id);


        $resultadosPagar = DB::table('lote AS l')
        ->select(
            'l.id AS lote_id',
            'l.lote',
            'l.inscricao_municipal',
            'q.id AS quadra_id',
            'q.nome AS quadra_nome',
            'd.id AS debito_id',
            'd.data_cadastro AS debito_data_cadastro',
            'd.data_alteracao AS debito_data_alteracao',
            'd.valor_parcela AS valor_parcela_debito',
            'd.quantidade_parcela AS quantidade_parcela_debito',
            'e.id AS empreendimento_id',
            'e.nome AS empreendimento_nome',
            'td.id AS tipo_debito_id',
            'td.descricao AS tipo_debito_descricao',
            'dd.id AS descricao_debito_id',
            'dd.descricao AS descricao_debito_descricao',
            'p.id AS parcela_id',
            'p.valor_parcela AS valor_parcela',
            'p.valor_pago AS valor_pago_parcela',
            'p.data_pagamento AS data_recebimento_parcela',
            'p.data_vencimento AS data_vencimento_parcela',
            'p.numero_parcela AS numero_parcela',
            'p.situacao AS situacao_parcela',
            'cliente.nome AS nome_cliente',
            'cliente.razao_social AS razao_social_cliente',
            'users_cadastrado.name AS cadastrado_usuario_nome',
            'users_alterado.name AS alterado_usuario_nome'
        )
        ->join('quadra AS q', 'l.quadra_id', '=', 'q.id')
        ->join('empreendimento AS e', 'q.empreendimento_id', '=', 'e.id')
        ->join('cliente', 'cliente.id', '=', 'l.cliente_id') // Corrigido para incluir a tabela cliente
        ->leftJoin('debito AS d', 'l.id', '=', 'd.lote_id')
        ->leftJoin('tipo_debito AS td', 'd.tipo_debito_id', '=', 'td.id')
        ->leftJoin('descricao_debito AS dd', 'd.descricao_debito_id', '=', 'dd.id')
        ->leftJoin('users AS users_cadastrado', 'users_cadastrado.id', '=', 'd.cadastrado_usuario_id')
        ->leftJoin('users AS users_alterado', 'users_alterado.id', '=', 'd.alterado_usuario_id')
        ->join('parcela_conta_pagar AS p', 'd.id', '=', 'p.debito_id')
        ->where('l.id', $id)
        ->orderBy('data_vencimento_parcela', 'ASC') 
        ->get();
     
        $resultadosReceber = DB::table('lote AS l')
        ->select(
            'l.id AS lote_id',
            'l.lote',
            'l.inscricao_municipal',
            'q.id AS quadra_id',
            'q.nome AS quadra_nome',
            'd.id AS debito_id',
            'd.data_cadastro AS debito_data_cadastro',
            'd.data_alteracao AS debito_data_alteracao',
            'd.valor_parcela AS valor_parcela_debito',
            'd.quantidade_parcela AS quantidade_parcela_debito',
            'e.id AS empreendimento_id',
            'e.nome AS empreendimento_nome',
            'td.id AS tipo_debito_id',
            'td.descricao AS tipo_debito_descricao',
            'dd.id AS descricao_debito_id',
            'dd.descricao AS descricao_debito_descricao',
            'p.id AS parcela_id',
            'p.valor_parcela AS valor_parcela',
            'p.valor_recebido AS valor_pago_parcela',
            'p.data_recebimento AS data_recebimento_parcela',
            'p.data_vencimento AS data_vencimento_parcela',
            'p.numero_parcela AS numero_parcela',
            'p.situacao AS situacao_parcela',
            'cliente.nome AS nome_cliente',
            'cliente.razao_social AS razao_social_cliente',
            'users_cadastrado.name AS cadastrado_usuario_nome',
            'users_alterado.name AS alterado_usuario_nome'
        )
        ->join('quadra AS q', 'l.quadra_id', '=', 'q.id')
        ->join('empreendimento AS e', 'q.empreendimento_id', '=', 'e.id')
        ->join('cliente', 'cliente.id', '=', 'l.cliente_id') // Corrigido para incluir a tabela cliente
        ->leftJoin('debito AS d', 'l.id', '=', 'd.lote_id')
        ->leftJoin('tipo_debito AS td', 'd.tipo_debito_id', '=', 'td.id')
        ->leftJoin('descricao_debito AS dd', 'd.descricao_debito_id', '=', 'dd.id')
        ->leftJoin('users AS users_cadastrado', 'users_cadastrado.id', '=', 'd.cadastrado_usuario_id')
        ->leftJoin('users AS users_alterado', 'users_alterado.id', '=', 'd.alterado_usuario_id')
        ->join('parcela_conta_receber AS p', 'd.id', '=', 'p.debito_id')
        ->where('l.id', $id)
        ->orderBy('data_vencimento_parcela', 'ASC') 
        ->get();

   
    
        // Inicialize uma variável para armazenar o valor total
        $totalValorParcelas = 0;

        // Percorra a coleção de resultados
        if($resultadosPagar != null){
            foreach ($resultadosPagar as $resultado) {
                // Verifique se a situação da parcela é igual a 0
                if ($resultado->situacao_parcela == 0) {
                    // Adicione o valor da parcela ao valor total
                    $totalValorParcelas += $resultado->valor_parcela;
                }
            }
        }
       
        if($resultadosReceber != null){
            // Percorra a coleção de resultados
            foreach ($resultadosReceber as $resultado) {
                // Verifique se a situação da parcela é igual a 0
                if ($resultado->situacao_parcela == 0) {
                    // Adicione o valor da parcela ao valor total
                    $totalValorParcelas += $resultado->valor_parcela;
                }
            }

        }
        return view('lote/lote_gestao', compact('resultadosReceber', 'resultadosPagar'), compact('totalValorParcelas'));

    }

    //RETORNA VIEW NOVA LOTE      
    function nova_venda($id){
        $lote = Lote::find($id);
        $clientes = Cliente::all();
        $quadra = Quadra::where('id', $lote->quadra_id)->first();

        $data = [
            'lote' => $lote,
            'quadra' => $quadra,
        ];

        return view('lote/lote_contrato', compact('data', 'clientes'));
    }

    //ALTERAR LOTE
    function cadastrar_venda($id, $usuario, Request $request){
        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');

        $lote = Lote::find($id);

        if (!$lote) {
            return redirect()->back()->with('error', 'Lote não encontrado');
        }

        $lote->data_venda = $request->input('data_contrato');
        $lote->cliente_id = $request->input('cliente_id');
        $lote->data_alteracao = Carbon::now()->format('Y-m-d H:i:s');
        $lote->alterado_usuario_id = $usuario;
        $lote->save();

        return redirect()->back()->with('success', 'Venda cadastrada com sucesso');

    }
}