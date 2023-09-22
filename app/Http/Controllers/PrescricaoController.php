<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prescricao;


class PrescricaoController extends Controller
{
    function prescricao($lote_id){
        Prescricao::where('lote_id', $lote_id);
        return view('prescricao/prescricao_listagem', compact('lote_id'));
    }
}
