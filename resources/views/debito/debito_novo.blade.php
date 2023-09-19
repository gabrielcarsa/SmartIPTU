@extends('layouts/app')

@section('conteudo')

<h2>
    Adicionar Parcelas
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
        <form class="row g-3" action="{{ '/debito/cadastrar/' . Auth::user()->id . '/' . $data['lote_id'] }}" method="post"
            autocomplete="off">
            @csrf
            <div class="col-md-3">
                <label for="inputTipoDebito" class="form-label">Tipo de débito*</label>
                <select id="inputTipoDebito" name="tipo_debito_id"
                    class="form-select form-control @error('tipo_debito_id') is-invalid @enderror">
                    <option value="0" {{ old('tipo_debito_id') == 0 ? 'selected' : '' }}>-- Selecione --</option>
                    @foreach ($data['tipo_debito'] as $tipo)
                    <option value="{{ $tipo->id }}" {{ old('tipo_debito_id') == $tipo->id ? 'selected' : '' }}>
                        {{ $tipo->descricao }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="inputQtndParcelas" id="quantidade_parcela" class="form-label">Quantidade de parcelas*</label>
                <input type="text" name="quantidade_parcela" value="{{ old('quantidade_parcela') }}"
                    class="form-control @error('quantidade_parcela') is-invalid @enderror" id="inputQtndParcelas">
            </div>
            <div class="col-md-3">
                    <label for="inputVencimento1Parcela" id="data_vencimento" class="form-label">Vencimento da 1 parcela*</label>
                <input type="date" name="data_vencimento" value="{{ old('data_vencimento') }}"
                    class="form-control @error('data_vencimento') is-invalid @enderror" id="inputVencimento1Parcela">
            </div>
            <div class="col-md-3">
                <label for="inputDescricaoDebito" class="form-label">Descrição*</label>
                <select id="inputDescricaoDebito" name="descricao_debito_id"
                    class="form-select form-control @error('descricao_debito_id') is-invalid @enderror">
                    <option value="0" {{ old('descricao_debito_id') == 0 ? 'selected' : '' }}>-- Selecione --</option>
                    @foreach ($data['descricao_debito'] as $descricao)
                    <option value="{{ $descricao->id }}" {{ old('descricao_debito_id') == $descricao->id ? 'selected' : '' }}>
                        {{ $descricao->descricao }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="inputValorParcela" id="valor_parcela" class="form-label">Valor da parcela*</label>
                <input type="text" name="valor_parcela" value="{{ old('valor_parcela') }}"
                    class="form-control @error('valor_parcela') is-invalid @enderror" id="inputValorParcela">
            </div>
            <div class="col-md-3">
                <label for="inputValorEntrada" id="valor_entrada" class="form-label">Valor da Entrada</label>
                <input type="text" name="valor_entrada" value="{{ old('valor_entrada') }}"
                    class="form-control @error('valor_entrada') is-invalid @enderror" id="inputValorEntrada">
            </div>
            <div class="col-md-3">
                <label for="inputObservacao" id="observacao" class="form-label">Observação</label>
                <input type="text" name="observacao" value="{{ old('observacao') }}"
                    class="form-control @error('observacao') is-invalid @enderror" id="inputObservacao">
            </div>
            <div class="col-12">
                <button type="submit" class="btn-submit">
                    @if (isset($lote))
                    Alterar
                    @else
                    Cadastrar
                    @endif
                </button>
            </div>
        </form>
    </div>
</div>
@endsection