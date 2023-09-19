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
<a class="btn btn-primary btn-add" href="{{ route('debito_novo', ['lote_id' => $resultados[0]->lote_id]) }}"
    style="margin-bottom: 20px">
    <span class="material-symbols-outlined">
        add
    </span>
    Adicionar Parcelas
</a>

@if($resultados[0]->tipo_debito_descricao)

@php
$displayedDebitoDescricao = [];
@endphp

@foreach($resultados as $i)
@if (!in_array($i->tipo_debito_descricao, $displayedDebitoDescricao))

<div class="card">
    <h5 class="card-header">{{ $i->tipo_debito_descricao }}</h5>
    @if(isset($resultados))
    <div class="card-footer">
        <a class="btn btn-add" href="">PDF</a>
        <a class="btn btn-add" href="">Excel</a>
    </div>
    @endif
    <div class="card-footer">
        @if (isset($resultados))
        <p>
            Cadastrado por <strong>{{ $resultados[0]->cadastrado_usuario_nome }}</strong> em
            {{ \Carbon\Carbon::parse( $resultados[0]->debito_data_cadastro)->format('d/m/Y') }}
        </p>
        @if (isset($alterado_por_user))
        <p>
            Última alteração feita por <strong>{{ $resultados[0]->alterado_usuario_nome }}</strong> em
            {{ \Carbon\Carbon::parse( $resultados[0]->debito_data_alteracao)->format('d/m/Y') }}
        </p>
        @endif
        @endif
    </div>
    <div class="card-body">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col">ID</th>
                    <th scope="col">Número Parcela</th>
                    <th scope="col">Descrição</th>
                    <th scope="col">Data Vencimento</th>
                    <th scope="col">Valor Parcela</th>
                    <th scope="col">Valor Pago</th>
                    <th scope="col">Data Recebimento</th>
                    <th scope="col">Situação</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($resultados))
                @foreach ($resultados as $resultado)
                @if($resultado->tipo_debito_descricao == $i->tipo_debito_descricao)
                <tr>
                    <th scope="row"><input type="checkbox" id="" name="{{ $resultado->parcela_id }}" /></th>
                    <th scope="row">{{$resultado->parcela_id}}</th>
                    <th scope="row">{{$resultado->numero_parcela}}</th>
                    <th scope="row">{{$resultado->descricao_debito_descricao}}</th>
                    @if(empty($resultado->data_vencimento_parcela))
                    <th scope="row"></th>
                    @else
                    <th scope="row">{{ \Carbon\Carbon::parse($resultado->data_vencimento_parcela)->format('d/m/Y') }}
                    </th>
                    @endif
                    <th scope="row">R$ {{ $resultado->valor_parcela }}</th>
                    <th scope="row">R$ {{ $resultado->valor_pago_parcela }}</th>
                    @if(empty($resultado->data_recebimento_parcela))
                    <th scope="row"></th>
                    @else
                    <th scope="row">{{ \Carbon\Carbon::parse($resultado->data_recebimento_parcela)->format('d/m/Y') }}
                    </th>
                    @endif
                    @if($resultado->situacao_parcela == 0)
                    <th scope="row">Em Aberto</th>
                    @else
                    <th scope="row">Pago</th>
                    @endif
                </tr>
                @endif
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


@php
$displayedDebitoDescricao[] = $i->tipo_debito_descricao;
@endphp

@endif

@endforeach

@else

<p></p>

@endif

@endsection