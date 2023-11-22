@extends('layouts/app')

@section('conteudo')

<div class="container-md text-center">
    <div class="row align-items-center">
        <div class="col card-info">
            <p><span class="material-symbols-outlined">
                    home_work
                </span> Quadra</p>
            <h4>{{ $resultados[0]->quadra_nome }}</h4>
        </div>
        <div class="col card-info">
            <p><span class="material-symbols-outlined">
                    home_work
                </span> Lote</p>
            <h4>{{ $resultados[0]->lote }}</h4>
        </div>
        <div class="col card-info">
            <p> <span class="material-symbols-outlined">
                    person_pin
                </span>Responsabilidade</p>
            @if (!empty($resultados[0]->nome_cliente))
            <h4>{{ $resultados[0]->nome_cliente }}</h4>
            @elseif (!empty($resultados[0]->razao_social_cliente))
            <h4>{{ $resultados[0]->razao_social_cliente }}</h4>
            @endif
        </div>
        <div class="col card-info">
            <p> <span class="material-symbols-outlined">
                    domain
                </span> Inscrição Municipal</p>
            <h4>{{ $resultados[0]->inscricao_municipal }}</h4>
        </div>
        <div class="col card-info">
            <p> <span class="material-symbols-outlined">
                    receipt_long
                </span> Total Débitos</p>
            <h4>R$ {{number_format($totalValorParcelas, 2, ',', '.')}}</h4>
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

<a class="btn btn-primary btn-add" id="baixar_parcela" href="{{route('baixar_parcela')}}" style="margin-bottom: 20px">
    <span class="material-symbols-outlined">
        payments
    </span>
    Baixar parcelas
</a>
<a class="btn btn-primary btn-add" id="baixar_parcela"
    href="{{route('prescricao', ['lote_id' => $resultados[0]->lote_id])}}" style="margin-bottom: 20px">
    <span class="material-symbols-outlined">
        gavel
    </span>
    Prescrições
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
        <table class="table">
            <form action="" method="post">
                @csrf
                <thead>
                    <tr class="text-center">
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
                    <tr
                        class="resultados-table text-center @if (\Carbon\Carbon::parse($resultado->data_vencimento_parcela)->isPast() && $resultado->situacao_parcela == 0) parcela_atrasada @elseif ($resultado->situacao_parcela == 1) parcela_paga @endif">
                        @if($resultado->situacao_parcela == 0)
                        <th scope="row"><input type="checkbox" id="" name="checkboxes[]"
                                value="{{ $resultado->parcela_id }}" /></th>
                        @else
                        <th scope="row"></th>
                        @endif
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
                        <th scope="row">R$ {{ number_format($resultado->valor_parcela, 2, ',', '.') }}</th>
                        <th scope="row">R$ {{ number_format($resultado->valor_pago_parcela, 2, ',', '.') }}</th>
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
        var url = "{{ route('parcela_reajustar') }}?checkboxes=" + checkboxesSelecionados.join(',') + "&origem=lote_gestao&lote_id={{$resultados[0]->lote_id}}";

        // Redirecione para a URL com os parâmetros
        window.location.href = url;
    });

    // Captura o clique no Alterar Data Vencimento
    $("#alterar_vencimento").click(function(event) {
        event.preventDefault();

        // Obtenha os valores dos checkboxes selecionados
        var checkboxesSelecionados = [];

        $("input[name='checkboxes[]']:checked").each(function() {
            checkboxesSelecionados.push($(this).val());
        });

        // Crie a URL com os valores dos checkboxes como parâmetros de consulta
        var url = "{{ route('alterar_vencimento') }}?checkboxes=" + checkboxesSelecionados.join(',') + "&origem=lote_gestao";

        // Redirecione para a URL com os parâmetros
        window.location.href = url;
    });

    $("#baixar_parcela").click(function(event) {
        event.preventDefault();

        // Obtenha os valores dos checkboxes selecionados
        var checkboxesSelecionados = [];

        $("input[name='checkboxes[]']:checked").each(function() {
            checkboxesSelecionados.push($(this).val());
        });

        // Crie a URL com os valores dos checkboxes como parâmetros de consulta
        var url = "{{ route('baixar_parcela') }}?checkboxes=" + checkboxesSelecionados.join(',') + "&origem=lote_gestao";

        // Redirecione para a URL com os parâmetros
        window.location.href = url;
    });

    // Selecionar todos checkboxes
    $("#selecionar_todos").click(function() {
        // Obtém o estado atual do "Selecionar Todos" dentro da tabela atual
        var selecionarTodos = $(this).prop('checked');

        // Encontra os checkboxes individuais dentro da tabela atual e marca ou desmarca com base no estado do "Selecionar Todos"
        $(this).closest('table').find("input[name='checkboxes[]']").prop('checked', selecionarTodos);
    });

});
</script>