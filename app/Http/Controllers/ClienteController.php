<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ClienteRequest;
use App\Models\Cliente;
use App\Models\User;

class ClienteController extends Controller
{
    //RETORNA VIEW DO FILTRO PARA LISTAGEM DE CLIENTES
    function cliente(){
        return view('cliente/cliente_listagem');
    }
    // LISTAGEM DE CLIENTES
    function listar(Request $request){
        $nome = $request->input('nome');
        $cadastrado_user = null; // Defina como null por padrão

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

    //RETORNA VIEW ALTERAR CLIENTE  
    function editar($id){
        $cliente = Cliente::find($id);
        
        $cadastrado_por_user_id = $cliente['cadastrado_usuario_id'];
        $alterado_por_user_id = $cliente['alterado_usuario_id'];

        $cadastrado_por_user = User::find($cadastrado_por_user_id);
        $alterado_por_user = User::find($alterado_por_user_id);

        
        return view('cliente/cliente_novo', compact('cliente', 'alterado_por_user'), compact('cadastrado_por_user'));
    }

    function alterar($id, $usuario, ClienteRequest $request){ 
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return redirect()->back()->with('error', 'Cliente não encontrado');
        }
    
         //Validar todos campos definidos como obrigatório
         $validated = $request->validated();    

         //Definindo data para cadastrar
         date_default_timezone_set('America/Cuiaba');
 
        $cliente->nome = $request->input('nome');
        $cliente->razao_social = $request->input('razao_social');
        $cliente->cnpj = $request->input('cnpj');
        $cliente->cpf = $request->input('cpf');
        $cliente->rg = $request->input('rg');
        $cliente->rua_end = $request->input('rua_end');
        $cliente->bairro_end = $request->input('bairro_end');
        $cliente->numero_end = $request->input('numero_end');
        $cliente->cidade_end = $request->input('cidade_end');
        $cliente->estado_end = $request->input('estado_end');
        $cliente->data_nascimento = $request->input('data_nascimento');
        $cliente->data_alteracao = date('d-m-Y h:i:s a', time());
        $cliente->alterado_usuario_id = $usuario;
        $cliente->email = $request->input('email');
        $cliente->inscricao_estadual = $request->input('inscricao_estadual');
        $cliente->telefone1 = $request->input('telefone1');
        $cliente->telefone2 = $request->input('telefone2');
        $cliente->cep_end = $request->input('cep_end');
        $cliente->estado_civil = $request->input('estado_civil');
        $cliente->profissao = $request->input('profissao');
        $cliente->complemento_end = $request->input('complemento_end');
        $cliente->save();
    
        return redirect('cliente/editar/'.$id)->with('success', 'Cliente atualizado com sucesso');
    }

    //CADASTRO DE CLIENTE
    function cadastrar($usuario, ClienteRequest $request){
        $cliente = new Cliente();

        //Validar todos campos definidos como obrigatório
        $validated = $request->validated();    

        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');

        $cliente->nome = $request->input('nome');
        $cliente->razao_social = $request->input('razao_social');
        $cliente->cnpj = $request->input('cnpj');
        $cliente->cpf = $request->input('cpf');
        $cliente->rg = $request->input('rg');
        $cliente->rua_end = $request->input('rua_end');
        $cliente->bairro_end = $request->input('bairro_end');
        $cliente->numero_end = $request->input('numero_end');
        $cliente->cidade_end = $request->input('cidade_end');
        $cliente->estado_end = $request->input('estado_end');
        $cliente->data_nascimento = $request->input('data_nascimento');
        $cliente->data_cadastro = date('d-m-Y h:i:s a', time());
        $cliente->cadastrado_usuario_id = $usuario;
        $cliente->email = $request->input('email');
        $cliente->inscricao_estadual = $request->input('inscricao_estadual');
        $cliente->telefone1 = $request->input('telefone1');
        $cliente->telefone2 = $request->input('telefone2');
        $cliente->cep_end = $request->input('cep_end');
        $cliente->estado_civil = $request->input('estado_civil');
        $cliente->profissao = $request->input('profissao');
        $cliente->complemento_end = $request->input('complemento_end');
        $cliente->tipo_cadastro = $request->input('tipo_cadastro');
        $cliente->save();
        return redirect('cliente')->with('success', 'Cliente cadastrado com sucesso');
    }

    //EXCLUIR CLIENTE
    function excluir($id){
        $cliente = Cliente::find($id);
        $cliente->delete();
        return redirect("cliente")->with('success', 'Cliente excluido com sucesso');
    }

}