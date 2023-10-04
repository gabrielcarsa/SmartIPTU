<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoDebito;

class TipoDebitoController extends Controller
{
    function tipo_debito(){
        $lista_tipo_debito = TipoDebito::all();
        $total_lista_tipo_debito = $lista_tipo_debito->count();
        return view('tipo_debito/tipo_debito', compact('lista_tipo_debito', 'total_lista_tipo_debito') );
    }
}
