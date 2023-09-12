<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cliente;

class ClienteController extends Controller
{
    function cliente(){
        $cliente = Cliente::all();
        return view('cliente/cliente_listagem', compact('cliente'));
    }
}
