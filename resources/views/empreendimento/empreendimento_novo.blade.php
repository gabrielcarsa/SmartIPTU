@extends('layouts/app')

@section('conteudo')

<h2>
    @if (isset($empreendimento))
    Alterar Empreendimento
    @else
    Novo Empreendimento
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
            {{ \Carbon\Carbon::parse($empreendimentos->data_alteracao)->format('d/m/Y') }}
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
            action="{{ isset($empreendimento) ? '/empreendimento/alterar/' . $empreendimento->id . '/' . Auth::user()->id : '/empreendimento/cadastrar/' . Auth::user()->id }}"
            method="post" autocomplete="off">
            @csrf
            <div class="col-md-4" id="campoNome">
                <label for="inputNome" id="nome" class="form-label">Nome*</label>
                <input type="text" name="nome" value="{{isset($empreendimento) ? $empreendimento->nome : old('empreendimento')}}"
                    class="form-control @error('nome') is-invalid @enderror" id="inputNome">
            </div>
            <div class="col-md-4" id="campoMatricula">
                <label for="inputMatricula" id="matricula" class="form-label">Matrícula*</label>
                <input type="text" name="matricula"
                    value="{{isset($empreendimento) ? $empreendimento->matricula : old('matricula')}}"
                    class="form-control @error('matricula') is-invalid @enderror" id="inputMatricula">
            </div>
            <div class="col-md-4">
                <label for="inputCidade" class="form-label">Cidade*</label>
                <input type="text" name="cidade"
                    value="{{isset($empreendimento) ? $empreendimento->cidade: old('cidade') }}"
                    class="form-control @error('cidade') is-invalid @enderror" id="inputCidade">
            </div>
            <div class="col-md-2">
                <label for="inputEstado" class="form-label">Estado*</label>
                <input type="text" name="estado"
                    value="{{isset($empreendimento) ? $empreendimento->estado : old('estado') }}"
                    class="form-control @error('estado') is-invalid @enderror" id="inputEstado">
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