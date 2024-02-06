<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ParcelasAPIController extends Controller
{
    public function paraPagarReceberHoje(Request $request){
        $hoje = now()->toDateString(); // Obtém a data de hoje no formato 'YYYY-MM-DD'

        //Soma das parcelas a Pagar no dia de Hoje
        $pagarHoje =  DB::table('parcela_conta_pagar')
        ->whereDate('data_vencimento', $hoje)
        ->where('situacao', 0)
        ->sum('valor_parcela');

        //Soma das parcelas a Receber no dia de Hoje
        $receberHoje =  DB::table('parcela_conta_receber')
        ->whereDate('data_vencimento', $hoje)
        ->where('situacao', 0)
        ->sum('valor_parcela');

        $data = [
            'pagarHoje' => $pagarHoje,
            'receberHoje' => $receberHoje,
        ];

        return response()->json($data);
    }
}
