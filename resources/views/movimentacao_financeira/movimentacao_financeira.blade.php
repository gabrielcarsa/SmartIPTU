@extends('layouts/app')

@section('conteudo')

<h2>Movimentação Financeira</h2>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif
@if (session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card">
    <h5 class="card-header">Filtros para buscar</h5>
    <div class="card-body">
        <form class="row g-3" action="/movimentacao_financeira/listar" method="get" autocomplete="off">
            @csrf
            <div class="col-md-6">
                <label for="inputData" class="form-label">Data da movimentação</label>
                <input type="date" name="data" value="{{request('data')}}" class="form-control" id="inputData">
            </div>
            <div class="col-12">
                <button type="submit" class="btn-submit">Consultar</button>
                <a href="/movimentacao_financeira/novo" class="btn-add"><span class="material-symbols-outlined">
                        add
                    </span>Nova Movimentação</a>
            </div>
        </form>
    </div>
</div>



<div class="card">
    <h5 class="card-header">Movimentação</h5>
    @if(isset($clientes))
    <div class="card-footer">
        <a class="btn btn-add" href="../movimentacao_financeira/relatorio_pdf?nome={{request('nome')}}&cpf_cnpj={{request('cpf_cnpj')}}">PDF</a>
        <a class="btn btn-add" href="">Excel</a>
    </div>
    @endif
    <div class="card-body">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Cliente / Fornecedor</th>
                    <th scope="col">Descrição</th>
                    <th scope="col">Valor da Entrada</th>
                    <th scope="col">Valor da Saída</th>
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
                    <td><a href="editar/{{$cliente->id}}" class="btn-acao-listagem-secundary">Ver/Editar</a></td>
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