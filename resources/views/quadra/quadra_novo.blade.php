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
    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

    <div class="card-body">
        <form class="row g-3" action="{{ '/quadra/cadastrar/' . Auth::user()->id . '/' . $empreendimento_id}}"
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
                    <td><a class="btn-acao-listagem-danger" href="../../quadra/excluir/{{$quadra->id}}/{{$empreendimento_id}}">Excluir</a></td>
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