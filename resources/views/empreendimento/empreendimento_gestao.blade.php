@extends('layouts/app')

@section('conteudo')

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<h2>{{$empreendimento->nome}}</h2>
<a class="btn btn-primary btn-add" href="../../quadra/novo/{{$empreendimento->id}}" style="margin-bottom: 20px">
    <span class="material-symbols-outlined">
        add
    </span>Nova Quadra</a>
<a class="btn btn-primary btn-add" href="../../lote/novo/{{$empreendimento->id}}" style="margin-bottom: 20px">
    <span class="material-symbols-outlined">
        add
    </span>Novo Lote</a>
<a class="btn btn-primary btn-add" href="empreendimento/novo" style="margin-bottom: 20px">
    <span class="material-symbols-outlined">
        add
    </span>Nova Venda</a>
<div class="card">
    <h5 class="card-header">Filtros para buscar</h5>
    <div class="card-body">
        <form class="row g-3" action="/lotes" method="get" autocomplete="off">
            @csrf
            <div class="col-md-6">
                <label for="inputQuadra" class="form-label">Quadra</label>
                <input type="text" name="quadra" value="{{request('quadra')}}" class="form-control" id="inputQuadra">
            </div>
            <div class="col-md-6">
                <label for="inputLote" class="form-label">Lote</label>
                <input type="text" name="lote" value="{{request('lote')}}" class="form-control" id="inputLote">
            </div>
            <div class="col-12">
                <button type="submit" class="btn-submit">Consultar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <h5 class="card-header">Lista de cadastros</h5>
    @if(isset($resultado))
    <div class="card-footer">
        <a class="btn btn-add" href="">PDF</a>
        <a class="btn btn-add" href="">Excel</a>
    </div>
    @endif
    <div class="card-body">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Quadra</th>
                    <th scope="col">Lote</th>
                    <th scope="col">Responsabilidade</th>
                    <th scope="col">Inscrição Municipal</th>
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($resultado))
                @foreach ($resultado as $lote)
                <tr>
                    <th scope="row">{{$lote->lote_id}}</th>
                    <td>{{$lote->quadra_nome}}</td>
                    <td>{{$lote->lote}}</td>
                    @if(empty($lote->nome_cliente))
                    <td>{{$lote->razao_social__cliente}}</td>
                    @else
                    <td>{{$lote->nome_cliente}}</td>
                    @endif
                    <td><a href="../../lote/gestao/{{$lote->lote_id}}" class="btn-acao-listagem">{{$lote->inscricao_municipal}}</a></td>
                    <td><a href="../../lote/editar/{{$lote->lote_id}}" class="btn-acao-listagem-secundary">Ver/Editar</a></td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        @if(isset($resultado))
        <div class="card-footer">
            <p>Exibindo {{$resultado->count()}} de {{ $total_lotes }} registros</p>
        </div>
        @endif

    </div>
</div>

@endsection