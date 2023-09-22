@extends('layouts/app')

@section('conteudo')

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<h2>Prescrições</h2>
<a class="btn btn-primary btn-add" href="{{ route('prescricao_novo', [$lote_id]) }}" style="margin-bottom: 20px">
    <span class="material-symbols-outlined">
        add
    </span>Novo</a>
<div class="card">
    <h5 class="card-header">Prescrições</h5>
    @if(isset($empreendimentos))
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
                    <th scope="col">Processo</th>
                    <th scope="col">Data de entrada do pedido</th>
                    <th scope="col">Ano(s) Refência</th>
                    <th scope="col">Observação</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($empreendimentos))
                @foreach ($empreendimentos as $empreendimento)
                <tr>
                    <th scope="row">{{$empreendimento->id}}</th>
                    <td>{{$empreendimento->nome}}</td>
                    <td>{{$empreendimento->matricula}}</td>
                    <td>{{$empreendimento->cidade}}, {{$empreendimento->estado}}</td>
                    <td>
                        <a class="btn-acao-listagem" href="empreendimento/gestao/{{$empreendimento->id}}">Gestão</a>
                        <a class="btn-acao-listagem-secundary" href="empreendimento/editar/{{$empreendimento->id}}">Ver/Editar</a>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        @if(isset($empreendimentos))
        <div class="card-footer">
            <p>Exibindo {{$empreendimentos->count()}} de {{ $total_empreendimentos }} registros</p>
        </div>
        @endif

    </div>
</div>

@endsection