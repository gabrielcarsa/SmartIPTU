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
                @if(isset($parcela['titulo']))
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