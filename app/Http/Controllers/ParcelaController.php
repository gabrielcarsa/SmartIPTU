<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Parcela;
use App\Models\Debito;
use App\Models\TitularDebito;
use App\Http\Requests\ParcelaRequest;
use Carbon\Carbon;

class ParcelaController extends Controller
{
    //RETORNA VIEW PARA REAJUSTAR PARCELA
    function reajustar_view(Request $request){
       
        // Verifique se a chave 'checkboxes' está presente na requisição
        if ($request->has('checkboxes') && $request->filled('checkboxes')) {
             // Recupere os valores dos checkboxes da consulta da URL
            $checkboxesSelecionados = $request->input('checkboxes');

            // Converta os valores dos checkboxes em um array
            $checkboxesSelecionados = explode(',', $checkboxesSelecionados); 

    
            $parcelas = [];
            foreach ($checkboxesSelecionados as $parcelaId) {
                $parcelas[] = DB::table('parcela as p')
                ->select(
                    'p.id as id',
                    'p.numero_parcela as numero_parcela',
                    'p.data_vencimento as data_vencimento',
                    'p.valor_parcela as valor_parcela',
                    'p.situacao as situacao_parcela',
                    'd.id as debito_id',
                    'd.quantidade_parcela as debito_quantidade_parcela',
                    'd.descricao_debito_id as debito_descricao_debito_id',  
                    'dd.descricao as descricao',       
                )
                ->leftJoin('debito AS d', 'p.debito_id', '=', 'd.id')
                ->leftJoin('descricao_debito AS dd', 'd.descricao_debito_id', '=', 'dd.id')
                ->where('p.id', $parcelaId)
                ->get();
            }
            return view('parcela/parcela_reajustar', compact('parcelas'));

        }else{
            return redirect()->back()->with('error', 'Nenhuma parcela selecionada!');
        }
    }

    //REAJUSTAR PARCELAS
    function reajustar($user_id, Request $request){

        $validated = $request->validate([
            'valor_unico' => 'required|numeric|min:0.1',
        ]);

        $idParcelas = $request->get('id_parcela', []);

        foreach($idParcelas as $p){
            $parcela = Parcela::find($p);
            $parcela->valor_parcela = $request->input('valor_unico');
            $parcela->save();
        }
        $parcelaReferencia = Parcela::find($idParcelas[0]);
        $debito = Debito::find($parcelaReferencia->debito_id);
        $lote_id = $debito->lote_id;
        return redirect("lote/gestao/".$lote_id)->with('success', 'Parcelas reajustadas com sucesso');   
    }

      //RETORNA VIEW PARA ALTERAR DATA DE VENCIMENTO
      function alterar_vencimento(Request $request){
       
        // Verifique se a chave 'checkboxes' está presente na requisição
        if ($request->has('checkboxes') && $request->filled('checkboxes')) {
             // Recupere os valores dos checkboxes da consulta da URL
            $checkboxesSelecionados = $request->input('checkboxes');

            // Converta os valores dos checkboxes em um array
            $checkboxesSelecionados = explode(',', $checkboxesSelecionados); 

    
            $parcelas = [];
            foreach ($checkboxesSelecionados as $parcelaId) {
                $parcelas[] = DB::table('parcela as p')
                ->select(
                    'p.id as id',
                    'p.numero_parcela as numero_parcela',
                    'p.data_vencimento as data_vencimento',
                    'p.valor_parcela as valor_parcela',
                    'p.situacao as situacao_parcela',
                    'd.id as debito_id',
                    'd.quantidade_parcela as debito_quantidade_parcela',
                    'd.descricao_debito_id as debito_descricao_debito_id',  
                    'dd.descricao as descricao',       
                )
                ->leftJoin('debito AS d', 'p.debito_id', '=', 'd.id')
                ->leftJoin('descricao_debito AS dd', 'd.descricao_debito_id', '=', 'dd.id')
                ->where('p.id', $parcelaId)
                ->get();
            }
            return view('parcela/parcela_alterar_vencimento', compact('parcelas'));

        }else{
            return redirect()->back()->with('error', 'Nenhuma parcela selecionada!');
        }
    }

    //ALTERAR DATA DE VENCIMENTO
    function definir_alteracao_data($user_id, ParcelaRequest $request){

        $validated = $request->validate([
            'data_vencimento' => 'required|date',
        ]);

        $idParcelas = $request->get('id_parcela', []);

        $data_vencimento = $request->input('data_vencimento'); 
        $dataCarbon = Carbon::createFromFormat('Y-m-d', $data_vencimento);
        $i = 0;
        foreach($idParcelas as $p){
            $parcela = Parcela::find($p);
            if($i > 0){
                $parcela->data_vencimento = $dataCarbon->addMonth();
            }else{
                $parcela->data_vencimento = $data_vencimento;
            }
            $parcela->save();
            $i++;
        }
        $parcelaReferencia = Parcela::find($idParcelas[0]);
        $debito = Debito::find($parcelaReferencia->debito_id);
        $lote_id = $debito->lote_id;
        return redirect("lote/gestao/".$lote_id)->with('success', 'Data(s) de vencimento alteradas com sucesso');   
    }

     //RETORNA VIEW PARA BAIXAR PARCELA
     function baixar_parcela_view(Request $request){
       
        // Verifique se a chave 'checkboxes' está presente na requisição
        if ($request->has('checkboxes') && $request->filled('checkboxes')) {
             // Recupere os valores dos checkboxes da consulta da URL
            $checkboxesSelecionados = $request->input('checkboxes');

            // Converta os valores dos checkboxes em um array
            $checkboxesSelecionados = explode(',', $checkboxesSelecionados); 

            $parcelas = [];
            foreach ($checkboxesSelecionados as $parcelaId) {
                $parcelas[] = DB::table('parcela as p')
                ->select(
                    'p.id as id',
                    'p.numero_parcela as numero_parcela',
                    'p.data_vencimento as data_vencimento',
                    'p.valor_parcela as valor_parcela',
                    'p.situacao as situacao_parcela',
                    'd.id as debito_id',
                    'd.quantidade_parcela as debito_quantidade_parcela',
                    'd.descricao_debito_id as debito_descricao_debito_id',  
                    'dd.descricao as descricao',       
                )
                ->leftJoin('debito AS d', 'p.debito_id', '=', 'd.id')
                ->leftJoin('descricao_debito AS dd', 'd.descricao_debito_id', '=', 'dd.id')
                ->where('p.id', $parcelaId)
                ->get();
            }
            return view('parcela/parcela_baixar', compact('parcelas'));

        }else{
            return redirect()->back()->with('error', 'Nenhuma parcela selecionada!');
        }
    }

    //BAIXAR PARCELAS
    function definir_baixar_parcela($user_id, Request $request){
        $validated = $request->validate([
            'data_recebimento.*' => 'required|date',
            'valor_pago.*' => 'required|numeric|min:0.1',
        ]);

        $idParcelas = $request->get('id_parcela', []);
        $valorPago = $request->get('valor_pago', []);
        $dataRecebimento = $request->get('data_recebimento', []);
    
        $i = 0;
        foreach ($idParcelas as $id) {
            // Process each $id here
            $parcela = Parcela::find($id);
            $parcela->valor_pago = $valorPago[$i];
            $parcela->data_recebimento = $dataRecebimento[$i];
            $parcela->situacao = 1;
            $parcela->save();
            $i++;
        }
       
        $parcelaReferencia = Parcela::find($idParcelas[0]);
        $debito = Debito::find($parcelaReferencia->debito_id);
        $lote_id = $debito->lote_id;
        return redirect("lote/gestao/".$lote_id)->with('success', 'Parcelas baixadas com sucesso');

   
    }

    //VIEW PARA RETORNAR FINANCEIRO CONTAS A RECEBER
    function contas_receber(){
        $titular_debito = DB::table('titular_debito as t')
        ->select(
            't.id as id_titular_debito',
            't.cliente_id as cliente_id',
            'c.nome as nome',
            'c.razao_social as razao_social',
        )
        ->leftJoin('cliente AS c', 'c.id', '=', 't.cliente_id')
        ->get();

        return view('parcela/parcela_contas_receber', compact('titular_debito'));
    }

    function contas_receber_listagem(Request $request){
        $titular_debito_id = $request->input('titular_debito_id');
        $resultados = Parcela::all();
        $total = $resultados->count();

        if ($titular_debito_id == 0) {
            $resultados = DB::table('parcela as p')
            ->select(
                'p.id as id',
                'p.numero_parcela as numero_parcela',
                'p.data_vencimento as data_vencimento',
                'p.valor_parcela as valor_parcela',
                'p.situacao as situacao_parcela',
                'd.id as debito_id',
                'd.tipo_debito_id as tipo_debito_id',
                'd.quantidade_parcela as debito_quantidade_parcela',
                'd.descricao_debito_id as debito_descricao_debito_id',  
                'dd.descricao as descricao',  
                'td.id as id_titular_debito',
                'td.cliente_id as titular_debito_cliente_id',
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
                'q.nome as quadra'
            )
            ->join('debito as d', 'p.debito_id', '=', 'd.id')
            ->join('lote as l', 'd.lote_id', '=', 'l.id')
            ->join('quadra as q', 'l.quadra_id', '=', 'q.id')
            ->join('empreendimento as e', 'q.empreendimento_id', '=', 'e.id')
            ->join('cliente as c', 'l.cliente_id', '=', 'c.id')
            ->join('descricao_debito as dd', 'd.descricao_debito_id', '=', 'dd.id')
            ->join('titular_debito as td', 'd.titular_debito_id', '=', 'td.id')
            ->join('tipo_debito as tpd', 'd.tipo_debito_id', '=', 'tpd.id')
            ->get();
        }else if($titular_debito_id == 1) {
            $resultados = DB::table('parcela as p')
            ->select(
                'p.id as id',
                'p.numero_parcela as numero_parcela',
                'p.data_vencimento as data_vencimento',
                'p.valor_parcela as valor_parcela',
                'p.situacao as situacao_parcela',
                'd.id as debito_id',
                'd.tipo_debito_id as tipo_debito_id',
                'd.quantidade_parcela as debito_quantidade_parcela',
                'd.descricao_debito_id as debito_descricao_debito_id',  
                'dd.descricao as descricao',  
                'td.id as id_titular_debito',
                'td.cliente_id as titular_debito_cliente_id',
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
                'q.nome as quadra'

            )
            ->join('debito as d', 'p.debito_id', '=', 'd.id')
            ->join('lote as l', 'd.lote_id', '=', 'l.id')
            ->join('quadra as q', 'l.quadra_id', '=', 'q.id')
            ->join('empreendimento as e', 'q.empreendimento_id', '=', 'e.id')
            ->join('cliente as c', 'l.cliente_id', '=', 'c.id')
            ->join('descricao_debito as dd', 'd.descricao_debito_id', '=', 'dd.id')
            ->join('titular_debito as td', 'd.titular_debito_id', '=', 'td.id')
            ->join('tipo_debito as tpd', 'd.tipo_debito_id', '=', 'tpd.id')
            ->where('d.titular_debito_id', $titular_debito_id)
            ->whereColumn('l.cliente_id', '<>', 'td.cliente_id')
            ->get();
        }

        $titular_debito = DB::table('titular_debito as t')
        ->select(
            't.id as id_titular_debito',
            't.cliente_id as cliente_id',
            'c.nome as nome',
            'c.razao_social as razao_social',
        )
        ->leftJoin('cliente AS c', 'c.id', '=', 't.cliente_id')
        ->get();

        $data = [
            'resultados' => $resultados,
            'total' => $total,
        ];

        return view('parcela/parcela_contas_receber', compact('titular_debito', 'data'));
    }
}