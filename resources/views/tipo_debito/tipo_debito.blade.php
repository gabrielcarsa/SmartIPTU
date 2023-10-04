@extends('layouts/app')

@section('conteudo')

<h2>Tipos de Débito</h2>

<div class="card">
    <h5 class="card-header">Cadastrar tipo de débito</h5>
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
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
        <form class="row g-3" action="{{ '/tipo_debito/cadastrar/' . Auth::user()->id }}" method="get" autocomplete="off">
            @csrf
            <div class="col-md-4">
                <label for="inputDescricao" class="form-label">Descrição</label>
                <input type="text" name="descricao" value="{{request('descricao')}}" class="form-control" id="inputDescricao">
            </div>

            <div class="col-12">
                <button type="submit" class="btn-submit">Cadastrar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <h5 class="card-header">Tipos de débitos cadastrados</h5>
    <div class="card-body">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Descrição</th>
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($lista_tipo_debito))
                @foreach ($lista_tipo_debito as $tipo)
                <tr>
                    <th scope="row">{{$tipo->id}}</th>
                    <td>{{$tipo->descricao}}</td>
                    <td>
                        <a class="btn-acao-listagem-danger" href="excluir/{{$tipo->id}}">Excluir</a>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        @if(isset($lista_tipo_debito))
        <div class="card-footer">
            <p>Exibindo {{$lista_tipo_debito->count()}} de {{ $total_lista_tipo_debito }} registros</p>
        </div>
        @endif

    </div>
</div>

@endsection