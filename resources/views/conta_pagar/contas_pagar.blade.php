@extends('layouts/app')

@section('conteudo')

<h2>Contas a pagar</h2>

@if(isset($data['resultados']))
<a class="btn btn-primary btn-add" id="reajustar_parcelas" href="{{route('parcela_reajustar')}}"
    style="margin-bottom: 20px">
    <span class="material-symbols-outlined">
        attach_money
    </span>
    Reajustar Valores
</a>

<a class="btn btn-primary btn-add" id="alterar_vencimento" href="{{route('alterar_vencimento')}}"
    style="margin-bottom: 20px">
    <span class="material-symbols-outlined">
        edit_calendar
    </span>
    Alterar data de vencimento
</a>

<a class="btn btn-primary btn-add" id="baixar_parcela" href="{{route('baixar_parcela')}}" style="margin-bottom: 20px">
    <span class="material-symbols-outlined">
        payments
    </span>
    Baixar parcelas
</a>

<a class="btn btn-primary btn-add" id="estornar_pagamento" href="{{route('estornar_pagamento')}}"
    style="margin-bottom: 20px">
    <span class="material-symbols-outlined">
        restart_alt
    </span>
    Estornar pagamento
</a>

<a class="btn btn-primary btn-add" id="estornar_parcela" href="{{route('estornar_parcela_pagar')}}"
    style="margin-bottom: 20px">
    <span class="material-symbols-outlined">
        delete
    </span>
    Estornar parcela
</a>
@endif

<div class="card">
    <h5 class="card-header">Filtros para buscar</h5>
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
        <form class="row g-3" action="/contas_pagar/listar" method="get" autocomplete="off">
            @csrf
            <div class="col-md-4">
                <label for="inputCliente" class="form-label">Fornecedor</label>
                <input type="text" name="cliente" value="{{request('cliente')}}" class="form-control" id="inputCliente">
            </div>
            <div class="col-md-4">
                <label for="inputEmpreendimento" class="form-label">Empreendimento</label>
                <input type="text" name="empreendimento" value="{{request('empreendimento')}}" class="form-control"
                    id="inputEmpreendimento">
            </div>
            <div class="col-md-4">
                <label for="inputTitularReceber" class="form-label">Titular da conta a pagar</label>
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
                <label for="inputTipoDebito" class="form-label">Tipo Débito</label>
                <select id="inputTipoDebito" name="tipo_debito" class="form-select form-control">
                    <option value="0" select>-- Todos --</option>
                    @foreach ($tipo_debito as $tipo)
                    <option value="{{$tipo->id}}">
                        {{$tipo->descricao}}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label for="inputCategoria" class="form-label">Categoria</label>
                <select id="inputCategoria" name="categoria" class="form-select form-control">
                    <option value="0" select>-- Todos --</option>
                    @foreach ($categoria as $cat)
                    <option value="{{$cat->id}}">
                        {{$cat->descricao}}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label for="inputQuadra" class="form-label">Quadra</label>
                <input type="text" name="quadra" value="{{request('quadra')}}" class="form-control" id="inputQuadra">
            </div>
            <div class="col-md-2">
                <label for="inputLote" class="form-label">Lote</label>
                <input type="text" name="lote" value="{{request('lote')}}" class="form-control" id="inputLote">
            </div>
            <div class="col-md-4">
                <label for="inputId" class="form-label">ID parcela</label>
                <input type="text" name="idParcela" value="{{request('idParcela')}}" class="form-control" id="inputId">
            </div>

            <div class="col-md-2">
                <label for="inputPeriodoDe" class="form-label">Período de</label>
                <input type="date" name="periodoDe" value="{{request('periodoDe')}}" class="form-control"
                    id="inputPeriodoDe">
            </div>
            <div class="col-md-2">
                <label for="inputPeriodoAte" class="form-label">Período até</label>
                <input type="date" name="periodoAte" value="{{request('periodoAte')}}" class="form-control"
                    id="inputPeriodoAte">
            </div>
            <div class="col-md-4">
                <label for="" class="form-label">Tipo Período</label><br>

                <div class="form-check form-check-inline">
                    <input class="form-check-input @error('periodoLancamento') is-invalid @enderror" type="checkbox"
                        id="periodoLancamento" name="periodoLancamento"
                        {{ request('periodoLancamento') ? 'checked' : '' }}>
                    <label class="form-check-label" for="periodoLancamento">Lançamento</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input @error('periodoVencimento') is-invalid @enderror" type="checkbox"
                        id="periodoVencimento" name="periodoVencimento"
                        {{ request('periodoVencimento') ? 'checked' : '' }}>
                    <label class="form-check-label" for="periodoVencimento">Vencimento</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input @error('periodoRecebimento') is-invalid @enderror" type="checkbox"
                        id="periodoRecebimento" name="periodoRecebimento"
                        {{ request('periodoRecebimento') ? 'checked' : '' }}>
                    <label class="form-check-label" for="periodoRecebimento">Recebimento</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input @error('periodoBaixa') is-invalid @enderror" type="checkbox"
                        id="periodoBaixa" name="periodoBaixa" {{ request('periodoBaixa') ? 'checked' : '' }}>
                    <label class="form-check-label" for="periodoBaixa">Baixa</label>
                </div>
            </div>
            <div class="col-md-4">
                <label for="" class="form-label">Situação</label><br>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="situacaoVencer" name="situacaoVencer"
                        onclick="handleCheckboxClickSituacao('situacaoVencer')">
                    <label class="form-check-label" for="inlineCheckbox1">A vencer</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="situacaoPago" name="situacaoPago"
                        onclick="handleCheckboxClickSituacao('situacaoPago')">
                    <label class="form-check-label" for="inlineCheckbox2">Pago</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" checked id="situacaoTodos" name="situacaoTodos"
                        onclick="handleCheckboxClickSituacao('situacaoTodos')">
                    <label class="form-check-label" for="inlineCheckbox1">Todos</label>
                </div>
            </div>

            <div class="col-md-3">
                <label for="" class="form-label">A Pagar referente</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input @error('referenteLotes') is-invalid @enderror" type="checkbox"
                        id="referenteLotes" name="referenteLotes" {{ request('referenteLotes') ? 'checked' : '' }}
                        onclick="handleCheckboxClick('referenteLotes')">
                    <label class="form-check-label" for="referenteLotes">Lotes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input @error('referenteOutros') is-invalid @enderror" type="checkbox"
                        id="referenteOutros" name="referenteOutros" {{ request('referenteOutros') ? 'checked' : '' }}
                        onclick="handleCheckboxClick('referenteOutros')">
                    <label class="form-check-label" for="referenteOutros">Outros</label>
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn-submit">Consultar</button>
                <a href="{{ route('nova_despesa') }}" class="btn btn-add"><span class="material-symbols-outlined">
                        add
                    </span>
                    Nova conta a Pagar (Outros)
                </a>
            </div>
        </form>
    </div>
</div>

@if(isset($data['resultados']))
<div class="card">
    <h5 class="card-header">Parcelas</h5>
    <div class="card-footer">
        <a class="btn btn-add"
            href="../cliente/relatorio_pdf?nome={{request('nome')}}&cpf_cnpj={{request('cpf_cnpj')}}">PDF</a>
        <a class="btn btn-add" href="">Excel</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered text-center">
            @if($data['isReferenteLotes'])
            <thead>
                <tr>
                    <th scope="col"><input type="checkbox" id="selecionar_todos" name="selecionar_todos" /></th>
                    <th scope="col">ID</th>
                    <!--<th scope="col">Titular da conta</th>-->
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
            <tbody class="tbody-contas">

                @foreach ($data['resultados'] as $resultado)
                <tr>
                    <td>
                        <input data-bs-toggle="collapse" data-bs-target="#collapse{{$resultado->id}}"
                            aria-expanded="false" aria-controls="collapse{{$resultado->id}}" type="checkbox" id=""
                            name="checkboxes[]" value="{{ $resultado->id }}" />
                    </td>
                    <td scope="row">{{$resultado->id}}</td>
                    <!--<td scope="row">{{$resultado->nome_cliente_ou_razao_social}}</td>-->
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
                    <td>R$ {{number_format($resultado->valor_parcela, 2, ',', '.')}}</td>
                    <td>
                        @if($resultado->situacao_parcela == 0)
                        Em aberto
                        @elseif($resultado->situacao_parcela == 1)
                        Pago
                        @else
                        Pago parcialmente
                        @endif
                    </td>
                </tr>
                <tr class="collapse" id="collapse{{$resultado->id}}">
                    <td colspan="13">
                        <!-- Colspan igual ao número de colunas na tabela -->
                        <div class="row-collapse-table text-left">
                            <p>Pago em:
                                {{$resultado->data_pagamento == null ? '' : \Carbon\Carbon::parse($resultado->data_pagamento)->format('d/m/Y') }}
                            </p>
                            <p>Valor pago: R$ {{number_format($resultado->parcela_valor_pago, 2, ',', '.')}}</p>
                            <p>Telefones fornecedor: {{$resultado->tel1}}, {{$resultado->tel2}}</p>
                            <p>Cadastrado por: {{$resultado->cadastrado_por}}</p>
                            <p>Alterado por: {{$resultado->alterado_por}}</p>
                            <p>Baixado por: {{$resultado->baixado_por}}</p>
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
                    <th scope="col">Titular da conta</th>
                    <th scope="col">Fornecedor</th>
                    <th scope="col">Nº parcela</th>
                    <th scope="col">Categoria</th>
                    <th scope="col">Descrição</th>
                    <th scope="col">Vencimento</th>
                    <th scope="col">Valor</th>
                    <th scope="col">Situação</th>
                </tr>
            </thead>
            <tbody class="tbody-contas">
                @foreach ($data['resultados'] as $resultado)
                <tr>
                    <td>
                        <input data-bs-toggle="collapse" data-bs-target="#collapse{{$resultado->id}}"
                            aria-expanded="false" aria-controls="collapse{{$resultado->id}}" type="checkbox" id=""
                            name="checkboxes[]" value="{{ $resultado->id }}" />
                    </td>
                    <td scope="row">{{$resultado->id}}</td>
                    <td scope="row">{{$resultado->nome_cliente_ou_razao_social}}</td>
                    @if($resultado->tipo_cadastro == 0)
                    <td scope="row">{{$resultado->nome}}</td>
                    @else
                    <td scope="row">{{$resultado->razao_social}}</td>
                    @endif
                    <td scope="row">{{$resultado->numero_parcela}} de {{$resultado->quantidade_parcela}}</td>
                    <td>{{$resultado->categoria}}</td>
                    <td>{{$resultado->descricao}}</td>
                    <td>{{\Carbon\Carbon::parse($resultado->data_vencimento)->format('d/m/Y') }}</td>
                    <td>R$ {{number_format($resultado->valor_parcela, 2, ',', '.')}}</td>
                    <td>
                        @if($resultado->situacao_parcela == 0)
                        Em aberto
                        @elseif($resultado->situacao_parcela == 1)
                        Pago
                        @else
                        Pago parcialmente
                        @endif
                    </td>
                </tr>
                <tr class="collapse" id="collapse{{$resultado->id}}">
                    <td colspan="13">
                        <!-- Colspan igual ao número de colunas na tabela -->
                        <div class="row-collapse-table text-left">
                            <p>Pago em:
                                {{$resultado->data_pagamento == null ? '' : \Carbon\Carbon::parse($resultado->data_pagamento)->format('d/m/Y') }}
                            </p>
                            <p>Valor pago: R$ {{number_format($resultado->parcela_valor_pago, 2, ',', '.')}}</p>
                            <p>Telefones fornecedor: {{$resultado->tel1}}, {{$resultado->tel2}}</p>
                            <p>Cadastrado por: {{$resultado->cadastrado_por}}</p>
                            <p>Alterado por: {{$resultado->alterado_por}}</p>
                            <p>Baixado por: {{$resultado->baixado_por}}</p>
                        </div>
                    </td>
                </tr>

                @endforeach
            </tbody>
            @endif

        </table>
        @if(isset($data['resultados']))
        <div class="card-footer">
            <p>Exibindo {{$data['resultados']->count()}} registros</p>
            <p>Valor total das parcelas: R$ {{number_format($data['totalValorParcelas'], 2, ',', '.')}}</p>
            <p>Valor total pago: R$ {{number_format($data['totalValorPago'], 2, ',', '.')}}</p>
        </div>
        @endif

    </div>
</div>
@endif

@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
function handleCheckboxClick(clickedCheckboxId) {
    // Desmarcar o outro checkbox
    if (clickedCheckboxId === 'referenteLotes') {
        document.getElementById('referenteOutros').checked = false;
    } else if (clickedCheckboxId === 'referenteOutros') {
        document.getElementById('referenteLotes').checked = false;
    }
}

function handleCheckboxClickSituacao(clickedCheckboxId) {
    // Desmarcar o outro checkbox
    if (clickedCheckboxId === 'situacaoTodos') {
        document.getElementById('situacaoPago').checked = false;
        document.getElementById('situacaoVencer').checked = false;
    } else if (clickedCheckboxId === 'situacaoPago') {
        document.getElementById('situacaoVencer').checked = false;
        document.getElementById('situacaoTodos').checked = false;
    } else if (clickedCheckboxId === 'situacaoVencer') {
        document.getElementById('situacaoPago').checked = false;
        document.getElementById('situacaoTodos').checked = false;
    }
}

$(document).ready(function() {

    if (referenteLotes.checked) {
        // Captura o clique no Parcelas Reajustar
        $("#reajustar_parcelas").click(function(event) {
            event.preventDefault();

            // Obtenha os valores dos checkboxes selecionados
            var checkboxesSelecionados = [];

            $("input[name='checkboxes[]']:checked").each(function() {
                checkboxesSelecionados.push($(this).val());
            });

            // Crie a URL com os valores dos checkboxes como parâmetros de consulta
            var url = "{{ route('parcela_reajustar') }}?checkboxes=" + checkboxesSelecionados.join(
                ',') + "&origem=contas_pagar";

            // Redirecione para a URL com os parâmetros
            window.location.href = url;
        });

        // Captura o clique no Alterar Data Vencimento
        $("#alterar_vencimento").click(function(event) {
            event.preventDefault();

            // Obtenha os valores dos checkboxes selecionados
            var checkboxesSelecionados = [];

            $("input[name='checkboxes[]']:checked").each(function() {
                checkboxesSelecionados.push($(this).val());
            });

            // Crie a URL com os valores dos checkboxes como parâmetros de consulta
            var url = "{{ route('alterar_vencimento') }}?checkboxes=" + checkboxesSelecionados.join(
                ',') + "&origem=contas_pagar";

            // Redirecione para a URL com os parâmetros
            window.location.href = url;
        });

        $("#baixar_parcela").click(function(event) {
            event.preventDefault();

            // Obtenha os valores dos checkboxes selecionados
            var checkboxesSelecionados = [];

            $("input[name='checkboxes[]']:checked").each(function() {
                checkboxesSelecionados.push($(this).val());
            });

            // Crie a URL com os valores dos checkboxes como parâmetros de consulta
            var url = "{{ route('baixar_parcela') }}?checkboxes=" + checkboxesSelecionados.join(',') +
                "&origem=contas_pagar";

            // Redirecione para a URL com os parâmetros
            window.location.href = url;
        });

        $("#estornar_pagamento").click(function(event) {
            event.preventDefault();

            // Obtenha os valores dos checkboxes selecionados
            var checkboxesSelecionados = [];

            $("input[name='checkboxes[]']:checked").each(function() {
                checkboxesSelecionados.push($(this).val());
            });

            // Crie a URL com os valores dos checkboxes como parâmetros de consulta
            var url = "{{ route('estornar_pagamento') }}?checkboxes=" + checkboxesSelecionados.join(
                    ',') +
                "&origem=contas_pagar";

            // Redirecione para a URL com os parâmetros
            window.location.href = url;
        });

        $("#estornar_parcela").click(function(event) {
            event.preventDefault();

            // Obtenha os valores dos checkboxes selecionados
            var checkboxesSelecionados = [];

            $("input[name='checkboxes[]']:checked").each(function() {
                checkboxesSelecionados.push($(this).val());
            });

            // Crie a URL com os valores dos checkboxes como parâmetros de consulta
            var url = "{{ route('estornar_parcela_pagar') }}?checkboxes=" + checkboxesSelecionados.join(
                    ',') +
                "&origem=contas_pagar";

            // Redirecione para a URL com os parâmetros
            window.location.href = url;
        });

    } else if (referenteOutros.checked) {

        // Captura o clique no Parcelas Reajustar
        $("#reajustar_parcelas").click(function(event) {
            event.preventDefault();

            // Obtenha os valores dos checkboxes selecionados
            var checkboxesSelecionados = [];

            $("input[name='checkboxes[]']:checked").each(function() {
                checkboxesSelecionados.push($(this).val());
            });

            // Crie a URL com os valores dos checkboxes como parâmetros de consulta
            var url = "{{ route('pagar_reajustar') }}?checkboxes=" + checkboxesSelecionados.join(
                ',') + "&origem=contas_pagar";

            // Redirecione para a URL com os parâmetros
            window.location.href = url;
        });

        // Captura o clique no Alterar Data Vencimento
        $("#alterar_vencimento").click(function(event) {
            event.preventDefault();

            // Obtenha os valores dos checkboxes selecionados
            var checkboxesSelecionados = [];

            $("input[name='checkboxes[]']:checked").each(function() {
                checkboxesSelecionados.push($(this).val());
            });

            // Crie a URL com os valores dos checkboxes como parâmetros de consulta
            var url = "{{ route('pagar_alterar_vencimento') }}?checkboxes=" + checkboxesSelecionados
                .join(
                    ',') + "&origem=contas_pagar";

            // Redirecione para a URL com os parâmetros
            window.location.href = url;
        });

        $("#baixar_parcela").click(function(event) {
            event.preventDefault();

            // Obtenha os valores dos checkboxes selecionados
            var checkboxesSelecionados = [];

            $("input[name='checkboxes[]']:checked").each(function() {
                checkboxesSelecionados.push($(this).val());
            });

            // Crie a URL com os valores dos checkboxes como parâmetros de consulta
            var url = "{{ route('pagar_baixar_parcela') }}?checkboxes=" + checkboxesSelecionados.join(
                    ',') +
                "&origem=contas_pagar";

            // Redirecione para a URL com os parâmetros
            window.location.href = url;
        });

        $("#estornar_pagamento").click(function(event) {
            event.preventDefault();

            // Obtenha os valores dos checkboxes selecionados
            var checkboxesSelecionados = [];

            $("input[name='checkboxes[]']:checked").each(function() {
                checkboxesSelecionados.push($(this).val());
            });

            // Crie a URL com os valores dos checkboxes como parâmetros de consulta
            var url = "{{ route('estornar_pagamento') }}?checkboxes=" + checkboxesSelecionados.join(
                    ',') +
                "&origem=contas_pagar";

            // Redirecione para a URL com os parâmetros
            window.location.href = url;
        });

        $("#estornar_parcela").click(function(event) {
            event.preventDefault();

            // Obtenha os valores dos checkboxes selecionados
            var checkboxesSelecionados = [];

            $("input[name='checkboxes[]']:checked").each(function() {
                checkboxesSelecionados.push($(this).val());
            });

            // Crie a URL com os valores dos checkboxes como parâmetros de consulta
            var url = "{{ route('estornar_parcela_pagar') }}?checkboxes=" + checkboxesSelecionados.join(
                    ',') +
                "&origem=contas_pagar";

            // Redirecione para a URL com os parâmetros
            window.location.href = url;
        });
    }

    // Selecionar todos checkboxes
    $("#selecionar_todos").click(function() {
        // Obtém o estado atual do "Selecionar Todos" dentro da tabela atual
        var selecionarTodos = $(this).prop('checked');

        // Encontra os checkboxes individuais dentro da tabela atual e marca ou desmarca com base no estado do "Selecionar Todos"
        $(this).closest('table').find("input[name='checkboxes[]']").prop('checked', selecionarTodos);
    });


});
</script>