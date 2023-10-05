@extends('layouts/app')

@section('conteudo')

<h2>Categoria de Contas a Pagar</h2>

<div class="card">
    <h5 class="card-header">Cadastrar categoria de contas a pagar</h5>
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
        <form class="row g-3" action="{{ '/categoria_pagar/cadastrar/' . Auth::user()->id }}" method="post" autocomplete="off">
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
    <h5 class="card-header">Categoria de contas a pagar cadastradas</h5>
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
                @if(isset($categoria_pagar))
                @foreach ($categoria_pagar as $categoria)
                <tr>
                    <th scope="row">{{$categoria->id}}</th>
                    <td>{{$categoria->descricao}}</td>
                    <td>
                        <a class="btn-acao-listagem-danger" href="categoria_pagar/excluir/{{$categoria->id}}">Excluir</a>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        @if(isset($categoria_pagar))
        <div class="card-footer">
            <p>Exibindo {{$categoria_pagar->count()}} de {{ $total_categoria_pagar }} registros</p>
        </div>
        @endif

    </div>
</div>

@endsection