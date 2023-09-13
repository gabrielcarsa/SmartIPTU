@extends('layouts/app')

@section('conteudo')
<h2>Adicionar Clientes</h2>
<div class="card">
    <h5 class="card-header">Preencha os campos requisitados *</h5>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <div class="card-body">
        <form class="row g-3" action="/cliente/cadastrar/{{Auth::user()->id}}" method="post" autocomplete="off">
            @csrf
            <div class="col-md-4">
                <label for="inputNome" class="form-label">Nome*</label>
                <input type="text" name="nome" value="{{ old('nome') }}" class="form-control @error('nome') is-invalid @enderror" id="inputNome">
            </div>
            <div class="col-md-3">
                <label for="inputCpf" class="form-label">CPF/CNPJ*</label>
                <input type="text" name="cpf_cnpj" value="{{ old('cpf_cnpj') }}" class="form-control @error('cpf_cnpj') is-invalid @enderror" id="inputCpf" pattern="\d{3}\.?\d{3}\.?\d{3}-?\d{2}" >
            </div>
            <div class="col-md-2">
                <label for="inputRg" class="form-label">RG</label>
                <input type="text" name="rg" value="{{ old('rg') }}" class="form-control" id="inputRg">
            </div>
            <div class="col-md-3">
                <label for="inputTipo" class="form-label">Tipo de cadastro*</label>
                <select id="inputTipo" name="tipo_cadastro" class="form-select">
                    <option value="0" selected>Pessoa Física</option>
                    <option value="1">Pessoa Jurídica</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="inputRua" class="form-label">Rua*</label>
                <input type="text" name="rua_end" value="{{ old('rua_end') }}" class="form-control @error('rua_end') is-invalid @enderror" id="inputRua" placeholder="">
            </div>
            <div class="col-md-3">
                <label for="inputBairro" class="form-label">Bairro*</label>
                <input type="text" name="bairro_end" value="{{ old('bairro_end') }}" class="form-control @error('bairro_end') is-invalid @enderror" id="inputBairro">
            </div>
            <div class="col-md-4">
                <label for="inputCidade" class="form-label">Cidade*</label>
                <input type="text" name="cidade_end" value="{{ old('cidade_end') }}" class="form-control @error('cidade_end') is-invalid @enderror" id="inputCidade">
            </div>
            <div class="col-md-2">
                <label for="inputEstado" class="form-label">Estado*</label>
                <input type="text" name="estado_end" value="{{ old('estado_end') }}" class="form-control @error('estado_end') is-invalid @enderror" id="inputEstado">
            </div>

            <div class="col-md-2">
                <label for="inputNumero" class="form-label">Número*</label>
                <input type="text" name="numero_end" value="{{ old('numero_end') }}" class="form-control @error('numero_end') is-invalid @enderror" id="inputNumero">
            </div>
            <div class="col-md-2">
                <label for="inputComplemento" class="form-label">Complemento</label>
                <input type="text" name="complemento_end" value="{{ old('complemento_end') }}" class="form-control" id="inputComplemento">
            </div>
            <div class="col-md-2">
                <label for="inputCep" class="form-label">CEP*</label>
                <input type="text" name="cep_end" value="{{ old('cep_end') }}" class="form-control @error('cep_end') is-invalid @enderror" id="inputCep">
            </div>
            <div class="col-md-2">
                <label for="inputDataNascimento" class="form-label">Data de nascimento</label>
                <input type="date" name="data_nascimento" value="{{ old('data_nascimento') }}" class="form-control @error('email') is-invalid @enderror" id="inputDataNascimento" placeholder="1234 Main St">
            </div>
            <div class="col-md-4">
                <label for="inputProfissao" class="form-label">Profissão</label>
                <input type="text" name="profissao" value="{{ old('profissao') }}" class="form-control" id="inputProfissao">
            </div>
            <div class="col-md-3">
                <label for="inputEstadoCivil" class="form-label">Estado Civil</label>
                <input type="text" name="estado_civil" value="{{ old('estado_civil') }}" class="form-control" id="inputEstadoCivil">
            </div>
            <div class="col-md-3">
                <label for="inputEmail" class="form-label">Email*</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" id="inputEmail">
            </div>
            <div class="col-md-3">
                <label for="inputTelefone1" class="form-label">Telefone 1</label>
                <input type="text" name="telefone1" value="{{ old('telefone1') }}" class="form-control" id="inputTelefone1">
            </div>
            <div class="col-md-3">
                <label for="inputTelefone2" class="form-label">Telefone 2</label>
                <input type="text" name="telefone2" value="{{ old('telefone2') }}" class="form-control" id="inputTelefone2">
            </div>
            <div class="col-12">
                <button type="submit" class="btn-submit">Cadastrar</button>
            </div>
        </form>
    </div>
</div>
@endsection