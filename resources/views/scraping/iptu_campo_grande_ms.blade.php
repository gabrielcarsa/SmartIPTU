@extends('layouts/app')

@section('conteudo')

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<h2>Central de Informações</h2>
<p>Dados atualizados obtidos pelo site da prefeitura 01/12/2023 17:15</p>

<a class="btn btn-primary btn-add" id="central_informacoes" href="{{route('iptuCampoGrande')}}"
    style="margin-bottom: 20px; background-color:RGB(254, 254, 34); color:#000">
    Atualizar banco de dados desse lote
</a>

@if(isset($resultadoLote))
<div class="row">
    <div class="col-md-6">
        <p>Responsabilidade: {{$resultadoLote['responsabilidade']}}</p>
        <p>Inscrição: {{$resultadoLote['inscricaoMunicipal']}}</p>
        <p>Bairro: {{$resultadoLote['bairro']}}</p>
    </div>
    <div class="col-md-6">
        <p>Quadra: {{$resultadoLote['quadra']}}</p>
        <p>Lote: {{$resultadoLote['lote']}}</p>
    </div>

</div>
@endif

@if(isset($resultadoParcela))

@foreach ($resultadoParcela as $parcela)

@if(isset($parcela['titulo']))
<h3>{{$parcela['titulo']}}</h3>
@endif

<table class="table table-striped table-dark">
    <thead>
        <tr>
            <th>Descrição do Débito</th>
            <th>Vencimento</th>
            <th>Valor Total</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($parcela['parcelas']))
        @foreach ($parcela['parcelas'] as $detalheParcela)
        <tr>
            <td>{{$detalheParcela['descricao_debito']}}</td>
            <td>{{$detalheParcela['vencimento']}}</td>

            {{-- Verificando se o valor total do parcelamento está vazio --}}
            @if($detalheParcela['valor_total_parcelamento'] == "")
            <td>{{$detalheParcela['valor_total_debitos']}}</td>
            @else
            <td>{{$detalheParcela['valor_total_parcelamento']}}</td>
            @endif
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
@endforeach

@endif
@endsection