@extends('layouts/app')

@section('conteudo')

<h2>Central de Contas</h2>

<div class="card">
    <h5 class="card-header">Cadastrar uma conta de titular</h5>
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
        <form class="row g-3" action="{{ '/titular_conta/cadastrar/' . Auth::user()->id }}" method="post"
            autocomplete="off">
            @csrf

            <div class="col-md-4">
                <label for="inputCliente" class="form-label">Selecione o cliente para virar titular de uma conta*</label>
                <select id="inputCliente" name="cliente_id"
                    class="form-select form-control @error('cliente_id') is-invalid @enderror">
                    <option value="0" {{ old('cliente_id') == 0 ? 'selected' : '' }}>-- Selecione --</option>
                    @foreach ($data['clientes'] as $cliente)
                    <option value="{{$cliente->id}}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                        @if(empty($cliente->nome))
                        {{$cliente->razao_social}}
                        @else
                        {{$cliente->nome}}
                        @endif
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12">
                <button type="submit" class="btn-submit">Cadastrar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <h5 class="card-header">Titulares de contas cadastrados</h5>
    <div class="card-body">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nome ou Razão Social</th>
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($data['titulares_contas']))
                @foreach ($data['titulares_contas'] as $titular)
                <tr>
                    <th scope="row">{{$titular->id}}</th>
                    <td>{{$titular->nome_cliente_ou_razao_social}}</td>
                    <td>
                        <a class="btn-acao-listagem-danger" href="titular_conta/excluir/{{$titular->id}}">Excluir</a>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        @if(isset($data['titulares_contas']))
        <div class="card-footer">
            <p>Exibindo {{$data['titulares_contas']->count()}} de {{ $data['total_titular_conta'] }} registros</p>
        </div>
        @endif

    </div>
</div>

@endsection