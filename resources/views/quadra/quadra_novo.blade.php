@extends('layouts/app')

@section('conteudo')

<h2>
    Nova Quadra
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
        @if (isset($quadra))
        <p>
            Cadastrado por <strong>{{$cadastrado_por_user->name}}</strong> em
            {{ \Carbon\Carbon::parse($quadra->data_cadastro)->format('d/m/Y') }}
        </p>
        @if (isset($alterado_por_user))
        <p>
            Última alteração feita por <strong>{{$alterado_por_user->name}}</strong> em
            {{ \Carbon\Carbon::parse($quadra->data_alteracao)->format('d/m/Y') }}
        </p>
        @endif
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#exampleModal">
            Excluir quadra
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Excluir {{$quadra->nome}}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Deseja mesmo excluir essa quadra? </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não</button>
                        <a href="../excluir/{{$quadra->id}}" class="btn btn-danger">Sim, excluir</a>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        @endif

        <form class="row g-3"
            action="{{ '/quadra/cadastrar/' . Auth::user()->id . '/' . $empreendimento_id}}"
            method="post" autocomplete="off">
            @csrf
            <div class="col-md-3" id="campoNome">
                <label for="inputNome" id="nome" class="form-label">Nome*</label>
                <input type="text" name="nome" value="{{isset($quadra) ? $quadra->nome : old('quadra')}}"
                    class="form-control @error('nome') is-invalid @enderror" id="inputNome">
            </div>
            <div class="col-12">
                <button type="submit" class="btn-submit">
                    Cadastrar
                </button>
            </div>
        </form>
    </div>
</div>


<div class="card">
    <h5 class="card-header">Lista de Quadras do Empreendimento</h5>

    <div class="card-body">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Quadra</th>
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($quadras))
                @foreach ($quadras as $quadra)
                <tr>
                    <th>{{$quadra->id}}</th>
                    <td scope="row">{{$quadra->nome}}</td>
                    <td><a href="editar/{{$quadra->id}}" class="btn-acao-listagem">Ver/Editar</a></td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        @if(isset($quadras))
        <div class="card-footer">
            <p>Exibindo {{$quadras->count()}} de {{ $total_quadras }} registros</p>
        </div>
        @endif

    </div>
</div>
@endsection