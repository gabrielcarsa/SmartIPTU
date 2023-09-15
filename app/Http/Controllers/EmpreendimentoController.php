<?php

namespace App\Http\Controllers;
use App\Models\Empreendimento;
use App\Models\User;
use Illuminate\Http\Request;

class EmpreendimentoController extends Controller
{
    // LISTAGEM DE EMPREENDIMENTOS
    function listar(){
        $empreendimentos = Empreendimento::all();
        $total_empreendimentos = $empreendimentos->count();

        return view('empreendimento/empreendimento_listagem', compact('empreendimentos', 'total_empreendimentos') );
    }
}
