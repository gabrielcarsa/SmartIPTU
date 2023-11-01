<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MovimentacaoFinanceiraController extends Controller
{
    function movimentacao_financeira(){
        return view('movimentacao_financeira/movimentacao_financeira');
    }

    function novo(){
    }
}
