@extends('layouts/app')

@section('conteudo')

<h2>
    Baixar Parcelas
</h2>

<div class="card">
    <h5 class="card-header">Parcelas</h5>
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

    <div class="card-body">
        <form class="row g-3"
            action="{{ isset($data['parcelaReceberOutros']) ? '/contas_receber/definir_baixar_parcela/' . Auth::user()->id : (isset($data['parcelaPagarOutros']) ? '/contas_pagar/definir_baixar_parcela/' . Auth::user()->id : '/parcela/definir_baixar_parcela/' . Auth::user()->id . '?origem=' . request()->input('origem').'&lote_id=' . request()->input('lote_id')) }}"
            method="post" autocomplete="off">
            @csrf
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Valor</th>
                        <th scope="col">Data Vencimento</th>
                        <th scope="col">Valor</th>
                        <th scope="col">Data</th>
                        <th scope="col">Ordem</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($parcelas))
                    @foreach($parcelas as $index => $parcela)

                    <tr class="resultados-table">
                        <th scope="row">
                            <input type="text" name="" value="{{ $parcela[0]->id }}" readonly disabled
                                class="form-control @error('id_parcela') is-invalid @enderror" id="inputIdParcelas">
                            <input type="hidden" name="id_parcela[]" value="{{ $parcela[0]->id }}">
                        </th>
                        <th scope="row">
                            <input type="text" name="valor_parcela"
                                value="{{ number_format($parcela[0]->valor_parcela, 2, ',', '.') }}" readonly disabled
                                class="form-control @error('valor_parcela') is-invalid @enderror"
                                id="inputValorParcelas">
                        </th>
                        <th scope="row">
                            <input type="text" name="data_vencimento_parcela"
                                value="{{ \Carbon\Carbon::parse( $parcela[0]->data_vencimento )->format('d/m/Y') }}"
                                readonly disabled
                                class="form-control @error('data_vencimento_parcela') is-invalid @enderror"
                                id="inputDataVencimentoParcelas">
                        </th>
                        <th scope="row">
                            <input type="text" name="valor[]"
                                value="{{ old('valor.' . $index) != null ?  old('valor.' . $index) : '' }}"
                                class="form-control valor_pago @error('valor.' . $index) is-invalid @enderror"
                                id="inputValor">
                        </th>
                        <th scope="row">
                            <input type="date" name="data[]"
                                value="{{ old('data.' . $index) != null ?  old('data.' . $index) : '' }}"
                                class="form-control @error('data.' . $index) is-invalid @enderror" id="">
                        </th>
                        <th scope="row">
                            <input type="text" style="width:80px" name="ordem[]" value="{{ old('ordem') }}"
                                class="form-control @error('ordem') is-invalid @enderror" id="inputOrdem">
                        </th>
                    </tr>

                    @endforeach
                    @endif
                </tbody>
            </table>
            <hr>

            <div class="row row-form-destacar">
                <div class="col-md-4">
                    <label for="inputTitularConta" class="form-label">Titular da Conta*</label>
                    <select id="inputTitularConta" name="titular_conta_id" class="form-select form-control">
                        <option value="0" {{ old('titular_conta_id') == 0 ? 'selected' : '' }}>-- Selecione --
                        </option>
                        @foreach ($data['titular_conta'] as $t)
                        <option value="{{ $t->id_titular_conta }}"
                            {{ old('titular_conta_id') == $t->id_titular_conta ? 'selected' : '' }}>
                            @if(empty($t->nome))
                            {{$t->razao_social}}
                            @else
                            {{$t->nome}}
                            @endif
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4" id="contaBancariaField">
                    <label for="inputContaCorrente" class="form-label">Conta Corrente*</label>
                    <select id="inputContaCorrente" name="conta_corrente_id" class="form-select form-control">
                        <option value="0" selected> Selecione --</option>
                    </select>
                </div>
                <div class="col-md-2" >
                    <label for="inputParcial" class="form-label">Baixa parcial? </label>
                    <input id="inputParcial" type="checkbox" name="baixa_parcial">
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn-submit">
                    Baixar parcelas
                </button>
            </div>
        </form>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>


<script>
$(document).ready(function() {
    $(document).on('input', 'input[name^="valor"]', function() {
        // Remova os caracteres não numéricos
        var unmaskedValue = $(this).val().replace(/\D/g, '');

        // Adicione a máscara apenas ao input de valor relacionado à mudança
        $(this).val(mask(unmaskedValue));
    });

    function mask(value) {
        // Converte o valor para número
        var numberValue = parseFloat(value) / 100;

        // Formata o número com vírgula como separador decimal e duas casas decimais
        return numberValue.toLocaleString('pt-BR', {
            minimumFractionDigits: 2
        });
    }
});

$(document).ready(function() {
    // Quando o titular da conta é selecionado
    $('#inputTitularConta').change(function() {
        var selectedTitularContaId = $('#inputTitularConta').val();

        if (selectedTitularContaId > 0) {
            // Fazer uma solicitação AJAX para obter as contas bancárias do titular da conta selecionado
            $.get('/movimentacao_financeira/conta_corrente/' + selectedTitularContaId, function(data) {
                var contaBancariaField = $('#contaBancariaField');
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
            var contaBancariaField = $('#contaBancariaField');
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