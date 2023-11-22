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
        <form class="row g-3" action="{{ isset($parcelaReceberOutros) ? '/contas_receber/definir_baixar_parcela/' . Auth::user()->id : (isset($parcelaPagarOutros) ? '/contas_pagar/definir_baixar_parcela/' . Auth::user()->id : '/parcela/definir_baixar_parcela/' . Auth::user()->id . '?origem=' . request()->input('origem').'&lote_id=' . request()->input('lote_id')) }}"
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
                            <input type="text" name="valor_parcela" value="{{ number_format($parcela[0]->valor_parcela, 2, ',', '.') }}" readonly
                                disabled class="form-control @error('valor_parcela') is-invalid @enderror"
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
                            <input type="text" name="valor[]" value="{{ old('valor.' . $index) != null ?  old('valor.' . $index) : '' }}"
                                class="form-control valor_pago @error('valor.' . $index) is-invalid @enderror"
                                id="inputValor">
                        </th>
                        <th scope="row">
                            <input type="date" name="data[]"
                                value="{{ old('data.' . $index) != null ?  old('data.' . $index) : '' }}"
                                class="form-control @error('data.' . $index) is-invalid @enderror" id="">
                        </th>
                        <th scope="row">

                        </th>
                    </tr>

                    @endforeach
                    @endif
                </tbody>

            </table>
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
    $('#inputValor').mask('000.000.000.000.000,00', { reverse: true });
  });
</script>

@endsection