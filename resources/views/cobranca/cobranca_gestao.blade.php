@extends('layouts/app')

@section('conteudo')

<h2>
    Cobrança
</h2>

<div class="card">
    <h5 class="card-header">*</h5>
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
        <form class="row g-3" action="" method="post" autocomplete="off">
            @csrf
            <div class="col-md-3">
                <label for="inputEmpreendimento" class="form-label">Selecione o empreendimento*</label>
                <select id="inputEmpreendimento" name="empreendimento_id"
                    class="form-select form-control @error('empreendimento_id') is-invalid @enderror">
                    <option value="0" {{ old('empreendimento_id') == 0 ? 'selected' : '' }}>-- Selecione --</option>
                    @foreach ($data['empreendimentos'] as $empreendimento)
                    <option value="{{$empreendimento->id}}" {{ old('empreendimento_id') == $empreendimento->id ? 'selected' : '' }}>
                        {{$empreendimento->nome}}
                    </option>
                    @endforeach
                </select>
            </div>
        
            <div class="col-12">
                <button type="submit" class="btn-submit">
                    Gerar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection