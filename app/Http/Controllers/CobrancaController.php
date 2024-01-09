<?php

namespace App\Http\Controllers;
use App\Models\Empreendimento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CobrancaController extends Controller
{
    // FUNÇÃO PARA RETORNAR GESTÃO DE COBRANÇA
    public function gestao_cobranca(){
        $empreendimentos = Empreendimento::all();

        $data = [
            'empreendimentos' => $empreendimentos,
        ];

        return view('cobranca/cobranca_gestao', compact('data'));
    }

}
