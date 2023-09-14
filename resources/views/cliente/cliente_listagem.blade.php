@extends('layouts/app')

@section('conteudo')

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<h2>Clientes</h2>
<div class="card">
    <h5 class="card-header">Filtros para buscar</h5>
    <div class="card-body">
        <form class="row g-3" action="/cliente/listar" method="get" autocomplete="off">
            @csrf
            <div class="col-md-6">
                <label for="inputEmail4" class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" id="inputEmail4">
            </div>
            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">CPF/CNPJ</label>
                <input type="text" name="cpf_cnpj" class="form-control" id="inputPassword4">
            </div>
            <div class="col-12">
                <button type="submit" class="btn-submit">Consultar</button>
                <a href="/cliente/novo" class="btn-add"><span class="material-symbols-outlined">
                        add
                    </span>Novo</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <h5 class="card-header">Lista de cadastros</h5>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nome/Razão Social</th>
                    <th scope="col">CPF/CNPJ</th>
                    <th scope="col">Telefone</th>
                    <th scope="col">Email</th>
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($clientes))
                @foreach ($clientes as $cliente)
                <tr>
                    <th scope="row">{{$cliente->id}}</th>
                    @if($cliente->tipo_cadastro == 0)
                    <td>{{$cliente->nome}}</td>
                    <td>{{$cliente->cpf}}</td>
                    @else
                    <td>{{$cliente->razao_social}}</td>
                    <td>{{$cliente->cnpj}}</td>
                    @endif
                    <td>{{$cliente->telefone1}}</td>
                    <td>{{$cliente->email}}</td>
                    <td><a href="editar/{{$cliente->id}}">Ver/Editar</a></td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        @if(isset($clientes))
        <div class="card-footer">
            <p>Exibindo {{$clientes->count()}} de {{ $total_clientes }} registros</p>
        </div>
        @endif

    </div>
</div>

@endsection