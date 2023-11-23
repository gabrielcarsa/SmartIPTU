@extends('layouts/app')

@section('conteudo')

<h2>Conta Corrente</h2>


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

<a class="btn btn-primary btn-add" id="alterar_vencimento" href="novo/{{$titular_id}}">
    Cadastrar
</a>

<div class="card">
    <h5 class="card-header">Titulares de contas cadastrados</h5>
    <div class="card-body">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Apelido</th>
                    <th scope="col">Agência / Digito</th>
                    <th scope="col">Banco</th>
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($contas_corrente))
                @foreach ($contas_corrente as $conta)
                <tr>
                    <th scope="row">{{$conta->id}}</th>
                    <td>{{$conta->apelido}} </td>
                    <td>{{$conta->agencia}} / {{$conta->digito_agencia}}</td>
                    <td>{{$conta->banco}} </td>
                    <td>
                        <a class="btn-acao-listagem-danger" href="titular_conta/excluir/{{$conta->id}}">Excluir</a>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>

    </div>
</div>

@endsection