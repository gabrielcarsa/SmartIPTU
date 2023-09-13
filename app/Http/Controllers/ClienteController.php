<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ClienteRequest;
use App\Models\Cliente;

class ClienteController extends Controller
{
    //RETORNA VIEW DO FILTRO PARA LISTAGEM DE CLIENTES
    function cliente(){
        return view('cliente/cliente_listagem');
    }
    // LISTAGEM DE CLIENTES
    function listar(Request $request){
        $nome = $request->input('nome');
        if (empty($nome)) {
            $clientes = Cliente::all();
        } else {
            $clientes = DB::table('cliente')->where('nome', 'LIKE', '%' . $nome . '%')->get();
        }
        return view('cliente/cliente_listagem', compact('clientes'));
    }

    //RETORNA VIEW PARA ADICIONAR CLIENTES
    function novo(){
        return view('cliente/cliente_novo');
    }

    //CADASTRO DE CLIENTE
    function cadastrar($usuario, ClienteRequest $request){
        $cliente = new Cliente();

        //Validar todos campos definidos como obrigatório
        $validated = $request->validated();    

        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');

        $cliente->nome = $request->input('nome');
        $cliente->cpf_cnpj = $request->input('cpf_cnpj');
        $cliente->rg = $request->input('rg');
        $cliente->rua_end = $request->input('rua_end');
        $cliente->bairro_end = $request->input('bairro_end');
        $cliente->numero_end = $request->input('numero_end');
        $cliente->cidade_end = $request->input('cidade_end');
        $cliente->estado_end = $request->input('estado_end');
        $cliente->data_nascimento = $request->input('data_nascimento');
        $cliente->data_cadastro = date('d-m-Y h:i:s a', time());
        $cliente->usuario_id = $usuario;
        $cliente->email = $request->input('email');
        $cliente->telefone1 = $request->input('telefone1');
        $cliente->telefone2 = $request->input('telefone2');
        $cliente->cep_end = $request->input('cep_end');
        $cliente->estado_civil = $request->input('estado_civil');
        $cliente->profissao = $request->input('profissao');
        $cliente->complemento_end = $request->input('complemento_end');
        $cliente->tipo_cadastro = $request->input('tipo_cadastro');
        $cliente->save();
        return redirect('cliente');
    }

}