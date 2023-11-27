@extends('layouts/app')

@section('conteudo')

<h2>Movimentação Financeira</h2>

<div class="row">
    <div class="col-md-3">
        <div class="card-movimentacao d-flex align-items-center" style="background-color:RGB(0, 218, 255);">
            <div class="row">
                <div class="col-md-4">
                    <span class="material-symbols-outlined">
                        attach_money
                    </span>
                </div>
                <div class="col-md-8 align-self-center">
                    <h3>Entradas de Hoje</h3>
                    <p>R$ {{isset($data['entradas']) ? number_format($data['entradas'], 2, ',', '.') : '0'}}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-movimentacao d-flex align-items-center" style="background-color:RGB(250, 82, 82);">
            <div class="row">
                <div class="col-md-4">
                    <span class="material-symbols-outlined">
                        money_off
                    </span>
                </div>
                <div class="col-md-8 align-self-center">
                    <h3>Saídas de Hoje</h3>
                    <p>R$ {{isset($data['saidas']) ? number_format($data['saidas'], 2, ',', '.') : '0'}}</p>
                </div>
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

<div class="card">
    <h5 class="card-header">Filtros para buscar</h5>
    <div class="card-body">
        <form class="row g-3" action="/movimentacao_financeira/listar" method="get" autocomplete="off">
            @csrf
            <div class="col-md-2">
                <label for="inputData" class="form-label">Data da movimentação</label>
                <input type="date" name="data" value="{{request('data')}}" class="form-control @error('data') is-invalid @enderror" id="inputData">
            </div>
            <div class="col-md-3">
                <label for="inputTitularConta" class="form-label">Titular da Conta*</label>
                <select id="inputTitularConta" name="titulares_conta"
                    class="form-select form-control @error('titulares_conta') is-invalid @enderror">
                    <option value="0" {{ old('titulares_conta') == 0 ? 'selected' : '' }}>-- Selecione --</option>
                    @foreach ($data['titulares_conta'] as $titular)
                    <option value="{{ $titular->id }}" {{ old('titulares_conta') == $titular->id ? 'selected' : '' }}>
                        @if(empty($titular->nome))
                        {{$titular->razao_social}}
                        @else
                        {{$titular->nome}}
                        @endif
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3" id="CampoContaCorrente">
                <label for="inputContaCorrente" class="form-label">Conta Corrente*</label>
                <select id="inputContaCorrente" name="conta_corrente" class="form-select form-control">
                    <option value="0" selected>-- Selecione --</option>
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



<div class="card">
    @if(isset($movimentacao))
    <h5 class="card-header">
        Movimentação Financeira{!! isset($data['saldo_atual'][0]) ? " do dia <strong>" .
            \Carbon\Carbon::parse($data['saldo_atual'][0]->data)->format('d/m/Y') . "</strong>" : "" !!}
    </h5>
    <div class="card-footer">
        <a class="btn btn-add"
            href="../movimentacao_financeira/relatorio_pdf?data={{request('data')}}&titular={{request('titulares_conta')}}&conta_corrente={{request('conta_corrente')}}">PDF</a>
        <a class="btn btn-add" href="">Excel</a>
    </div>
    <div class="card-saldo">
        <div class="row">
            <div class="col">
                @if(isset($data['saldo_anterior'][0]))
                <p>Saldo Inicial:
                    <span id="saldo">R$ {{number_format($data['saldo_anterior'][0]->saldo, 2, ',', '.')}}</span>
                </p>
                @endif
            </div>
            <div class="col text-right">
                @if(isset($data['saldo_atual'][0]))
                <p>Saldo em Banco:
                    <span id="saldo">R$ {{number_format($data['saldo_atual'][0]->saldo, 2, ',', '.')}}</span>
                </p>
                @endif
            </div>
        </div>
    </div>
    @endif
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
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($movimentacao))
                @foreach ($movimentacao as $mov)
                <tr>
                    <th scope="row">{{$mov->id}}</th>
                    @if($mov->tipo_cadastro == 0)
                    <td class="align-middle">{{$mov->nome}}</td>
                    @else
                    <td class="align-middle">{{$mov->razao_social}}</td>
                    @endif

                    @if($mov->tipo_movimentacao == 0)
                    <td class="align-middle">{{$mov->tipo_debito == null ? $mov->categoria_receber : $mov->tipo_debito}}
                    </td>
                    @else
                    <td class="align-middle">{{$mov->tipo_debito == null ? $mov->categoria_pagar : $mov->tipo_debito}}
                    </td>
                    @endif

                    <td class="align-middle">{{$mov->descricao}}</td>

                    @if($mov->tipo_movimentacao == 0)
                    <td class="align-middle entradaMovimentacao">R$ {{number_format($mov->valor, 2, ',', '.')}}</td>
                    <td class="align-middle"></td>
                    @else
                    <td class="align-middle"></td>
                    <td class="align-middle saidaMovimentacao">R$ {{number_format($mov->valor, 2, ',', '.')}}</td>
                    @endif

                    @if($mov->tipo_movimentacao == 0)
                    <td class="d-flex align-items-center">
                        <a href="/contas_receber/listar?titular_conta_id=0&idParcela={{$mov->id_parcela_receber}}&{{$mov->parcela_receber_debito == null ? 'referenteOutros=on' : 'referenteLotes=on'}}"
                            class="btn-icone-listagem">
                            <span class="material-symbols-outlined">
                                visibility
                            </span>
                        </a>
                    </td>
                    @else
                    <td class="d-flex align-items-center">
                        <a href="/contas_pagar/listar?titular_conta_id=0&idParcela={{$mov->id_parcela_pagar}}&{{$mov->parcela_pagar_debito == null ? 'referenteOutros=on' : 'referenteLotes=on'}}"
                            class="btn-icone-listagem">
                            <span class="material-symbols-outlined">
                                visibility
                            </span>
                        </a>
                    </td>
                    @endif
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        @if(isset($movimentacao))
        <div class="card-footer">
            <p>Exibindo {{$movimentacao->count()}} de {{ $data['total_movimentacao'] }} registros</p>
        </div>
        @endif

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {

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