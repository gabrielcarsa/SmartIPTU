<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TitularConta;
use App\Models\ContaReceber;
use App\Models\ParcelaContaReceber;
use App\Models\Cliente;
use App\Models\Parcela;
use Carbon\Carbon;
use App\Models\CategoriaReceber;
use App\Http\Requests\ContaReceberRequest;
use Illuminate\Support\Facades\DB;


class ContaReceberController extends Controller
{
    //RETORNA VIEW PARA CADASTRO DE NOVA CONTA A RECEBER
    function conta_receber_novo(){
        $titular_conta = DB::table('titular_conta as t')
        ->select(
            't.id as id_titular_conta',
            't.cliente_id as cliente_id',
            'c.nome as nome',
            'c.razao_social as razao_social',
        )
        ->leftJoin('cliente AS c', 'c.id', '=', 't.cliente_id')
        ->get();

        $categorias = CategoriaReceber::all();

        $clientes = Cliente::all();

        $data = [
            'titular_conta' => $titular_conta,
            'categorias' =>$categorias,
            'clientes' => $clientes,
        ];
        return view('conta_receber/conta_receber_novo', compact('data'));
    }

     //CADASTRO DE CONTA A RECEBER
     function cadastrar($usuario, Request $request){
        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');    

        $contaReceber = new ContaReceber();
        $contaReceber->titular_conta_id = $request->input('titular_conta_id');
        $contaReceber->cliente_id = $request->input('cliente_id');
        $contaReceber->categoria_receber_id = $request->input('categoria_receber_id');
        $contaReceber->quantidade_parcela = $request->input('quantidade_parcela');
        $contaReceber->data_vencimento = $request->input('data_vencimento');
        $contaReceber->valor_parcela = $request->input('valor_parcela');
        $contaReceber->valor_entrada = $request->input('valor_entrada');
        $contaReceber->observacao = $request->input('observacao');
        $contaReceber->data_cadastro = date('d-m-Y h:i:s a', time());
        $contaReceber->cadastrado_usuario_id = $usuario;
        $contaReceber->save();

        // Cadastrar Parcelas
        $qtd_parcelas = $request->input('quantidade_parcela');
        $contaReceber_id = $contaReceber->id;
        $data_vencimento = $contaReceber->data_vencimento; 
        $dataCarbon = Carbon::createFromFormat('Y-m-d', $data_vencimento);
        $valor_entrada = $contaReceber->valor_entrada;

        for($i = 1; $i <= $qtd_parcelas; $i++){
            $parcela = new ParcelaContaReceber();
            $parcela->conta_receber_id = $contaReceber_id;
            $parcela->numero_parcela = $i;
            $parcela->valor_parcela = $contaReceber->valor_parcela;
            $parcela->cadastrado_usuario_id = $usuario;
            if($i > 1){
                $parcela->data_vencimento = $dataCarbon->addMonth();
            }else{
                if($valor_entrada != 0){
                    $parcela->valor_parcela = $valor_entrada;
                }
                $parcela->data_vencimento = $data_vencimento;
            }
            $parcela->save();
        }

        return redirect('contas_receber')->with('success', 'Nova receita cadastrada com sucesso');
    }

    //VIEW PARA RETORNAR FINANCEIRO CONTAS A RECEBER
    function contas_receber(){
        $titular_conta = DB::table('titular_conta as t')
        ->select(
            't.id as id_titular_conta',
            't.cliente_id as cliente_id',
            'c.nome as nome',
            'c.razao_social as razao_social',
        )
        ->leftJoin('cliente AS c', 'c.id', '=', 't.cliente_id')
        ->get();

        return view('parcela/parcela_contas_receber', compact('titular_conta'));
    }

    //LISTAGEM E FILTRO CONTAS A RECEBER
    function contas_receber_listagem(ContaReceberRequest $request){
        $titular_conta_id = $request->input('titular_conta_id');
        $isReferenteLotes = $request->input('refenteLotes');
        $isReferenteOutros = $request->input('refenteOutros');

        //select referente a parcelas de contas a receber de lotes
        $queryReferenteLotes = [
            'p.id as id',
            'p.numero_parcela as numero_parcela',
            'p.data_vencimento as data_vencimento',
            'p.valor_parcela as valor_parcela',
            'p.situacao as situacao_parcela',
            'p.valor_pago as parcela_valor_pago',
            'p.data_recebimento as data_recebimento',
            'p.data_baixa as data_baixa',
            'p.cadastrado_usuario_id as parcela_cadastrado_usuario_id',
            'p.alterado_usuario_id as parcela_alterado_usuario_id',
            'p.usuario_baixa_id as parcela_usuario_baixa_id',
            'p.data_alteracao as parcela_data_alteracao',
            'd.quantidade_parcela as quantidade_parcela',
            'dd.descricao as descricao',  
            'c.nome as nome',
            'c.tipo_cadastro as tipo_cadastro',
            'c.razao_social as razao_social',
            'tpd.descricao as tipo_debito_descricao', 
            'l.lote as lote',
            'l.inscricao_municipal as inscricao',
            'e.nome as empreendimento',
            'q.nome as quadra',
            'uc.name as cadastrado_por',
            DB::raw('COALESCE(ua.name) as alterado_por'),
            DB::raw('COALESCE(ub.name) as baixado_por')
        ];

        //select referente a parcelas de outras contas a receber
        $queryReferenteOutros = [
            'p.id as id',
            'p.numero_parcela as numero_parcela',
            'p.data_vencimento as data_vencimento',
            'p.valor_parcela as valor_parcela',
            'p.situacao as situacao_parcela',
            'p.valor_pago as parcela_valor_pago',
            'p.data_recebimento as data_recebimento',
            'p.data_baixa as data_baixa',
            'p.cadastrado_usuario_id as parcela_cadastrado_usuario_id',
            'p.alterado_usuario_id as parcela_alterado_usuario_id',
            'p.usuario_baixa_id as parcela_usuario_baixa_id',
            'p.data_alteracao as parcela_data_alteracao',
            'cr.quantidade_parcela as quantidade_parcela',
            'ctr.descricao as descricao',
            'c.nome as nome',
            'c.tipo_cadastro as tipo_cadastro',
            'c.razao_social as razao_social',
            'uc.name as cadastrado_por',
            DB::raw('COALESCE(ua.name) as alterado_por'),
            DB::raw('COALESCE(ub.name) as baixado_por'),
        ];

        //Referente a Lotes
        if ($isReferenteLotes) {
            if($titular_conta_id == 0){
                $resultados = DB::table('parcela as p')
                ->select($queryReferenteLotes)
                ->selectRaw('CASE WHEN titular_conta_cliente.razao_social IS NOT NULL THEN titular_conta_cliente.razao_social ELSE titular_conta_cliente.nome END AS nome_cliente_ou_razao_social')
                ->join('debito as d', 'p.debito_id', '=', 'd.id')
                ->join('lote as l', 'd.lote_id', '=', 'l.id')
                ->join('quadra as q', 'l.quadra_id', '=', 'q.id')
                ->join('empreendimento as e', 'q.empreendimento_id', '=', 'e.id')
                ->join('cliente as c', 'l.cliente_id', '=', 'c.id')
                ->join('descricao_debito as dd', 'd.descricao_debito_id', '=', 'dd.id')
                ->join('titular_conta as td', 'd.titular_conta_id', '=', 'td.id')
                ->join('tipo_debito as tpd', 'd.tipo_debito_id', '=', 'tpd.id')
                ->leftJoin('cliente AS titular_conta_cliente', 'td.cliente_id', '=', 'titular_conta_cliente.id')
                ->join('users as uc', 'uc.id', '=', 'p.cadastrado_usuario_id') // Usuario que cadastrou a parcela
                ->leftJoin('users as ua', 'ua.id', '=', 'p.alterado_usuario_id') // Usuário que alterou, usando LEFT JOIN para permitir nulos
                ->leftJoin('users as ub', 'ub.id', '=', 'p.usuario_baixa_id') // Usuário que baixou, usando LEFT JOIN para permitir nulos
                ->whereColumn('l.cliente_id', '<>', 'td.cliente_id')
                ->orderBy('data_vencimento', 'ASC') 
                ->get();
            }else{ //Referente a Outras Receitas
                $resultados = DB::table('parcela as p')
                ->select($queryReferenteLotes)
                ->selectRaw('CASE WHEN titular_conta_cliente.razao_social IS NOT NULL THEN titular_conta_cliente.razao_social ELSE titular_conta_cliente.nome END AS nome_cliente_ou_razao_social')
                ->join('debito as d', 'p.debito_id', '=', 'd.id')
                ->join('lote as l', 'd.lote_id', '=', 'l.id')
                ->join('quadra as q', 'l.quadra_id', '=', 'q.id')
                ->join('empreendimento as e', 'q.empreendimento_id', '=', 'e.id')
                ->join('cliente as c', 'l.cliente_id', '=', 'c.id')
                ->join('descricao_debito as dd', 'd.descricao_debito_id', '=', 'dd.id')
                ->join('titular_conta as td', 'd.titular_conta_id', '=', 'td.id')
                ->join('tipo_debito as tpd', 'd.tipo_debito_id', '=', 'tpd.id')
                ->leftJoin('cliente AS titular_conta_cliente', 'td.cliente_id', '=', 'titular_conta_cliente.id')
                ->join('users as uc', 'uc.id', '=', 'p.cadastrado_usuario_id') // Usuario que cadastrou a parcela
                ->leftJoin('users as ua', 'ua.id', '=', 'p.alterado_usuario_id') // Usuário que alterou, usando LEFT JOIN para permitir nulos
                ->leftJoin('users as ub', 'ub.id', '=', 'p.usuario_baixa_id') // Usuário que baixou, usando LEFT JOIN para permitir nulos
                ->where('d.titular_conta_id', $titular_conta_id)
                ->whereColumn('l.cliente_id', '<>', 'td.cliente_id')
                ->orderBy('data_vencimento', 'ASC') 
                ->get();
            }
        } else {
            if($titular_conta_id == 0){
                $resultados = DB::table('parcela_conta_receber as p')
                ->select($queryReferenteOutros)
                ->selectRaw('CASE WHEN titular_conta_cliente.razao_social IS NOT NULL THEN titular_conta_cliente.razao_social ELSE titular_conta_cliente.nome END AS nome_cliente_ou_razao_social')
                ->join('conta_receber as cr', 'p.conta_receber_id', '=', 'cr.id')
                ->join('cliente as c', 'cr.cliente_id', '=', 'c.id')
                ->join('categoria_receber as ctr', 'cr.categoria_receber_id', '=', 'ctr.id')
                ->join('titular_conta as td', 'cr.titular_conta_id', '=', 'td.id')
                ->join('users as uc', 'uc.id', '=', 'p.cadastrado_usuario_id')
                ->leftJoin('users as ua', 'ua.id', '=', 'p.alterado_usuario_id')
                ->leftJoin('users as ub', 'ub.id', '=', 'p.usuario_baixa_id')
                ->leftJoin('cliente AS titular_conta_cliente', 'td.cliente_id', '=', 'titular_conta_cliente.id')
                ->orderBy('p.data_vencimento', 'ASC')
                ->get();
            }else{
                $resultados = DB::table('parcela_conta_receber as p')
                ->select($queryReferenteOutros)
                ->selectRaw('CASE WHEN titular_conta_cliente.razao_social IS NOT NULL THEN titular_conta_cliente.razao_social ELSE titular_conta_cliente.nome END AS nome_cliente_ou_razao_social')
                ->join('conta_receber as cr', 'p.conta_receber_id', '=', 'cr.id')
                ->join('cliente as c', 'cr.cliente_id', '=', 'c.id')
                ->join('categoria_receber as ctr', 'cr.categoria_receber_id', '=', 'ctr.id')
                ->join('titular_conta as td', 'cr.titular_conta_id', '=', 'td.id')
                ->join('users as uc', 'uc.id', '=', 'p.cadastrado_usuario_id')
                ->leftJoin('users as ua', 'ua.id', '=', 'p.alterado_usuario_id')
                ->leftJoin('users as ub', 'ub.id', '=', 'p.usuario_baixa_id')
                ->leftJoin('cliente AS titular_conta_cliente', 'td.cliente_id', '=', 'titular_conta_cliente.id')
                ->where('cr.titular_conta_id', $titular_conta_id)
                ->orderBy('p.data_vencimento', 'ASC')
                ->get();
            }      
        } 

        $titular_conta = DB::table('titular_conta as t')
        ->select(
            't.id as id_titular_conta',
            't.cliente_id as cliente_id',
            'c.nome as nome',
            'c.razao_social as razao_social',
        )
        ->leftJoin('cliente AS c', 'c.id', '=', 't.cliente_id')
        ->get();
    
        $data = [
            'resultados' => $resultados,
            'isReferenteLotes' => $isReferenteLotes, 
        ];
    
        return view('parcela/parcela_contas_receber', compact('titular_conta', 'data'));
    }
}