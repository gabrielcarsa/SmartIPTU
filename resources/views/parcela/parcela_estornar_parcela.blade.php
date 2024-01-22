@extends('layouts/app')

@section('conteudo')

<h2>
    Estornar Parcela
</h2>

<div class="card">
    <h5 class="card-header">Parcelas</h5>
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
            action="{{ isset($data['parcelaReceberOutros']) ? '/contas_receber/estornar_parcela/' . Auth::user()->id : (isset($data['parcelaPagarOutros']) ? '/contas_pagar/estornar_parcela/' . Auth::user()->id : '/parcela/estornar_parcela/' . Auth::user()->id . '?origem=' . request()->input('origem').'&lote_id=' . request()->input('lote_id')) }}"
            method="post" autocomplete="off">
            @csrf
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Valor</th>
                        <th scope="col">Data Vencimento</th>
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
                                value="{{  $parcela[0]->valor_parcela }}"
                                readonly
                                class="form-control @error('valor_parcela') is-invalid @enderror">
                        </th>
                        <th scope="row">
                            <input type="text" name="data_vencimento_parcela"
                                value="{{ \Carbon\Carbon::parse( $parcela[0]->data_vencimento )->format('d/m/Y') }}"
                                readonly
                                class="form-control @error('data_vencimento_parcela') is-invalid @enderror"
                                id="inputDataVencimentoParcelas">
                        </th>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>

            <div class="col-12">
                <button type="submit" class="btn-submit">
                    Confirmar operação
                </button>
            </div>
        </form>
    </div>
</div>


@endsection