@extends('layouts/app')

@section('conteudo')

<h2>Descrição de Débitos</h2>

<div class="card">
    <h5 class="card-header">Cadastrar descrição de débitos</h5>
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
        <form class="row g-3" action="{{ '/descricao_debito/cadastrar/' . Auth::user()->id }}" method="post" autocomplete="off">
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
    <h5 class="card-header">Descrições cadastradas</h5>
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
                @if(isset($descricao_debito))
                @foreach ($descricao_debito as $descricao)
                <tr>
                    <th scope="row">{{$descricao->id}}</th>
                    <td>{{$descricao->descricao}}</td>
                    <td>
                        <a class="btn-acao-listagem-danger" href="descricao_debito/excluir/{{$descricao->id}}">Excluir</a>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        @if(isset($descricao_debito))
        <div class="card-footer">
            <p>Exibindo {{$descricao_debito->count()}} de {{ $total_descricao_debito }} registros</p>
        </div>
        @endif

    </div>
</div>

@endsection