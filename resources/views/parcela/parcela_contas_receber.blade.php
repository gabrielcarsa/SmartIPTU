@extends('layouts/app')

@section('conteudo')

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<h2>Contas a receber</h2>
<div class="card">
    <h5 class="card-header">Filtros para buscar</h5>
    <div class="card-body">
        <form class="row g-3" action="/contas_receber/listar" method="get" autocomplete="off">
            @csrf
            <div class="col-md-4">
                <label for="inputEmail4" class="form-label">Cliente</label>
                <input type="text" name="nome" value="{{request('nome')}}" class="form-control" id="inputEmail4">
            </div>
            <div class="col-md-4">
                <label for="inputEmail4" class="form-label">Empreendimento</label>
                <input type="text" name="nome" value="{{request('nome')}}" class="form-control" id="inputEmail4">
            </div>
            <div class="col-md-4">
                <label for="inputTitularReceber" class="form-label">Titular a receber</label>
                <select id="inputTitularReceber" name="titular_debito_id" class="form-select form-control">
                    <option value="0" select>-- Todos --</option>
                    @foreach ($titular_debito as $t)
                    <option value="{{$t->id_titular_debito}}">
                        @if(empty($t->nome))
                        {{$t->razao_social}}
                        @else
                        {{$t->nome}}
                        @endif
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label for="inputEmail4" class="form-label">Quadra</label>
                <input type="text" name="nome" value="{{request('nome')}}" class="form-control" id="inputEmail4">
            </div>
            <div class="col-md-4">
                <label for="inputEmail4" class="form-label">Lote</label>
                <input type="text" name="nome" value="{{request('nome')}}" class="form-control" id="inputEmail4">
            </div>
            <div class="col-md-4">
                <label for="inputPassword4" class="form-label">Origem</label>
                <input type="text" name="cpf_cnpj" value="{{request('cpf_cnpj')}}" class="form-control"
                    id="inputPassword4">
            </div>

            <div class="col-md-2">
                <label for="inputEmail4" class="form-label">Período de</label>
                <input type="text" name="nome" value="{{request('nome')}}" class="form-control" id="inputEmail4">
            </div>
            <div class="col-md-2">
                <label for="inputPassword4" class="form-label">Período até</label>
                <input type="text" name="cpf_cnpj" value="{{request('cpf_cnpj')}}" class="form-control"
                    id="inputPassword4">
            </div>
            <div class="col-md-4">
                <label for="" class="form-label">Tipo Período</label><br>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
                    <label class="form-check-label" for="inlineCheckbox1">Lançamento</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                    <label class="form-check-label" for="inlineCheckbox2">Vencimento</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
                    <label class="form-check-label" for="inlineCheckbox1">Recebimento</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                    <label class="form-check-label" for="inlineCheckbox2">Baixa</label>
                </div>
            </div>
            <div class="col-md-4">
                <label for="" class="form-label">Situação</label><br>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
                    <label class="form-check-label" for="inlineCheckbox1">A vencer</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                    <label class="form-check-label" for="inlineCheckbox2">Pago</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
                    <label class="form-check-label" for="inlineCheckbox1">Todos</label>
                </div>
            </div>


            <div class="col-12">
                <button type="submit" class="btn-submit">Consultar</button>
                <a href="/cliente/novo" class="btn-add"><span class="material-symbols-outlined">
                        add
                    </span>Novo</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <h5 class="card-header">Lista de cadastros</h5>
    @if(isset($clientes))
    <div class="card-footer">
        <a class="btn btn-add"
            href="../cliente/relatorio_pdf?nome={{request('nome')}}&cpf_cnpj={{request('cpf_cnpj')}}">PDF</a>
        <a class="btn btn-add" href="">Excel</a>
    </div>
    @endif
    <div class="card-body">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Tipo Débito</th>
                    <th scope="col">Descrição</th>
                    <th scope="col">Empreendimento</th>
                    <th scope="col">QD / LT</th>
                    <th scope="col">Inscrição</th>
                    <th scope="col">Responsabilidade</th>
                    <th scope="col">Vencimento</th>
                    <th scope="col">Valor</th>
                    <th scope="col">Situação</th>


                </tr>
            </thead>
            <tbody>
                @if(isset($clientes))
                @foreach ($clientes as $cliente)
                <tr>
                    <th scope="row">{{$cliente->id}}</th>
                    @if($cliente->tipo_cadastro == 0)
                    <td>{{$cliente->nome}}</td>
                    <td>{{$cliente->cpf}}</td>
                    @else
                    <td>{{$cliente->razao_social}}</td>
                    <td>{{$cliente->cnpj}}</td>
                    @endif
                    <td>{{$cliente->telefone1}}</td>
                    <td>{{$cliente->email}}</td>
                    <td><a href="editar/{{$cliente->id}}" class="btn-acao-listagem-secundary">Ver/Editar</a></td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        @if(isset($clientes))
        <div class="card-footer">
            <p>Exibindo {{$clientes->count()}} de {{ $total_clientes }} registros</p>
        </div>
        @endif

    </div>
</div>

@endsection