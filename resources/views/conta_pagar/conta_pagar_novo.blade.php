@extends('layouts/app')

@section('conteudo')

<h2>
    Nova Despesa
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

        <form class="row g-3" action="{{ '/contas_pagar/cadastrar/' . Auth::user()->id }}" method="post"
            autocomplete="off">
            @csrf
            <div class="col-md-4">
                <label for="inputTitularReceber" class="form-label">Titular da despesa*</label>
                <select id="inputTitularReceber" name="titular_conta_id" class="form-select form-control">
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
            <div class="col-md-4">
                <label for="inputCliente" class="form-label">Fornecedor</label>
                <select id="inputCliente" name="cliente_id"
                    class="form-select form-control @error('cliente_id') is-invalid @enderror">
                    <option value="0" {{ old('cliente_id') == 0 ? 'selected' : '' }}>-- Selecione --</option>
                    @foreach ($data['clientes'] as $cliente)
                    <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                        @if(empty($cliente->nome))
                        {{$cliente->razao_social}}
                        @else
                        {{$cliente->nome}}
                        @endif
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="inputDescricao" class="form-label">Categoria*</label>
                <select id="inputDescricao" name="categoria_pagar_id"
                    class="form-select form-control @error('categoria_pagar_id') is-invalid @enderror">
                    <option value="0" {{ old('categoria_pagar_id') == 0 ? 'selected' : '' }}>-- Selecione --</option>
                    @foreach ($data['categorias'] as $cat)
                    <option value="{{ $cat->id }}" {{ old('categoria_pagar_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->descricao }}

                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label for="inputDescricao" id="descricao" class="form-label">Descrição</label>
                <input type="text" name="descricao" value="{{ old('descricao') }}"
                    class="form-control @error('descricao') is-invalid @enderror" id="inputDescricao">
            </div>
            <div class="col-md-2">
                <label for="inputQtndParcelas" id="quantidade_parcela" class="form-label">Quantidade de
                    parcelas*</label>
                <input type="text" name="quantidade_parcela" value="{{ old('quantidade_parcela') }}"
                    class="form-control @error('quantidade_parcela') is-invalid @enderror" id="inputQtndParcelas">
            </div>
            <div class="col-md-2">
                <label for="inputVencimento1Parcela" id="data_vencimento" class="form-label">Vencimento da 1
                    parcela*</label>
                <input type="date" name="data_vencimento" value="{{ old('data_vencimento') }}"
                    class="form-control @error('data_vencimento') is-invalid @enderror" id="inputVencimento1Parcela">
            </div>

            <div class="col-md-2">
                <label for="inputValorParcela" id="valor_parcela" class="form-label">Valor da parcela*</label>
                <input type="text" name="valor_parcela" value="{{ old('valor_parcela') }}"
                    class="form-control @error('valor_parcela') is-invalid @enderror" id="inputValorParcela">
            </div>
            <div class="col-md-2">
                <label for="inputValorEntrada" id="valor_entrada" class="form-label">Valor da Entrada</label>
                <input type="text" name="valor_entrada" value="{{ old('valor_entrada') }}"
                    class="form-control @error('valor_entrada') is-invalid @enderror" id="inputValorEntrada">
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
    $(document).on('input', 'input[id^="inputValorEntrada"]', function() {
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
    $(document).on('input', 'input[id^="inputValorParcela"]', function() {
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
</script>
@endsection