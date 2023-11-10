@extends('layouts/app')

@section('conteudo')

<h2>
    Nova Movimentação
</h2>

<div class="card">
    <h5 class="card-header">Preencha os campos requisitados *</h5>
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
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
        <form class="" action="{{ '/movimentacao_financeira/cadastrar/' . Auth::user()->id }}" method="post"
            autocomplete="off">
            @csrf
            <div class="row row-form row-form-destacar">
                <div class="col-md-2">
                    <label for="inputData" id="data" class="form-label">Data da movimentação*</label>
                    <input type="date" name="data" value="{{ old('data') }}"
                        class="form-control @error('data') is-invalid @enderror" id="inputData">
                </div>
                <div class="col-md-2 adicionar-linha d-flex align-items-center ms-auto">
                    <a href="" id="adicionarMovimentacao">+</a>
                </div>
            </div>
            <hr>
            <div class="row row-form movimentacao">
                <div class="col-md-2">
                    <label for="inputTipoMovimentacao" id="tipo_movimentacao" class="form-label">Tipo
                        Movimentação*</label>
                    <select id="inputTipoMovimentacao" name="tipo_movimentacao" class="form-select form-control">
                        <option value="0" select>-- Selecione --</option>
                        <option value="1">Entrada</option>
                        <option value="2">Saída</option>
                    </select>
                </div>

                <div class="col-md-3" id="categoriaField">
                    <label for="inputCategoria" class="form-label">Categoria*</label>
                    <select id="inputCategoria" name="categoria_id" class="form-select form-control">
                        <option value="0" selected>-- Selecione --</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="inputCliente" class="form-label">Cliente / Fornecedor*</label>
                    <select id="inputCliente" name="cliente_fornecedor_id"
                        class="form-select form-control @error('cliente_fornecedor_id') is-invalid @enderror">
                        <option value="0" {{ old('cliente_fornecedor_id') == 0 ? 'selected' : '' }}>-- Selecione --
                        </option>
                        @foreach ($data['clientes'] as $cliente)
                        <option value="{{ $cliente->id }}"
                            {{ old('cliente_fornecedor_id') == $cliente->id ? 'selected' : '' }}>
                            @if(empty($cliente->nome))
                            {{$cliente->razao_social}}
                            @else
                            {{$cliente->nome}}
                            @endif
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="inputValor" id="valor" class="form-label">Valor da Entrada / Saída*</label>
                    <input type="text" name="valor" value="{{ old('valor') }}"
                        class="form-control @error('valor') is-invalid @enderror" id="inputValor">
                </div>
                <div class="col-md-2">
                    <label for="inputDescricao" id="descricao" class="form-label">Descrição*</label>
                    <input type="text" name="descricao" value="{{ old('descricao') }}"
                        class="form-control @error('descricao') is-invalid @enderror" id="inputDescricao">
                </div>
            </div>
            <hr>
            <div class="row row-form row-form-destacar">
                <div class="col-md-4">
                    <label for="inputTitularConta" class="form-label">Titular da Conta*</label>
                    <select id="inputTitularConta" name="titular_conta_id" class="form-select form-control">
                        <option value="0" {{ old('titular_conta_id') == 0 ? 'selected' : '' }}>-- Selecione --</option>
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
                    <select id="inputContaCorrente" name="conta_corrente" class="form-select form-control">
                        <option value="0" selected> Selecione --</option>
                    </select>
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn-submit">
                    Cadastrar
                </button>
            </div>
        </form>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>



<script>
$(document).ready(function() {
    $('#inputValor').mask('000.000.000.000.000,00', {
        reverse: true
    });
});
$(document).ready(function() {
     // Adiciona uma nova linha de movimentação ao clicar em "+"
     $('#adicionarMovimentacao').click(function(e) {
        e.preventDefault();

        // Clona a div de movimentação
        var novaMovimentacao = $('.movimentacao:first').clone();

        // Limpa os valores dos campos clonados
        novaMovimentacao.find('input, select').val('');

        // Adiciona a nova div de movimentação no final do formulário
        $('.movimentacao:last').after(novaMovimentacao);
    });

   
    
    // Quando o tipo de movimentação é selecionado é selecionados
    $('#inputTipoMovimentacao').change(function() {
        var selectedTipoMovimentacao = $('#inputTipoMovimentacao').val();

        if (selectedTipoMovimentacao > 0) {
            // Fazer uma solicitação AJAX para obter as categorias do tipo selecionado
            if (selectedTipoMovimentacao == 1) { //Entrada
                $.get('/categoria_receber/json', function(data) {
                    var categoriaField = $('#categoriaField');
                    var selectCategoria = $('#inputCategoria');
                    selectCategoria.empty();

                    // Adicionar as opções de categoria
                    $.each(data, function(key, value) {
                        selectCategoria.append($('<option>', {
                            value: value.id,
                            text: value.descricao
                        }));
                    });

                    // Mostrar o campo de categoria
                    contaBancariaField.show();
                });
            } else { //Saída
                $.get('/categoria_pagar/json', function(data) {
                    var categoriaField = $('#categoriaField');
                    var selectCategoria = $('#inputCategoria');
                    selectCategoria.empty();

                    // Adicionar as opções de categoria
                    $.each(data, function(key, value) {
                        selectCategoria.append($('<option>', {
                            value: value.id,
                            text: value.descricao
                        }));
                    });

                    // Mostrar o campo de categoria
                    categoriaField.show();
                });
            }

        } else {
            // Se o tipo de movimentacao não for selecionado, ocultar o campo de categoria e defina a opção padrão
            var categoriaField = $('#categoriaField');
            var selectCategoria = $('#inputCategoria');
            selectCategoria.empty();

            selectCategoria.append($('<option>', {
                value: 0,
                text: '-- Selecione o Tipo de Movimentação --'
            }));
            categoriaField.hide();
        }
    });

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