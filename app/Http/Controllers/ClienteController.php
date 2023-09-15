<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cliente;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class ClienteController extends Controller
{
    //RETORNA VIEW DO FILTRO PARA LISTAGEM DE CLIENTES
    function cliente(){
        return view('cliente/cliente_listagem');
    }


    // LISTAGEM DE CLIENTES
    function listar(Request $request){
        $clientes = Cliente::all();
        $total_clientes = $clientes->count();
        $query = Cliente::query();

        // Verifique se o campo "nome" está preenchido no formulário
        if ($request->filled('nome')) {
            $query->where(function ($subquery) use ($request) {
                $subquery->where('nome', 'ilike', '%' . $request->input('nome') . '%')
                    ->orWhere('razao_social', 'ilike', '%' . $request->input('nome') . '%');
            });
        }
    
        // Verifique se o campo "cpf_cnpj" está preenchido no formulário
        if ($request->filled('cpf_cnpj')) {
            $query->where(function ($subquery) use ($request) {
                $subquery->where('cpf', 'ilike', '%' . $request->input('cpf_cnpj') . '%')
                    ->orWhere('cnpj', 'ilike', '%' . $request->input('cpf_cnpj') . '%');
            });
        }
    
        // Execute a consulta e obtenha os resultados
        $clientes = $query->get();

        return view('cliente/cliente_listagem', compact('clientes', 'total_clientes') );
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


    //ALTERAR CLIENTE  
    function alterar($id, $usuario, Request $request){ 
         //Definindo data para cadastrar
         date_default_timezone_set('America/Cuiaba');

        $cliente = Cliente::find($id);

        if (!$cliente) {
            return redirect()->back()->with('error', 'Cliente não encontrado');
        }
    
        //Validar todos campos definidos como obrigatório
        if($request->input('tipo_cadastro_hidden') == 0){
            $validated = $request->validate([
                'nome' => 'required|min:3',
                'cpf' => 'required|unique:cliente,cpf,'.$id,
                'rua_end' => 'required',
                'bairro_end' => 'required',
                'numero_end' => 'required|numeric',
                'cidade_end' => 'required',
                'estado_end' => 'required',
                'cep_end' => 'required|numeric',
                'email' => 'required|email',
                'data_nascimento' => 'nullable|date',
            ]);

            //Campos Pessoa Física
            $cliente->nome = $request->input('nome');
            if ($request->input('cpf')) {
                $cliente->cpf = str_replace(['.', '-'], '', $request->input('cpf'));
            }
            $cliente->rg = $request->input('rg');
            $cliente->data_nascimento = $request->input('data_nascimento');
            $cliente->estado_civil = $request->input('estado_civil');
            $cliente->profissao = $request->input('profissao');

        } else{
            $validated = $request->validate([
                'razao_social' => 'required|min:3',
                'cnpj' => 'required|unique:cliente,cnpj,'.$id,
                'inscricao_estadual' => 'required|numeric',
                'rua_end' => 'required',
                'bairro_end' => 'required',
                'numero_end' => 'required|numeric',
                'cidade_end' => 'required',
                'estado_end' => 'required',
                'cep_end' => 'required|numeric',
                'email' => 'required|email',
                'data_nascimento' => 'nullable|date',
            ]);

            //Campos Pessoa Jurídica
            $cliente->razao_social = $request->input('razao_social');
            if ($request->input('cnpj')) {
                $cliente->cnpj = str_replace(['.', '-','/'], '', $request->input('cnpj'));
            }
            $cliente->inscricao_estadual = $request->input('inscricao_estadual');
        }

        $cliente->rua_end = $request->input('rua_end');
        $cliente->bairro_end = $request->input('bairro_end');
        $cliente->numero_end = $request->input('numero_end');
        $cliente->cidade_end = $request->input('cidade_end');
        $cliente->estado_end = $request->input('estado_end');
        $cliente->data_alteracao = date('d-m-Y h:i:s a', time());
        $cliente->alterado_usuario_id = $usuario;
        $cliente->email = $request->input('email');
        $cliente->telefone1 = $request->input('telefone1');
        $cliente->telefone2 = $request->input('telefone2');
        $cliente->cep_end = $request->input('cep_end');
        $cliente->complemento_end = $request->input('complemento_end');
        $cliente->save();
    
        return redirect('cliente/editar/'.$id)->with('success', 'Cliente atualizado com sucesso');
    }


    //CADASTRO DE CLIENTE
    function cadastrar($usuario, Request $request){

        //Definindo data para cadastrar
        date_default_timezone_set('America/Cuiaba');    

        $cliente = new Cliente();

        //Validar todos campos definidos como obrigatório
        if($request->input('tipo_cadastro') == 0){
            $validated = $request->validate([
                'nome' => 'required|min:3',
                'cpf' => 'required|unique:cliente',
                'rua_end' => 'required',
                'bairro_end' => 'required',
                'numero_end' => 'required|numeric',
                'cidade_end' => 'required',
                'estado_end' => 'required',
                'cep_end' => 'required|numeric',
                'email' => 'required|email',
                'data_nascimento' => 'nullable|date',
            ]);

            //Campos Pessoa Física
            $cliente->nome = $request->input('nome');
            if ($request->input('cpf')) {
                $cliente->cpf = str_replace(['.', '-'], '', $request->input('cpf'));
            }
            $cliente->rg = $request->input('rg');
            $cliente->data_nascimento = $request->input('data_nascimento');
            $cliente->profissao = $request->input('profissao');
            $cliente->estado_civil = $request->input('estado_civil');

        } else{
            $validated = $request->validate([
                'razao_social' => 'required|min:3',
                'cnpj' => 'required|unique:cliente',
                'inscricao_estadual' => 'required|numeric',
                'rua_end' => 'required',
                'bairro_end' => 'required',
                'numero_end' => 'required|numeric',
                'cidade_end' => 'required',
                'estado_end' => 'required',
                'cep_end' => 'required|numeric',
                'email' => 'required|email',
                'data_nascimento' => 'nullable|date',
            ]);

            //Campos Pessoa Jurídica
            $cliente->razao_social = $request->input('razao_social');
            if ($request->input('cnpj')) {
                $cliente->cnpj = str_replace(['.', '-','/'], '', $request->input('cnpj'));
            }
            $cliente->inscricao_estadual = $request->input('inscricao_estadual');

        }

        $cliente->rua_end = $request->input('rua_end');
        $cliente->bairro_end = $request->input('bairro_end');
        $cliente->numero_end = $request->input('numero_end');
        $cliente->cidade_end = $request->input('cidade_end');
        $cliente->estado_end = $request->input('estado_end');
        $cliente->data_cadastro = date('d-m-Y h:i:s a', time());
        $cliente->cadastrado_usuario_id = $usuario;
        $cliente->email = $request->input('email');
        $cliente->telefone1 = $request->input('telefone1');
        $cliente->telefone2 = $request->input('telefone2');
        $cliente->cep_end = $request->input('cep_end');
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


    //EXPORTANDO TABELA PARA PDF
    function relatorio_pdf(Request $request){
        $clientes = Cliente::all();
        $total_clientes = $clientes->count();
        $query = Cliente::query();

        // Verifique se o campo "nome" está preenchido no formulário
        if ($request->filled('nome')) {
            $query->where(function ($subquery) use ($request) {
                $subquery->where('nome', 'ilike', '%' . $request->input('nome') . '%')
                    ->orWhere('razao_social', 'ilike', '%' . $request->input('nome') . '%');
            });
        }
    
        // Verifique se o campo "cpf_cnpj" está preenchido no formulário
        if ($request->filled('cpf_cnpj')) {
            $query->where(function ($subquery) use ($request) {
                $subquery->where('cpf', 'ilike', '%' . $request->input('cpf_cnpj') . '%')
                    ->orWhere('cnpj', 'ilike', '%' . $request->input('cpf_cnpj') . '%');
            });
        }
    
        // Execute a consulta e obtenha os resultados
        $clientes = $query->get();
        $pdf = PDF::loadView('cliente.cliente_relatorio_pdf', ['clientes' => $clientes]);
        return $pdf->download('cliente_relatorio.pdf');
    }

}