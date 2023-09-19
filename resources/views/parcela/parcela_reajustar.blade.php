@extends('layouts/app')

@section('conteudo')

<h2>
    Reajustar Parcelas
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
        <form class="row g-3" action="{{ '/debito/cadastrar/' . Auth::user()->id . '/' }}" method="post"
            autocomplete="off">
            @csrf
           
            <div class="col-md-3">
                <label for="inputQtndParcelas" id="quantidade_parcela" class="form-label">Quantidade de parcelas*</label>
                <input type="text" name="quantidade_parcela" value="{{ old('quantidade_parcela') }}"
                    class="form-control @error('quantidade_parcela') is-invalid @enderror" id="inputQtndParcelas">
            </div>
            
            <div class="col-12">
                <button type="submit" class="btn-submit">
                    Reajustar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection