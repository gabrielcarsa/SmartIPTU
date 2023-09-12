<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ClienteRequest;
use App\Models\Cliente;

class ClienteController extends Controller
{
    function cliente(){
        return view('cliente/cliente_listagem');
    }

    function listar(ClienteRequest $request){
        $nome = $request->input('nome');
        $clientes = DB::table('cliente')->where('nome', 'LIKE', '%' . $nome . '%')->get();
        return view('cliente/cliente_listagem', compact('clientes'));
    }

    function novo(){
        return view('cliente/cliente_novo');
    }
}