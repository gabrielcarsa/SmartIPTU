<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quadra;
use App\Models\User;

class QuadraController extends Controller
{
    //RETORNA VIEW PARA ADICIONAR QUADRA
    function novo($empreendimento_id){
        $quadras = Quadra::where('empreendimento_id', $empreendimento_id)->get();
        $total_quadras = Quadra::all()->count();
        return view('quadra/quadra_novo', compact('empreendimento_id', 'quadras'), compact('total_quadras'));
    }

    //CADASTRO DE QUADRA
    function cadastrar($usuario, $empreendimento_id, Request $request){

        // Validar campos
        //$validated = $request->validated();

        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');    

        $quadra = new Quadra();
        $quadra->nome = $request->input('nome');
        $quadra->empreendimento_id = $empreendimento_id;
        $quadra->data_cadastro = date('d-m-Y h:i:s a', time());
        $quadra->cadastrado_usuario_id = $usuario;
        $quadra->save();
        return redirect('quadra/novo/'.$empreendimento_id)->with('success', 'Quadra cadastrado com sucesso');
    }

}
