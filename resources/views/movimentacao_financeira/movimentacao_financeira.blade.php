@extends('layouts/app')

@section('conteudo')

@if(isset($data['contas_pagar_atrasadas']) && !$data['contas_pagar_atrasadas']->isEmpty())
<!-- MODAL PARCELAS ATRASO -->
<div class="modal modal-lg fade" id="parcelasEmAberto" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <p class="modal-title fs-5 fw-semibold" id="exampleModalLabel">
                    Lembrete parcelas em aberto
                </p>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="p-3">
                <p>
                    Há <span class="fw-semibold">parcelas de conta fixa</span> que estão em aberto há mais de 20 dias.
                </p>
                <p>
                    Algumas dessas já foram pagas? Se houver lance a baixa.
                </p>
                <ul class="list-group">

                    @foreach($data['contas_pagar_atrasadas'] as $parcela)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <p class="m-0">
                            {{$parcela->numero_parcela}}/{{$parcela->conta_pagar->quantidade_parcela}}
                        </p>
                        <p class="m-0">
                            {{$parcela->conta_pagar->categoria_pagar->descricao}}
                        </p>
                        <p class="m-0">
                            {{\Carbon\Carbon::parse($parcela->data_vencimento)->format('d/m/Y')}}
                        </p>
                        <p class="m-0">
                            R$ {{number_format($parcela->valor_parcela, 2, ',', '.')}}
                        </p>
                        <a href="{{ route('contas_pagar.listar', ['titular_conta_id' => 0, 'idParcela' => $parcela->id, 'referenteOutros' => true] ) }}"
                            class="btn btn-primary">
                            Baixar
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endif

<h2>Movimentação Financeira</h2>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="d-flex bg-white align-items-center p-3 shadow-sm rounded">
            <span class="material-symbols-outlined bg-light p-2 rounded fs-3 text-success bg-gray-100">
                trending_up
            </span>
            <div class="ml-3">
                <p class="m-0 fw-semibold">
                    Entradas de hoje
                </p>
                <p class="m-0">
                    R$ {{isset($data['entradas']) ? number_format($data['entradas'], 2, ',', '.') : '0'}}
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="d-flex bg-white align-items-center p-3 shadow-sm rounded">
            <span class="material-symbols-outlined bg-light p-2 rounded fs-3 text-danger bg-gray-100">
                trending_down
            </span>
            <div class="ml-3">
                <p class="m-0 fw-semibold">
                    Saídas de hoje
                </p>
                <p class="m-0">
                    R$ {{isset($data['saidas']) ? number_format($data['saidas'], 2, ',', '.') : '0'}}
                </p>
            </div>
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

<div class="bg-white p-3 rounded shadow-sm my-3">
    <h5 class="d-flex align-items-center">
        <span class="material-symbols-outlined mr-1">
            filter_alt
        </span>
        Filtros para buscar
    </h5>
    <div class="mt-3">
        <form class="row g-3" action="/movimentacao_financeira/listar" method="get" autocomplete="off">
            @csrf
            <div class="col-md-2">
                <label for="inputData" class="form-label">Data início</label>
                <input type="date" name="data" value="{{request('data')}}"
                    class="form-control @error('data') is-invalid @enderror" id="inputData">
            </div>
            <div class="col-md-2">
                <label for="inputData" class="form-label">Data fim</label>
                <input type="date" name="data_fim" value="{{request('data_fim')}}"
                    class="form-control @error('data_fim') is-invalid @enderror" id="inputData">
            </div>
            <div class="col-md-3">
                <label for="inputTitularConta" class="form-label">Titular da Conta*</label>
                <select id="inputTitularConta" name="titulares_conta"
                    class="form-select form-control @error('titulares_conta') is-invalid @enderror">
                    <option value="0">-- Selecione --</option>
                    @foreach ($data['titulares_conta'] as $titular)
                    <option value="{{ $titular->id }}"
                        {{ request('titulares_conta') == $titular->id ? 'selected' : '' }}>
                        @if(empty($titular->cliente->nome))
                        {{$titular->cliente->razao_social}}
                        @else
                        {{$titular->cliente->nome}}
                        @endif
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3" id="CampoContaCorrente">
                <label for="inputContaCorrente" class="form-label">Conta Corrente*</label>
                <select id="inputContaCorrente" name="conta_corrente" class="form-select form-control">
                    <option value="0">-- Selecione --</option>
                </select>
            </div>

            <div class="col-12">
                <button type="submit" class="btn-submit">Consultar</button>
                <a href="{{ route('nova_movimentacao') }}" class="btn-add"><span class="material-symbols-outlined">
                        add
                    </span>Nova Movimentação</a>
            </div>
        </form>
    </div>
</div>

@if(isset($movimentacao))

<!-- SALDOS -->
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="d-flex bg-white align-items-center p-3 shadow-sm rounded">
            <span class="material-symbols-outlined bg-light p-2 rounded fs-3 text-primary bg-gray-100">
                date_range
            </span>
            <div class="ml-3">
                <p class="m-0 fw-semibold">
                    Saldo anterior
                    {{isset($dados['saldo_anterior'][0]) ? \Carbon\Carbon::parse($dados['saldo_anterior'][0]->data)->format('d/m/Y') : ''}}
                </p>
                <p class="m-0">
                    @if(isset($data['saldo_anterior'][0]))
                    R$ {{number_format($data['saldo_anterior'][0]->saldo, 2, ',', '.')}}
                    @else
                    Não encontrado
                    @endif
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="d-flex bg-white align-items-center p-3 shadow-sm rounded">
            <span class="material-symbols-outlined bg-light p-2 rounded fs-3 text-primary bg-gray-100">
                attach_money
            </span>
            <div class="ml-3">
                <p class="m-0 fw-semibold">
                    Saldo atual
                </p>
                <p class="m-0">
                    @if(isset($data['saldo_atual'][0]))
                    R$ {{number_format($data['saldo_atual'][0]->saldo, 2, ',', '.')}}
                    @else
                    Não encontrado
                    @endif
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="d-flex bg-white align-items-center p-3 shadow-sm rounded">
            <span class="material-symbols-outlined bg-light p-2 rounded fs-3 text-primary bg-gray-100">
                storefront
            </span>
            <div class="ml-3">
                <p class="m-0 fw-semibold">
                    Titular
                </p>
                <p class="m-0 text-truncate" style="max-width: 50%">
                    @if(isset($data['movimentacao'][0]))
                    {{$movimentacao[0]->titular_conta->cliente->nome ?? $movimentacao[0]->titular_conta->cliente->razao_social}}
                    @endif
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="d-flex bg-white align-items-center p-3 shadow-sm rounded">
            <span class="material-symbols-outlined bg-light p-2 rounded fs-3 text-primary bg-gray-100">
                attach_money
            </span>
            <div class="ml-3">
                <p class="m-0 fw-semibold">
                    Conta Corrente
                </p>
                <p class="m-0">
                    @if(isset($data['movimentacao'][0]))
                    {{$movimentacao[0]->conta_corrente->apelido}}
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
<!-- FIM SALDOS -->

<div class="bg-white p-3 rounded shadow-sm">
    <h5 class="">
        Movimentação Financeira{!! isset($data['saldo_atual'][0]) ? " do dia <strong>" .
            \Carbon\Carbon::parse($data['saldo_atual'][0]->data)->format('d/m/Y') . "</strong>" : "" !!}
    </h5>
    <div class="my-3">
        <a class="btn btn-add"
            href="../movimentacao_financeira/relatorio_pdf?data={{request('data')}}&data_fim={{request('data_fim')}}&titular={{request('titulares_conta')}}&conta_corrente={{request('conta_corrente')}}">
            PDF
        </a>
        <a class="btn btn-add" href="">
            Excel
        </a>
    </div>

    <div class="card-body">
        <table class="table table-striped table-bordered text-center">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Cliente / Fornecedor</th>
                    <th scope="col">Categoria</th>
                    <th scope="col">Descrição</th>
                    <th scope="col">Valor da Entrada</th>
                    <th scope="col">Valor da Saída</th>
                    <th scope="col">Saldo</th>
                    <th scope="col">Ordem</th>
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            @php
            $saldoAtual = isset($data['saldo_anterior'][0]) ? $data['saldo_anterior'][0]->saldo : 0; // Inicializa
            @endphp

            <tbody>
                @foreach ($movimentacao as $mov)
                <!-- Calcula o saldo atual com base na movimentação e no saldo anterior -->
                @php
                if ($mov->tipo_movimentacao == 0) { // Se for uma entrada
                $saldoAtual += $mov->valor; // Adiciona o valor da entrada ao saldo atual
                } else { // Se for uma saída
                $saldoAtual -= $mov->valor; // Subtrai o valor da saída do saldo atual
                }
                @endphp
                <tr>
                    <th scope="row">{{$mov->id}}</th>
                    @if($mov->cliente->tipo_cadastro == 0)
                    <td class="align-middle">{{$mov->cliente->nome}}</td>
                    @else
                    <td class="align-middle">{{$mov->cliente->razao_social}}</td>
                    @endif

                    @if($mov->tipo_movimentacao == 0)
                    <td class="align-middle">
                        {{$mov->tipo_debito == null ? $mov->categoria_receber->descricao : $mov->tipo_debito->descricao}}
                    </td>
                    @else
                    <td class="align-middle">
                        {{$mov->tipo_debito == null ? $mov->categoria_pagar->descricao : $mov->tipo_debito->descricao}}
                    </td>
                    @endif

                    <td class="align-middle">{{$mov->descricao}}</td>

                    @if($mov->tipo_movimentacao == 0)
                    <td class="align-middle text-success fw-bold">R$ {{number_format($mov->valor, 2, ',', '.')}}</td>
                    <td class="align-middle"></td>
                    <td class="align-middle">R$ {{number_format($saldoAtual, 2, ',', '.')}}</td>
                    @else
                    <td class="align-middle"></td>
                    <td class="align-middle text-danger fw-bold">R$ {{number_format($mov->valor, 2, ',', '.')}}</td>
                    <td class="align-middle">R$ {{number_format($saldoAtual, 2, ',', '.')}}</td>
                    @endif

                    <td class="align-middle">
                        <input type="text" style="width:60px;" name="movimentacoes[0][ordem]" value="{{ $mov->ordem }}"
                            class="form-control @error('ordem') is-invalid @enderror" id="inputOrdem">
                    </td>

                    @if($mov->tipo_movimentacao == 0)
                    <td class="">
                        <a href="/contas_receber/listar?titular_conta_id=0&idParcela={{$mov->parcela_conta_receber->id}}&{{$mov->parcela_conta_receber->debito == null ? 'referenteOutros=on' : 'referenteLotes=on'}}"
                            class="text-decoration-none">
                            Ver
                        </a>
                    </td>
                    @else
                    <td class="">
                        <a href="/contas_pagar/listar?titular_conta_id=0&idParcela={{$mov->parcela_conta_pagar->id}}&{{$mov->parcela_conta_pagar->debito == null ? 'referenteOutros=on' : 'referenteLotes=on'}}"
                            class="text-decoration-none">
                            Ver
                        </a>
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="card-footer">
            <p>Exibindo {{$movimentacao->count()}} de {{ $data['total_movimentacao'] }} registros</p>
        </div>

    </div>
</div>
@endif

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    $('#parcelasEmAberto').modal('show'); // Exibe o modal automaticamente
});

$(document).ready(function() {
    // Quando o valor do input de ordem é alterado
    $('input[name^="movimentacoes"]').change(function() {
        var newValue = $(this).val(); // Obter o novo valor de ordem
        var movimentacaoId = $(this).closest('tr').find('th').text(); // Obter o ID da movimentação
        var row = $(this).closest('tr'); // Referência à linha da tabela

        // Enviar uma solicitação AJAX para atualizar a ordem
        $.ajax({
            _token: '{{ csrf_token() }}',
            url: '/movimentacao_financeira/alterar_ordem',
            method: 'GET',
            data: {
                movimentacao_id: movimentacaoId,
                nova_ordem: newValue
            },
            success: function(response) {

                // Recarrega a página atual
                location.reload();
            },
            error: function(xhr, status, error) {
                // Se ocorrer um erro durante a solicitação AJAX, você pode lidar com isso aqui
                console.error(error);
            }
        });


    });

    // Quando o titular da conta é selecionado
    $('#inputTitularConta').change(function() {
        var selectedTitularContaId = $('#inputTitularConta').val();

        if (selectedTitularContaId > 0) {
            // Fazer uma solicitação AJAX para obter as contas bancárias do titular da conta selecionado
            $.get('/movimentacao_financeira/conta_corrente/' + selectedTitularContaId, function(data) {
                var contaBancariaField = $('#CampoContaCorrente');
                var selectContaBancaria = $('#inputContaCorrente');
                selectContaBancaria.empty();

                // Adicionar as opções de contas bancárias
                $.each(data, function(key, value) {
                    selectContaBancaria.append($('<option>', {
                        value: value.id,
                        text: value.apelido
                    }));
                });

                // Mostrar o campo de contas bancárias
                contaBancariaField.show();
            });
        } else {
            // Se o titular da conta não for selecionado, ocultar o campo de contas bancárias e defina a opção padrão
            var contaBancariaField = $('#CampoContaCorrente');
            var selectContaBancaria = $('#inputContaCorrente');
            selectContaBancaria.empty();
            selectContaBancaria.append($('<option>', {
                value: 0,
                text: '-- Selecione o Titular da Conta --'
            }));
            contaBancariaField.hide();
        }
    });
});
</script>


@endsection