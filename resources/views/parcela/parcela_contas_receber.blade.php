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
                <select id="inputTitularReceber" name="titular_conta_id" class="form-select form-control">
                    <option value="0" select>-- Todos --</option>
                    @foreach ($titular_conta as $t)
                    <option value="{{$t->id_titular_conta}}">
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

            <div class="col-md-3">
                <label for="" class="form-label">A Receber refente</label><br>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="refenteLotes" name="refenteLotes"
                      >
                    <label class="form-check-label" for="refenteLotes">Lotes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="refenteOutros" name="refenteOutros"
                        >
                    <label class="form-check-label" for="refenteOutros">Outros</label>
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn-submit">Consultar</button>
                <a href="{{ route('nova_receita') }}" class="btn-add"><span class="material-symbols-outlined">
                        add
                    </span>Nova receita (Outros)</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <h5 class="card-header">Lista de cadastros</h5>
    @if(isset($data['resultados']))
    <div class="card-footer">
        <a class="btn btn-add"
            href="../cliente/relatorio_pdf?nome={{request('nome')}}&cpf_cnpj={{request('cpf_cnpj')}}">PDF</a>
        <a class="btn btn-add" href="">Excel</a>
    </div>
    @endif
    <div class="card-body">
        @if(isset($data['resultados']))
        <table class="table table-bordered table-striped text-center">
            @if($data['isReferenteLotes'])
            <thead>
                <tr>
                    <th scope="col"><input type="checkbox" id="selecionar_todos" name="selecionar_todos" /></th>
                    <th scope="col">ID</th>
                    <th scope="col">Titular a receber</th>
                    <th scope="col">Nº parcela</th>
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

                @foreach ($data['resultados'] as $resultado)
                <tr>
                    <td>
                        <button class="btn accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse{{$resultado->id}}" aria-expanded="false"
                            aria-controls="collapse{{$resultado->id}}">
                            <span class="material-symbols-outlined">
                                expand
                            </span>
                        </button>
                    </td>
                    <td scope="row">{{$resultado->id}}</td>
                    <td scope="row">{{$resultado->nome_cliente_ou_razao_social}}</td>
                    <td scope="row">{{$resultado->numero_parcela}} de {{$resultado->quantidade_parcela}}</td>
                    <td>{{$resultado->tipo_debito_descricao}}</td>
                    <td>{{$resultado->descricao}}</td>
                    <td>{{$resultado->empreendimento}}</td>
                    <td>{{$resultado->quadra}} / {{$resultado->lote}}</td>
                    <td>{{$resultado->inscricao}}</td>
                    <td>
                        @if($resultado->tipo_cadastro == 0)
                        {{$resultado->nome}}
                        @else
                        {{$resultado->razao_social}}
                        @endif
                    </td>
                    <td>{{\Carbon\Carbon::parse($resultado->data_vencimento)->format('d/m/Y') }}</td>
                    <td>{{$resultado->valor_parcela}}</td>
                    <td>
                        @if($resultado->situacao_parcela == 0)
                        Em aberto
                        @else
                        Pago
                        @endif
                    </td>
                </tr>
                <tr class="accordion-row">
                    <td colspan="12">
                        <!-- Colspan igual ao número de colunas na tabela -->
                        <div class="accordion" id="accordion{{$resultado->id}}">
                            <div class="accordion-item">
                                <div id="collapse{{$resultado->id}}" class="accordion-collapse collapse"
                                    aria-labelledby="heading{{$resultado->id}}"
                                    data-bs-parent="#accordion{{$resultado->id}}">
                                    <div class="accordion-body">
                                        <p>Recebimento em:
                                            {{\Carbon\Carbon::parse($resultado->data_recebimento)->format('d/m/Y') }}
                                        </p>
                                        <p>Valor recebido: R$ {{$resultado->parcela_valor_pago}}</p>
                                        <p>Cadastrado por: {{$resultado->cadastrado_por}}</p>
                                        <p>Alterado por: {{$resultado->alterado_por}}</p>
                                        <p>Baixado por: {{$resultado->baixado_por}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>

            @else
            <thead>
                <tr>
                    <th scope="col"><input type="checkbox" id="selecionar_todos" name="selecionar_todos" /></th>
                    <th scope="col">ID</th>
                    <th scope="col">Titular a receber</th>
                    <th scope="col">Nº parcela</th>
                    <th scope="col">Descrição</th>
                    <th scope="col">Vencimento</th>
                    <th scope="col">Valor</th>
                    <th scope="col">Situação</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['resultados'] as $resultado)
                <tr>
                    <td>
                        <button class="btn accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse{{$resultado->id}}" aria-expanded="false"
                            aria-controls="collapse{{$resultado->id}}">
                            <span class="material-symbols-outlined">
                                expand
                            </span>
                        </button>
                    </td>
                    <td scope="row">{{$resultado->id}}</td>
                    <td scope="row">{{$resultado->nome_cliente_ou_razao_social}}</td>
                    <td scope="row">{{$resultado->numero_parcela}} de {{$resultado->quantidade_parcela}}</td>
                    <td>{{$resultado->descricao}}</td>
                    <td>{{\Carbon\Carbon::parse($resultado->data_vencimento)->format('d/m/Y') }}</td>
                    <td>{{$resultado->valor_parcela}}</td>
                    <td>
                        @if($resultado->situacao_parcela == 0)
                        Em aberto
                        @else
                        Pago
                        @endif
                    </td>
                </tr>
                <tr class="accordion-row">
                    <td colspan="12">
                        <!-- Colspan igual ao número de colunas na tabela -->
                        <div class="accordion" id="accordion{{$resultado->id}}">
                            <div class="accordion-item">
                                <div id="collapse{{$resultado->id}}" class="accordion-collapse collapse"
                                    aria-labelledby="heading{{$resultado->id}}"
                                    data-bs-parent="#accordion{{$resultado->id}}">
                                    <div class="accordion-body">
                                        <p>Recebimento em:
                                            {{\Carbon\Carbon::parse($resultado->data_recebimento)->format('d/m/Y') }}
                                        </p>
                                        <p>Valor recebido: R$ {{$resultado->parcela_valor_pago}}</p>
                                        <p>Cadastrado por: {{$resultado->cadastrado_por}}</p>
                                        <p>Alterado por: {{$resultado->alterado_por}}</p>
                                        <p>Baixado por: {{$resultado->baixado_por}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            @endif

        </table>
        @endif

        @if(isset($data['resultados']))
        <div class="card-footer">
            <p>Exibindo {{$data['resultados']->count()}} registros</p>
        </div>
        @endif

    </div>
</div>

@endsection