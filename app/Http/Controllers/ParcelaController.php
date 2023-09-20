<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Parcela;
use App\Models\Debito;

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
                    'd.id as debito_id',
                    'd.quantidade_parcela as debito_quantidade_parcela',
                    'd.descricao_debito_id as debito_descricao_debito_id',  
                    'dd.descricao as descricao',       
                )
                ->leftJoin('debito AS d', 'p.debito_id', '=', 'd.id')
                ->leftJoin('descricao_debito AS dd', 'd.descricao_debito_id', '=', 'dd.id')
                ->where('p.id', $parcelaId)
                ->get();
                return view('parcela/parcela_reajustar', compact('parcelas'));
            }
        }else{
            return redirect()->back()->with('error', 'Nenhuma parcela selecionada!');
        }
    }

    function reajustar($user_id, Request $request){

        $idParcelas = $request->input('checkboxes');

        // Converta os valores dos checkboxes em um array
        $idParcelas = explode(',', $idParcelas); 
        
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
}