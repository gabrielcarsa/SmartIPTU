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
    @if(isset($prescricoes))
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
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @if(isset($prescricoes))
                @foreach ($prescricoes as $prescricao)
                <tr>
                    <th scope="row">{{$prescricao->id}}</th>
                    <td>{{$prescricao->processo}}</td>
                    <td>{{ \Carbon\Carbon::parse($prescricao->entrada_pedido)->format('d/m/Y') }}</td>
                    <td>{{$prescricao->anos_referencia}}</td>
                    <td>{{$prescricao->observacao}}</td>
                    <td>
                        <a class="btn-acao-listagem-secundary" href="editar/{{$prescricao->id}}">Ver/Editar</a>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        @if(isset($prescricoes))
        <div class="card-footer">
            <p>Exibindo {{$prescricoes->count()}} de {{ $total_prescricoes }} registros</p>
        </div>
        @endif

    </div>
</div>

@endsection