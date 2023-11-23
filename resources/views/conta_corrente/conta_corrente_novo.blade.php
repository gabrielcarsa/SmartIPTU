@extends('layouts/app')

@section('conteudo')

<h2>
    @if (isset($empreendimento))
    Alterar Conta 
    @else
    Nova Conta
    @endif
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
        @if (isset($empreendimento))
        <p>
            Cadastrado por <strong>{{$cadastrado_por_user->name}}</strong> em
            {{ \Carbon\Carbon::parse($empreendimento->data_cadastro)->format('d/m/Y') }}
        </p>
        @if (isset($alterado_por_user))
        <p>
            Última alteração feita por <strong>{{$alterado_por_user->name}}</strong> em
            {{ \Carbon\Carbon::parse($empreendimento->data_alteracao)->format('d/m/Y') }}
        </p>
        @endif
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#exampleModal">
            Excluir empreendimento
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Excluir {{$empreendimento->nome}}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Deseja mesmo excluir esse empreendimento? </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não</button>
                        <a href="../excluir/{{$empreendimento->id}}" class="btn btn-danger">Sim, excluir</a>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        @endif

        <form class="row g-3"
            action="{{ isset($conta) ? '/conta_corrente/alterar/' . $conta->id . '/' . Auth::user()->id : '/conta_corrente/cadastrar/' . $titular_id . '/' . Auth::user()->id }}"
            method="post" autocomplete="off">
            @csrf
            <div class="col-md-4">
                <label for="inputApelido" id="apelido" class="form-label">Apelido*</label>
                <input type="text" name="apelido" value="{{isset($conta) ? $conta->apelido : old('apelido')}}"
                    class="form-control @error('apelido') is-invalid @enderror" id="inputApelido">
            </div>
            <div class="col-md-4">
                <label for="inputBanco" id="banco" class="form-label">Banco*</label>
                <input type="text" name="banco"
                    value="{{isset($conta) ? $conta->banco : old('banco')}}"
                    class="form-control @error('banco') is-invalid @enderror" id="inputBanco">
            </div>
            <div class="col-md-3">
                <label for="inputAgencia" class="form-label">Agência*</label>
                <input type="text" name="agencia" value="{{isset($conta) ? $conta->agencia: old('agencia') }}"
                    class="form-control @error('agencia') is-invalid @enderror" id="inputAgencia">
            </div>
            <div class="col-md-1">
                <label for="inputDigitoAgencia" class="form-label">Digito agência*</label>
                <input type="text" name="digitoAgencia" value="{{isset($conta) ? $conta->digitoAgencia : old('digitoAgencia') }}"
                    class="form-control @error('digitoAgencia') is-invalid @enderror" id="inputDigitoAgencia">
            </div>
            <div class="col-md-3">
                <label for="inputCarteira" id="carteira" class="form-label">Carteira*</label>
                <input type="text" name="carteira" value="{{isset($conta) ? $conta->carteira : old('carteira')}}"
                    class="form-control @error('carteira') is-invalid @enderror" id="inputCarteira">
            </div>
            <div class="col-md-2">
                <label for="inputBaixa" id="baixa" class="form-label">Dias para baixa*</label>
                <input type="text" name="baixa" value="{{isset($conta) ? $conta->baixa : old('baixa')}}"
                    class="form-control @error('baixa') is-invalid @enderror" id="inputBaixa">
            </div>
            <div class="col-md-3">
                <label for="inputNumeroConta" id="numeroConta" class="form-label">Número da Conta*</label>
                <input type="text" name="numeroConta" value="{{isset($conta) ? $conta->numeroConta : old('numeroConta')}}"
                    class="form-control @error('numeroConta') is-invalid @enderror" id="inputBanco">
            </div>
            <div class="col-md-1">
                <label for="inputDigitoConta" class="form-label">Digito conta*</label>
                <input type="text" name="digitoConta" value="{{isset($conta) ? $conta->digitoConta : old('digitoConta')}}"
                    class="form-control @error('digitoConta') is-invalid @enderror" id="inputDigitoConta">
            </div>
            
            <div class="col-12">
                <button type="submit" class="btn-submit">
                    @if (isset($empreendimento))
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