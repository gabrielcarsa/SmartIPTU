<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parcela;

class ParcelaController extends Controller
{
    //RETORNA VIEW PARA REAJUSTAR PARCELA
    function reajustar_view(Request $request){
       
        // Verifique se a chave 'checkboxes' está presente na requisição
        if ($request->has('checkboxes')) {
             // Recupere os valores dos checkboxes da consulta da URL
            $checkboxesSelecionados = $request->input('checkboxes');

            // Converta os valores dos checkboxes em um array
            $checkboxesSelecionados = explode(',', $checkboxesSelecionados);  
            foreach ($checkboxesSelecionados as $parcelaId) {
                // Faça o processamento necessário com $parcelaId
            }
        }
        //return view('parcela/parcela_reajustar', compact('checkboxesSelecionados'));
    }
}
