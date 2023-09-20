@extends('layouts/app')

@section('conteudo')

<h2>
    Alterar Data de Vencimento
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
        <form class="row g-3"
            action="{{ '/parcela/definir_data_vencimento/' . Auth::user()->id . '?' . 'checkboxes=' . $_GET['checkboxes'] }}"
            method="post" autocomplete="off">
            @csrf
            <div class="col-md-4">
                <label for="inputDataVencimento" id="data_vencimento" class="form-label">Definir Data de Vencimento</label>
                <input type="date" name="data_vencimento" value="{{ old('data_vencimento') }}"
                    class="form-control @error('data_vencimento') is-invalid @enderror" id="inputDataVencimento">
            </div>
            <div class="col-12">
                <button type="submit" class="btn-submit">
                    Alterar
                </button>
            </div>
            <hr>
            @foreach($parcelas as $parcela)
            <div class="col-md-1">
                <label for="inputIdParcelas" id="id_parcela" class="form-label">ID</label>
                <input type="text" name="id_parcela" value="{{ $parcela[0]->id }}" readonly disabled
                    class="form-control @error('id_parcela') is-invalid @enderror" id="inputIdParcelas">
            </div>
            <div class="col-md-1">
                <label for="inputIdParcelas" id="numero_parcela" class="form-label">Nº parcela</label>
                <input type="text" name="numero_parcela"
                    value="{{ $parcela[0]->numero_parcela }} / {{ $parcela[0]->debito_quantidade_parcela }}" readonly
                    disabled class="form-control @error('numero_parcela') is-invalid @enderror" id="inputIdParcelas">
            </div>
            <div class="col-md-3">
                <label for="inputIdParcelas" id="descricao_parcela" class="form-label">Descrição</label>
                <input type="text" name="descricao_parcela" value="{{ $parcela[0]->descricao }}" readonly disabled
                    class="form-control @error('descricao_parcela') is-invalid @enderror" id="inputIdParcelas">
            </div>
            <div class="col-md-3">
                <label for="inputDataVencimentoParcelas" id="data_vencimento_parcela" class="form-label">Data de
                    Vencimento</label>
                <input type="text" name="data_vencimento_parcela"
                    value="{{ \Carbon\Carbon::parse( $parcela[0]->data_vencimento )->format('d/m/Y') }}" readonly
                    disabled class="form-control @error('data_vencimento_parcela') is-invalid @enderror"
                    id="inputDataVencimentoParcelas">
            </div>
            <div class="col-md-3">
                <label for="inputValorParcelas" id="valor_parcela" class="form-label">Valor</label>
                <input type="text" name="valor_parcela" value="{{ $parcela[0]->valor_parcela }}" readonly disabled
                    class="form-control @error('valor_parcela') is-invalid @enderror" id="inputValorParcelas">
            </div>

            <hr>

            @endforeach

        </form>
    </div>
</div>
@endsection