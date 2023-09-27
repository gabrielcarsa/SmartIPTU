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
    function contas_receber_listagem(Request $request){
        $titular_conta_id = $request->input('titular_conta_id');
        $resultadosTotal = Parcela::all();
        $total = $resultadosTotal->count();

        if ($titular_conta_id == 0) {
            $resultadosDebitos = DB::table('parcela as p')
            ->select(
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
                'd.id as debito_id',
                'd.tipo_debito_id as tipo_debito_id',
                'd.quantidade_parcela as debito_quantidade_parcela',
                'd.descricao_debito_id as debito_descricao_debito_id',  
                'dd.descricao as descricao',  
                'td.id as id_titular_conta',
                'td.cliente_id as titular_conta_cliente_id',
                'c.nome as nome',
                'c.razao_social as razao_social',
                'c.tipo_cadastro as cliente_tipo_cadastro',
                'c.id as id_cliente',     
                'tpd.id as id_tipo_debito',
                'tpd.descricao as tipo_debito_descricao', 
                'l.id as id_lote',
                'l.lote as lote',
                'l.inscricao_municipal as inscricao',
                'e.nome as empreendimento',
                'q.nome as quadra',
                'uc.name as cadastrado_por',
                DB::raw('COALESCE(ua.name) as alterado_por'),
                DB::raw('COALESCE(ub.name) as baixado_por')
            )
            ->join('debito as d', 'p.debito_id', '=', 'd.id')
            ->join('lote as l', 'd.lote_id', '=', 'l.id')
            ->join('quadra as q', 'l.quadra_id', '=', 'q.id')
            ->join('empreendimento as e', 'q.empreendimento_id', '=', 'e.id')
            ->join('cliente as c', 'l.cliente_id', '=', 'c.id')
            ->join('descricao_debito as dd', 'd.descricao_debito_id', '=', 'dd.id')
            ->join('titular_conta as td', 'd.titular_conta_id', '=', 'td.id')
            ->join('tipo_debito as tpd', 'd.tipo_debito_id', '=', 'tpd.id')
            ->join('users as uc', 'uc.id', '=', 'p.cadastrado_usuario_id') // Usuario que cadastrou a parcela
            ->leftJoin('users as ua', 'ua.id', '=', 'p.alterado_usuario_id') // Usuário que alterou, usando LEFT JOIN para permitir nulos
            ->leftJoin('users as ub', 'ub.id', '=', 'p.usuario_baixa_id') // Usuário que baixou, usando LEFT JOIN para permitir nulos
            ->whereColumn('l.cliente_id', '<>', 'td.cliente_id')
            ->orderBy('data_vencimento', 'ASC') 
            ->get();

            $resultadosContasReceber = DB::table('parcela_conta_receber as p')
            ->select(
                'p.id as id_parcela_conta_receber',
                'p.numero_parcela as numero_parcela_conta_receber',
                'p.data_vencimento as data_vencimento_conta_receber',
                'p.valor_parcela as valor_parcela_conta_receber',
                'p.situacao as situacao_parcela_conta_receber',
                'p.valor_pago as parcela_valor_pago_conta_receber',
                'p.data_recebimento as data_recebimento_conta_receber',
                'p.data_baixa as data_baixa_conta_receber',
                'p.cadastrado_usuario_id as parcela_cadastrado_usuario_id_conta_receber',
                'p.alterado_usuario_id as parcela_alterado_usuario_id_conta_receber',
                'p.usuario_baixa_id as parcela_usuario_baixa_id_conta_receber',
                'p.data_alteracao as parcela_data_alteracao_conta_receber',
                'cr.id as id_conta_receber',
                'cr.cliente_id as conta_receber_cliente_id',
                'cr.quantidade_parcela as conta_receber_quantidade_parcela',
                'cr.titular_conta_id as conta_receber_titular_conta_id',  
                'ctr.descricao as descricao_conta_receber',  
                'td.id as id_titular_conta_conta_receber',
                'td.cliente_id as titular_conta_cliente_id_conta_receber',
                'c.nome as nome_conta_receber',
                'c.razao_social as razao_social_conta_receber',
                'c.tipo_cadastro as cliente_tipo_cadastro_conta_receber',
                'c.id as id_cliente_conta_receber',     
                'uc.name as cadastrado_por_conta_receber',
                DB::raw('COALESCE(ua.name) as alterado_por_conta_receber'),
                DB::raw('COALESCE(ub.name) as baixado_por_conta_receber')
            )
            ->join('conta_receber as cr', 'p.conta_receber_id', '=', 'cr.id')
            ->join('cliente as c', 'cr.cliente_id', '=', 'c.id')
            ->join('categoria_receber as ctr', 'cr.categoria_receber_id', '=', 'ctr.id')
            ->join('titular_conta as td', 'cr.titular_conta_id', '=', 'td.id')
            ->join('users as uc', 'uc.id', '=', 'p.cadastrado_usuario_id') // Usuario que cadastrou a parcela
            ->leftJoin('users as ua', 'ua.id', '=', 'p.alterado_usuario_id') // Usuário que alterou, usando LEFT JOIN para permitir nulos
            ->leftJoin('users as ub', 'ub.id', '=', 'p.usuario_baixa_id') // Usuário que baixou, usando LEFT JOIN para permitir nulos
            ->get();

        }else {
            $resultadosDebitos = DB::table('parcela as p')
            ->select(
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
                'd.id as debito_id',
                'd.tipo_debito_id as tipo_debito_id',
                'd.quantidade_parcela as debito_quantidade_parcela',
                'd.descricao_debito_id as debito_descricao_debito_id',  
                'dd.descricao as descricao',  
                'td.id as id_titular_conta',
                'td.cliente_id as titular_conta_cliente_id',
                'c.nome as nome',
                'c.razao_social as razao_social',
                'c.tipo_cadastro as cliente_tipo_cadastro',
                'c.id as id_cliente',     
                'tpd.id as id_tipo_debito',
                'tpd.descricao as tipo_debito_descricao', 
                'l.id as id_lote',
                'l.lote as lote',
                'l.inscricao_municipal as inscricao',
                'e.nome as empreendimento',
                'q.nome as quadra',
                'uc.name as cadastrado_por',
                DB::raw('COALESCE(ua.name) as alterado_por'),
                DB::raw('COALESCE(ub.name) as baixado_por')
            )
            ->join('debito as d', 'p.debito_id', '=', 'd.id')
            ->join('lote as l', 'd.lote_id', '=', 'l.id')
            ->join('quadra as q', 'l.quadra_id', '=', 'q.id')
            ->join('empreendimento as e', 'q.empreendimento_id', '=', 'e.id')
            ->join('cliente as c', 'l.cliente_id', '=', 'c.id')
            ->join('descricao_debito as dd', 'd.descricao_debito_id', '=', 'dd.id')
            ->join('titular_conta as td', 'd.titular_conta_id', '=', 'td.id')
            ->join('tipo_debito as tpd', 'd.tipo_debito_id', '=', 'tpd.id')
            ->join('users as uc', 'uc.id', '=', 'p.cadastrado_usuario_id') // Usuario que cadastrou a parcela
            ->leftJoin('users as ua', 'ua.id', '=', 'p.alterado_usuario_id') // Usuário que alterou, usando LEFT JOIN para permitir nulos
            ->leftJoin('users as ub', 'ub.id', '=', 'p.usuario_baixa_id') // Usuário que baixou, usando LEFT JOIN para permitir nulos
            ->where('d.titular_conta_id', $titular_conta_id)
            ->whereColumn('l.cliente_id', '<>', 'td.cliente_id')
            ->orderBy('data_vencimento', 'ASC') 
            ->get();
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
        
        // Combine as duas coleções
        $resultados = $resultadosDebitos->concat($resultadosContasReceber);

        $data = [
            'resultadosDebitos' => $resultadosDebitos,
            'resultadosContasReceber' => $resultadosContasReceber,
            'total' => $total,
        ];

        return view('parcela/parcela_contas_receber', compact('titular_conta', 'data'));
    }
}