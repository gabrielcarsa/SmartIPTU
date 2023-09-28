<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContaPagarController extends Controller
{
    //VIEW PARA RETORNAR FINANCEIRO CONTAS A PAGAR
    function contas_pagar(){
        $titular_conta = DB::table('titular_conta as t')
        ->select(
            't.id as id_titular_conta',
            't.cliente_id as cliente_id',
            'c.nome as nome',
            'c.razao_social as razao_social',
        )
        ->leftJoin('cliente AS c', 'c.id', '=', 't.cliente_id')
        ->get();

        return view('conta_pagar/contas_pagar', compact('titular_conta'));
    }
      
    //LISTAGEM E FILTRO CONTAS A PAGAR
    function contas_pagar_listagem(Request $request){

        //Campos
        $titular_conta_id = $request->input('titular_conta_id');
        $isReferenteLotes = $request->input('refenteLotes');
        $isReferenteOutros = $request->input('refenteOutros');
        $periodoDe = $request->input('periodoDe');
        $periodoAte = $request->input('periodoAte');
        $isPeriodoVencimento = $request->input('periodoVencimento');
        $isPeriodoBaixa = $request->input('periodoBaixa');
        $isPeriodoLancamento = $request->input('periodoLancamento');
        $isPeriodoRecebimento = $request->input('periodoRecebimento');

    

        //select referente a parcelas de contas a pagar de lotes
        $queryReferenteLotes = DB::table('parcela as p')
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
            DB::raw('COALESCE(ub.name) as baixado_por'))
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
        ->whereColumn('l.cliente_id', '=', 'td.cliente_id');

        //select referente a parcelas de outras contas a pagar
        $queryReferenteOutros = DB::table('parcela_conta_pagar as p')
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
            'cp.quantidade_parcela as quantidade_parcela',
            'ctp.descricao as descricao',
            'c.nome as nome',
            'c.tipo_cadastro as tipo_cadastro',
            'c.razao_social as razao_social',
            'uc.name as cadastrado_por',
            DB::raw('COALESCE(ua.name) as alterado_por'),
            DB::raw('COALESCE(ub.name) as baixado_por'),
        )
        ->selectRaw('CASE WHEN titular_conta_cliente.razao_social IS NOT NULL THEN titular_conta_cliente.razao_social ELSE titular_conta_cliente.nome END AS nome_cliente_ou_razao_social')
        ->join('conta_pagar as cp', 'p.conta_pagar_id', '=', 'cp.id')
        ->join('cliente as c', 'cp.cliente_id', '=', 'c.id')
        ->join('categoria_pagar as ctp', 'cp.categoria_pagar_id', '=', 'ctp.id')
        ->join('titular_conta as td', 'cp.titular_conta_id', '=', 'td.id')
        ->join('users as uc', 'uc.id', '=', 'p.cadastrado_usuario_id')
        ->leftJoin('users as ua', 'ua.id', '=', 'p.alterado_usuario_id')
        ->leftJoin('users as ub', 'ub.id', '=', 'p.usuario_baixa_id')
        ->leftJoin('cliente AS titular_conta_cliente', 'td.cliente_id', '=', 'titular_conta_cliente.id');

        
        if ($isReferenteLotes) { //Referente a Lotes

            if($titular_conta_id == 0){ //Se o titular da conta for 'Todos'

                if(!empty($periodoDe) && !empty($periodoAte) && $isPeriodoVencimento){ //Verifica período e Vencimento
                    $resultados = $queryReferenteLotes
                    ->where('p.data_vencimento', '>', $periodoDe)
                    ->where('p.data_vencimento', '<', $periodoAte)
                    ->orderBy('data_vencimento', 'ASC') 
                    ->get();
                }else{ //Busca todos períodos referente a Lotes
                    $resultados = $queryReferenteLotes
                    ->orderBy('data_vencimento', 'ASC') 
                    ->get();
                }

            }else{ //Se o titular da conta for específico

                $resultados = $queryReferenteLotes
                ->where('d.titular_conta_id', $titular_conta_id)
                ->orderBy('data_vencimento', 'ASC') 
                ->get();
            } 
        
        } else { //Referente a outras receitas

            if($titular_conta_id == 0){ //Se o titular da conta for 'Todos'

                if(!empty($periodoDe) && !empty($periodoAte) && $isPeriodoVencimento){ //Verifica período e Vencimento

                    $resultados = $queryReferenteOutros
                    ->where('p.data_vencimento', '>', $periodoDe)
                    ->where('p.data_vencimento', '<', $periodoAte)
                    ->orderBy('data_vencimento', 'ASC') 
                    ->get();

                } else{ //Busca todos períodos referente a Lotes

                    $resultados = $queryReferenteOutros
                    ->orderBy('p.data_vencimento', 'ASC')
                    ->get();
                }

            }else{ //Se o titular da conta for específico

                $resultados = $queryReferenteOutros
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

          // Inicialize uma variável para armazenar o valor total
          $totalValorParcelas = 0;

          // Percorra a coleção de resultados
          foreach ($resultados as $resultado) {
              // Verifique se a situação da parcela é igual a 0
              if ($resultado->situacao_parcela == 0) {
                  // Adicione o valor da parcela ao valor total
                  $totalValorParcelas += $resultado->valor_parcela;
              }
          }
    
        $data = [
            'resultados' => $resultados,
            'isReferenteLotes' => $isReferenteLotes, 
        ];
        
    
        return view('conta_pagar/contas_pagar', compact('titular_conta', 'data'));
    }
}
