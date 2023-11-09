@extends('layouts/app')

@section('conteudo')

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<h2>Empreendimento</h2>
<p>
    - a fazer: validações; - Movimentacao (editar e colocar categoria e valid); - No contas ao pagar/receber mudar na
    movimentação; - validacoes; - dashboard; - conta bancaria do titular; - estornar parcelas; - exportar relatorios
</p>

<a class="btn btn-primary btn-add" href="empreendimento/novo" style="margin-bottom: 20px">
    <span class="material-symbols-outlined">
        add
    </span>Novo</a>
    
<div class="card">
    <h5 class="card-header">Empreendimentos cadastrados</h5>
    @if(isset($empreendimentos))
    <div class="card-footer">
        <a class="btn btn-add" href="">PDF</a>
        <a class="btn btn-add" href="">Excel</a>
    </div>
    @endif
    <div class="card-body">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Empreendimento</th>
                    <th scope="col">Matrícula</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($empreendimentos))
                @foreach ($empreendimentos as $empreendimento)
                <tr>
                    <th scope="row">{{$empreendimento->id}}</th>
                    <td>{{$empreendimento->nome}}</td>
                    <td>{{$empreendimento->matricula}}</td>
                    <td>{{$empreendimento->cidade}}, {{$empreendimento->estado}}</td>
                    <td>
                        <a class="btn-acao-listagem" href="empreendimento/gestao/{{$empreendimento->id}}">Gestão</a>
                        <a class="btn-acao-listagem-secundary"
                            href="empreendimento/editar/{{$empreendimento->id}}">Ver/Editar</a>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        @if(isset($empreendimentos))
        <div class="card-footer">
            <p>Exibindo {{$empreendimentos->count()}} de {{ $total_empreendimentos }} registros</p>
        </div>
        @endif

    </div>
</div>

@endsection