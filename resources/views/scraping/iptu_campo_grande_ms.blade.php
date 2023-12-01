@extends('layouts/app')

@section('conteudo')

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<h2>Central de Informações</h2>
<p>Dados atualizados obtidos pelo site da prefeitura</p>

@if(isset($resultadoLote))
<div>
    <p>Responsabilidade: {{$resultadoLote['responsabilidade']}}</p>
    <p>Inscrição: {{$resultadoLote['inscricaoMunicipal']}}</p>
    <p>Bairro: {{$resultadoLote['bairro']}}</p>
    <p>Quadra: {{$resultadoLote['quadra']}}</p>
    <p>Lote: {{$resultadoLote['lote']}}</p>
</div>
@endif

<div class="card-body">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th scope="col">Tipo de Débito</th>
                <th scope="col">Descrição Débito</th>
                <th scope="col">Data Vencimento</th>
                <th scope="col">Valor Total</th>

            </tr>
        </thead>
        <tbody>
            @if(isset($resultadoParcela))
            @foreach ($resultadoParcela as $parcela)
            <tr>
                @if($parcela['titulo'] != "")
                <td>{{$parcela['titulo']}}</td>
                @else
                <td></td>
                @endif
                <td>{{$parcela['descricao_debito']}}</td>
                <td>{{$parcela['vencimento']}}</td>
                @if($parcela['valor_total_parcelamento'] == "")
                <td>{{$parcela['valor_total_debitos']}}</td>
                @else
                <td>{{$parcela['valor_total_parcelamento']}}</td>
                @endif
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


@endsection