@extends('layouts/app')

@section('conteudo')

<h2>
    @if (isset($empreendimento))
    Alterar Prescrição
    @else
    Nova Prescrição
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
        @if (isset($prescricao))
        <p>
            Cadastrado por <strong>{{$cadastrado_por_user->name}}</strong> em
            {{ \Carbon\Carbon::parse($prescricao->data_cadastro)->format('d/m/Y') }}
        </p>
        @if (isset($alterado_por_user))
        <p>
            Última alteração feita por <strong>{{$alterado_por_user->name}}</strong> em
            {{ \Carbon\Carbon::parse($prescricao->data_alteracao)->format('d/m/Y') }}
        </p>
        @endif
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#exampleModal">
            Excluir prescrição
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
                        <p>Deseja mesmo excluir essa prescrição? </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não</button>
                        <a href="../excluir/{{$prescricao->id}}" class="btn btn-danger">Sim, excluir</a>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        @endif

        <form class="row g-3"
            action="{{ isset($prescricao) ? '/prescricao/alterar/' . $prescricao->id . '/' . Auth::user()->id : '/empreendimento/cadastrar/' . Auth::user()->id }}"
            method="post" autocomplete="off">
            @csrf
            <div class="col-md-3">
                <label for="inputProcesso" id="processo" class="form-label">Processo*</label>
                <input type="text" name="processo" value="{{isset($prescricao) ? $prescricao->processo : old('processo')}}"
                    class="form-control @error('processo') is-invalid @enderror" id="inputProcesso">
            </div>
            <div class="col-md-3" id="campoMatricula">
                <label for="inputMatricula" id="matricula" class="form-label">Entrada Pedido*</label>
                <input type="date" name="matricula"
                    value="{{isset($prescricao) ? $prescricao->matricula : old('matricula')}}"
                    class="form-control @error('matricula') is-invalid @enderror" id="inputMatricula">
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