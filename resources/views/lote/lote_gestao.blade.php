@extends('layouts/app')

@section('conteudo')

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<div class="container-md text-center">
    <div class="row">
        <div class="col">
            <h4>Quadra</h4>
            <p>{{ $resultados[0]->quadra_nome }}</p>
        </div>
        <div class="col">
            <h4>Lote</h4>
            <p>{{ $resultados[0]->lote }}</p>
        </div>
        <div class="col">
            <h4>Responsabilidade</h4>
            @if (!empty($resultados[0]->nome_cliente))
            <p>{{ $resultados[0]->nome_cliente }}</p>
            @elseif (!empty($resultados[0]->razao_social_cliente))
            <p>{{ $resultados[0]->razao_social_cliente }}</p>
            @endif
        </div>
        <div class="col">
            <h4>Inscrição Municipal</h4>
            <p>{{ $resultados[0]->inscricao_municipal }}</p>
        </div>
        <div class="col">
            <h4>Total Débitos</h4>
            <p>R$ 500</p>
        </div>
    </div>
</div>

<div class="card">
    <h5 class="card-header">{{ $resultados[0]->tipo_debito_descricao }}</h5>
    @if(isset($resultados))
    <div class="card-footer">
        <a class="btn btn-add" href="">PDF</a>
        <a class="btn btn-add" href="">Excel</a>
    </div>
    @endif
    <div class="card-footer">
        <p></p>
    </div>
    <div class="card-body">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Empreendimento</th>
                    <th scope="col">Matrícula</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Ações</th>
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
                        <a class="btn-acao-listagem-yellow"
                            href="empreendimento/editar/{{$empreendimento->id}}">Ver/Editar</a>
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