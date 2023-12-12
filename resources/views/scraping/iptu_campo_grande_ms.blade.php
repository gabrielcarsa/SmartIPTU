@extends('layouts/app')

@section('conteudo')

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<h2>Central de Informações</h2>

@if(isset($resultadoLote) && $resultadoLote != [])
<table class="table table-bordered border-primary">
    <thead>
        <tr>
            <th>Responsabilidade</th>
            <th>Inscrição</th>
            <th>Bairro</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{$resultadoLote['responsabilidade']}}</td>
            <td>{{$resultadoLote['inscricaoMunicipal']}}</td>
            <td>{{$resultadoLote['bairro']}}</td>
        </tr>
    </tbody>
</table>

<table class="table table-bordered border-primary">
    <thead>
        <tr>
            <th>Quadra</th>
            <th>Lote</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Quadra: {{$resultadoLote['quadra']}}</td>
            <td>Lote: {{$resultadoLote['lote']}}</td>
        </tr>
    </tbody>
</table>

@else
<p><span class="material-symbols-outlined">
        warning
    </span> Nenhum débito encontrado, caso exista verifique o número da inscrição no cadastro do lote</p>
@endif

@if(isset($resultadoParcela) && $resultadoLote != [])
@foreach ($resultadoParcela as $parcela)
<hr>
@if(isset($parcela['titulo']))
<div class="row" style="padding-top: 20px !important">
    <div class="col-md-6">
        <h3>{{$parcela['titulo']}}</h3>
    </div>
    <div class="col-md-6 text-right">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal-{{$loop->index}}">
            Atualizar débito
        </button>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal-{{$loop->index}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmar atualização?</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Atenção:</p>
                <ul>
                    <li>Ao atualizar, os débitos já existentes continuarão lá.</li>
                    <li>Cuidado ao atualizar débitos já cadastrados, isso pode gerar duplicidade deles.</li>
                    <li>Ao atualizar o sistema não irá substituar os mesmos débitos já existentes.</li>
                    <li>Antes de realizar essa ação, recomenda-se saber tudo sobre essa funcionalidade!</li>
                </ul>
                <p>Deseja mesmo confirmar essa atualização de {{$parcela['titulo']}}?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não</button>
                <a class="btn btn-primary" id="central_informacoes"
                    href="{{ route('cadastrar_scraping', ['debito' => json_encode($parcela), 'lote_id' => $lote_id, 'usuario' => Auth::user()->id]) }}">
                    Sim, Atualizar
                </a>
            </div>
        </div>
    </div>
</div>

@endif

<table class="table table-bordered border-primary text-center">
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