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
            <div class="col-md-2">
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
    <h5 class="card-header">
        Movimentação Financeira{!! isset($data['saldo_atual']) ? " do dia <strong>" . \Carbon\Carbon::parse($data['saldo_atual'][0]->data)->format('d/m/Y') . "</strong>" : "" !!}
    </h5>
    @if(isset($movimentacao))
    <div class="card-footer">
        <a class="btn btn-add"
            href="../movimentacao_financeira/relatorio_pdf?nome={{request('nome')}}&cpf_cnpj={{request('cpf_cnpj')}}">PDF</a>
        <a class="btn btn-add" href="">Excel</a>
    </div>
    <div class="card-saldo">
        <div class="row">
            <div class="col">
                @if(isset($data['saldo_anterior']))
                <p>Saldo Inicial:
                    <span id="saldo">R$ {{number_format($data['saldo_anterior'][0]->saldo, 2, ',', '.')}}</span>
                </p>
                @endif
            </div>
            <div class="col text-right">
                @if(isset($data['saldo_atual']))
                <p>Saldo em Banco:
                    <span id="saldo">R$ {{number_format($data['saldo_atual'][0]->saldo, 2, ',', '.')}}</span>
                </p>
                @endif
            </div>
        </div>
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
                @if(isset($movimentacao))
                @foreach ($movimentacao as $mov)
                <tr>
                    <th scope="row">{{$mov->id}}</th>
                    @if($mov->tipo_cadastro == 0)
                    <td>{{$mov->nome}}</td>
                    @else
                    <td>{{$mov->razao_social}}</td>
                    @endif
                    <td>{{$mov->descricao}}</td>
                    @if($mov->tipo_movimentacao == 0)
                    <td>{{$mov->valor}}</td>
                    <td></td>
                    @else
                    <td></td>
                    <td>{{$mov->valor}}</td>
                    @endif
                    <td><a href="editar/{{$mov->id}}" class="btn-acao-listagem-secundary">Ver/Editar</a></td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        @if(isset($movimentacao))
        <div class="card-footer">
            <p>Exibindo {{$movimentacao->count()}} de {{ $data['total_movimentacao'] }} registros</p>
        </div>
        @endif

    </div>
</div>


@endsection