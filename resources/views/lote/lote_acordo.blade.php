@extends('layouts/app')

@section('conteudo')

<h2>
    Acordo de Renegociação de IPTU
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
        <h5>QD. {{$data['quadra']->nome}} / LT. {{$data['lote']->lote}}</h5>

        <form class="row g-3" action="{{'/lote/acordo/' . $data['lote']->id . '/' .Auth::user()->id}}" method="post" autocomplete="off">
            @csrf
            <div class="col-md-3">
                <label for="inputDataContrato" id="data_contrato" class="form-label">Data do Contrato*</label>
                <input type="date" name="data_contrato" value="{{ old('data_contrato') }}"
                    class="form-control @error('data_contrato') is-invalid @enderror" id="inputDataContrato">
            </div>

            <div class="col-12">
                <button type="submit" class="btn-submit">
                    Cadastrar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection