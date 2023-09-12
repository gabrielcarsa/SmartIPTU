@extends('layouts/app')

@section('conteudo')
<h2>Adicionar Clientes</h2>
<div class="card">
    <h5 class="card-header">Preencha os campos requisitados *</h5>
    <div class="card-body">
        <form class="row g-3" action="/cliente/novo" method="post">
            @csrf
            <div class="col-md-4">
                <label for="inputNome" class="form-label">Nome*</label>
                <input type="text" class="form-control" id="inputNome">
            </div>
            <div class="col-md-3">
                <label for="inputCpf" class="form-label">CPF/CNPJ*</label>
                <input type="text" class="form-control" id="inputCpf">
            </div>
            <div class="col-md-2">
                <label for="inputRg" class="form-label">RG</label>
                <input type="text" class="form-control" id="inputRg">
            </div>
            <div class="col-md-3">
                <label for="inputTipo" class="form-label">Tipo de cadastro*</label>
                <select id="input" class="form-select">
                    <option value="0" selected>Pessoa Física</option>
                    <option value="1">Pessoa Jurídica</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="inputAddress2" class="form-label">Rua</label>
                <input type="text" class="form-control" id="inputAddress2" placeholder="Apartment, studio, or floor">
            </div>
            <div class="col-md-3">
                <label for="inputCity" class="form-label">Bairro</label>
                <input type="text" class="form-control" id="inputCity">
            </div>
            <div class="col-md-4">
                <label for="inputZip" class="form-label">Cidade</label>
                <input type="text" class="form-control" id="inputZip">
            </div>
            <div class="col-md-2">
                <label for="inputZip" class="form-label">Estado</label>
                <input type="text" class="form-control" id="inputZip">
            </div>

            <div class="col-md-2">
                <label for="inputZip" class="form-label">Número</label>
                <input type="text" class="form-control" id="inputZip">
            </div>
            <div class="col-md-2">
                <label for="inputZip" class="form-label">Complemento</label>
                <input type="text" class="form-control" id="inputZip">
            </div>
            <div class="col-md-2">
                <label for="inputZip" class="form-label">CEP</label>
                <input type="text" class="form-control" id="inputZip">
            </div>
            <div class="col-md-2">
                <label for="inputAddress" class="form-label">Data de nascimento</label>
                <input type="date" class="form-control" id="inputAddress" placeholder="1234 Main St">
            </div>
            <div class="col-md-4">
                <label for="inputZip" class="form-label">Profissão</label>
                <input type="text" class="form-control" id="inputZip">
            </div>
            <div class="col-md-3">
                <label for="inputZip" class="form-label">Estado Civil</label>
                <input type="text" class="form-control" id="inputZip">
            </div>
            <div class="col-md-3">
                <label for="inputZip" class="form-label">Email</label>
                <input type="text" class="form-control" id="inputZip">
            </div>
            <div class="col-md-3">
                <label for="inputZip" class="form-label">Telefone 1</label>
                <input type="text" class="form-control" id="inputZip">
            </div>
            <div class="col-md-3">
                <label for="inputZip" class="form-label">Telefone 2</label>
                <input type="text" class="form-control" id="inputZip">
            </div>
            <div class="col-12">
                <button type="submit" class="btn-submit">Cadastrar</button>
            </div>
        </form>
    </div>
</div>
@endsection