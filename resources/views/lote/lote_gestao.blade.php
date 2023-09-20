@extends('layouts/app')

@section('conteudo')

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


<a class="btn btn-primary btn-add" href="{{ route('debito_novo', ['lote_id' => $resultados[0]->lote_id]) }}"
    style="margin-bottom: 20px">
    <span class="material-symbols-outlined">
        add
    </span>
    Adicionar Parcelas
</a>

<a class="btn btn-primary btn-add" id="reajustar_parcelas" href="{{route('parcela_reajustar')}}"
    style="margin-bottom: 20px">
    <span class="material-symbols-outlined">
        attach_money
    </span>
    Reajustar Valores
</a>

<a class="btn btn-primary btn-add" id="alterar_vencimento" href="{{route('alterar_vencimento')}}"
    style="margin-bottom: 20px">
    <span class="material-symbols-outlined">
        edit_calendar
    </span>
    Alterar data de vencimento
</a>

<a class="btn btn-primary btn-add" href="{{ route('debito_novo', ['lote_id' => $resultados[0]->lote_id]) }}"
    style="margin-bottom: 20px">
    <span class="material-symbols-outlined">
        payments
    </span>
    Baixar parcelas
</a>

@if($resultados[0]->tipo_debito_descricao)

@php
$displayedDebitoDescricao = [];
@endphp

@foreach($resultados as $i)
@if (!in_array($i->tipo_debito_descricao, $displayedDebitoDescricao))

<div class="card">
    <h5 class="card-header">{{ $i->tipo_debito_descricao }}</h5>
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
            <form action="" method="post">
                @csrf
                <thead>
                    <tr>
                        <th scope="col"><input type="checkbox" id="selecionar_todos" name="selecionar_todos" /></th>
                        <th scope="col">ID</th>
                        <th scope="col">Nº Parcela</th>
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
                    <tr class="resultados-table">
                        <th scope="row"><input type="checkbox" id="" name="checkboxes[]"
                                value="{{ $resultado->parcela_id }}" /></th>
                        <th scope="row" class="id_table">{{$resultado->parcela_id}}</th>
                        <th scope="row">{{$resultado->numero_parcela}} / {{ $resultado->quantidade_parcela_debito }}
                        </th>
                        <th scope="row">{{$resultado->descricao_debito_descricao}}</th>
                        @if(empty($resultado->data_vencimento_parcela))
                        <th scope="row"></th>
                        @else
                        <th scope="row">
                            {{ \Carbon\Carbon::parse($resultado->data_vencimento_parcela)->format('d/m/Y') }}
                        </th>
                        @endif
                        <th scope="row">R$ {{ $resultado->valor_parcela }}</th>
                        <th scope="row">R$ {{ $resultado->valor_pago_parcela }}</th>
                        @if(empty($resultado->data_recebimento_parcela))
                        <th scope="row"></th>
                        @else
                        <th scope="row">
                            {{ \Carbon\Carbon::parse($resultado->data_recebimento_parcela)->format('d/m/Y') }}
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
            </form>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Captura o clique no Parcelas Reajustar
    $("#reajustar_parcelas").click(function(event) {
        event.preventDefault();

        // Obtenha os valores dos checkboxes selecionados
        var checkboxesSelecionados = [];

        $("input[name='checkboxes[]']:checked").each(function() {
            checkboxesSelecionados.push($(this).val());
        });

        // Crie a URL com os valores dos checkboxes como parâmetros de consulta
        var url = "{{ route('parcela_reajustar') }}?checkboxes=" + checkboxesSelecionados.join(',');

        // Redirecione para a URL com os parâmetros
        window.location.href = url;
    });
    // Captura o clique no Parcelas Reajustar
    $("#alterar_vencimento").click(function(event) {
        event.preventDefault();

        // Obtenha os valores dos checkboxes selecionados
        var checkboxesSelecionados = [];

        $("input[name='checkboxes[]']:checked").each(function() {
            checkboxesSelecionados.push($(this).val());
        });

        // Crie a URL com os valores dos checkboxes como parâmetros de consulta
        var url = "{{ route('alterar_vencimento') }}?checkboxes=" + checkboxesSelecionados.join(',');

        // Redirecione para a URL com os parâmetros
        window.location.href = url;
    });

});
</script>