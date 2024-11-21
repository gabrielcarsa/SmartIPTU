@extends('layouts/app')

@section('conteudo')

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

<h2>{{$data['empreendimento']->nome}}</h2>
<a class="btn btn-primary btn-add" href="../../quadra/novo/{{$data['empreendimento']->id}}" style="margin-bottom: 20px">
    <span class="material-symbols-outlined">
        add
    </span>Nova Quadra</a>
<a class="btn btn-primary btn-add" href="../../lote/novo/{{$data['empreendimento']->id}}" style="margin-bottom: 20px">
    <span class="material-symbols-outlined">
        add
    </span>Novo Lote</a>
<a class="btn btn-primary btn-add" data-bs-toggle="modal" data-bs-target="#exampleModal" style="margin-bottom: 20px">
    <span class="material-symbols-outlined">
        expand_less
    </span>
    Subir planilha de Lotes
</a>
<a href="{{ route('cadastrar_scraping_empreendimento', ['id' => $data['empreendimento']->id, 'usuario_id' => Auth::user()->id]) }}"
    class="btn btn-warning text-white fw-semibold" id="importar_pmcg" style="margin-bottom: 20px">
    PMCG importar total
</a>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Selecione a planilha</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Selecione o arquivo no formato .csv</p>
                <form action="/importar_lotes/{{ Auth::user()->id}}/{{$data['empreendimento']->id}}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="csv_file" accept=".csv">
                    <button type="submit" class="btn btn-primary">Importar</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3 g-3">
    <div class="col-md-3">
        <div class="bg-white rounded p-3 shadow-sm">
            <p class="text-secondary m-0">
                Total lotes
            </p>
            <p class="fw-semibold fs-4 m-0 text-primary">
                {{$total_lotes}}
            </p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="bg-white rounded p-3 shadow-sm">
            <p class="text-secondary m-0">
                Lotes empresa
            </p>
            <p class="fw-semibold fs-4 m-0">
                {{$data['lotesEmpresa']}}
            </p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="bg-white rounded p-3 shadow-sm">
            <p class="text-secondary m-0">
                Lotes clientes
            </p>
            <p class="fw-semibold fs-4 m-0">
                {{$data['lotesClientes']}}
            </p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="bg-white rounded p-3 shadow-sm">
            <p class="text-secondary m-0">
                Lotes escriturados
            </p>
            <p class="fw-semibold fs-4 m-0">
                {{$data['lotesEscriturados']}}
            </p>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="bg-white rounded p-3 shadow-sm">
            <div class="">
                <h3 class="fs-5 mb-3">
                    Débitos em atraso
                </h3>
                <div class="card-body">
                    <canvas id="graficoDividaClienteEmpresa"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded p-3 shadow-sm my-3">
    <h5 class="">
        Legenda
    </h5>
    <div class="row mt-3">
        <div class="col-sm-2">
            <p>
                <span class="bg-danger p-1 text-white rounded fw-bold mr-2">
                    N
                </span>
                Negativado
            </p>
        </div>
        <div class="col-sm-2">
            <p>
                <span class="bg-warning p-1 text-white rounded fw-bold mr-2">
                    AC PAR
                </span>
                Acordo parcial feito
            </p>
        </div>
        <div class="col-sm-2">
            <p>
                <span class="bg-success p-1 text-white rounded fw-bold mr-2">
                    AC
                </span>
                Acordo total feito
            </p>
        </div>
        <div class="col-sm-2">
            <p>
                <span class="material-symbols-outlined bg-success rounded-circle text-white p-1 fs-6 mr-2">
                    call
                </span>
                Telefone verificado
            </p>
        </div>
        <div class="col-sm-2">
            <p>
                <span class="material-symbols-outlined bg-danger rounded-circle text-white p-1 fs-6 mr-2">
                    phone_disabled
                </span>
                Telefone não verificado
            </p>
        </div>
        <div class="col-sm-2">
            <p>
                <span class="bg-primary p-1 text-white rounded fw-bold mr-2">
                    ESCR
                </span>
                Lote escriturado
            </p>
        </div>
    </div>
</div>

<div class="bg-white rounded p-3 shadow-sm">
    <h5 class="">
        Lista de cadastros
    </h5>
    @if(isset($resultado))
    <div class="card-footer">
        <a class="btn btn-add" href="">PDF</a>
        <a class="btn btn-add" href="">Excel</a>
    </div>
    @endif
    <div class="my-3">
        <table class="table table-striped table-bordered text-center">
            <thead>
                <tr>
                    <th></th>
                    <th scope="col">ID</th>
                    <th scope="col">Quadra</th>
                    <th scope="col">Lote</th>
                    <th scope="col">Responsabilidade</th>
                    <th scope="col">Inscrição Municipal</th>
                    <th scope="col">R$ Cliente</th>
                    <th scope="col">R$ Empresa</th>
                    <th scope="col">Telefones</th>
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($resultado))
                @php
                $hoje = \Carbon\Carbon::today()->toDateString();
                @endphp
                @foreach ($resultado as $lote)
                <tr>
                    <td>
                        <input type="checkbox" name="checkboxes[]" value="{{ $lote->id }}" id="inputCheckLote">
                    </td>
                    <td>{{$lote->id}}</td>
                    <td>{{$lote->quadra->nome}}</td>
                    <td scope="row">{{$lote->lote}}</td>

                    @if($lote->cliente)
                    <td class="d-flex align-items-center justify-content-center">

                        <p class="text-truncate m-0" style="max-width: 50%">
                            @if(empty($lote->cliente->nome))
                            {{$lote->cliente->razao_social}}
                            @else
                            {{$lote->cliente->nome}}
                            @endif
                            <br>
                            <span class="text-secondary">
                                {{$lote->data_venda == null ? '' : \Carbon\Carbon::parse($lote->data_venda)->format('d/m/Y')}}
                            </span>
                        </p>
                        @if($lote->cliente->is_contato_verificado == true)
                        <span class="material-symbols-outlined bg-success rounded-circle text-white p-1 fs-6">
                            call
                        </span>
                        @else
                        <span class="material-symbols-outlined bg-danger rounded-circle text-white p-1 fs-6">
                            phone_disabled
                        </span>
                        @endif
                        @if($lote->is_escriturado == true)
                        <span class="bg-primary p-1 text-white rounded fw-bold">
                            ESCR
                        </span>
                        @endif
                    </td>
                    @endif

                    <td>{{ $lote->inscricao_municipal }}
                        @if($lote->negativar == true)
                        <span class="bg-danger p-1 text-white rounded fw-bold">
                            N
                        </span>
                        @endif
                        @if($lote->is_acordo_parcial == true)
                        <span class="bg-warning p-1 text-white rounded fw-bold">
                            AC PAR
                        </span>
                        @endif
                        @if($lote->is_acordo_total == true)
                        <span class="bg-success p-1 text-white rounded fw-bold">
                            AC
                        </span>
                        @endif
                    </td>
                    <td class="text-danger fw-semibold">
                        @php
                        $debito = $lote->debito; // Isso é uma coleção (array) de Debito
                        $valorTotalCliente = 0;
                        @endphp

                        @if($debito != null)
                        @foreach($debito as $d)

                        @foreach($d->parcela_conta_receber as $parcela)

                        @php
                        $valorTotalCliente += $parcela->data_vencimento < $hoje ? $parcela->valor_parcela : 0;
                            @endphp

                            @endforeach

                            @endforeach
                            @endif

                            R$ {{number_format($valorTotalCliente, 2, ',', '.')}}
                    </td>
                    <td>
                        @php
                        $debito = $lote->debito; // Isso é uma coleção (array) de Debito
                        $valorTotalEmpresa = 0;
                        @endphp

                        @if($debito != null)
                        @foreach($debito as $d)

                        @foreach($d->parcela_conta_pagar as $parcela)

                        @php
                        $valorTotalEmpresa += $parcela->data_vencimento < $hoje ? $parcela->valor_parcela : 0;
                            @endphp

                            @endforeach

                            @endforeach
                            @endif

                            R$ {{number_format($valorTotalEmpresa, 2, ',', '.')}}
                    </td>
                    <td>{{$lote->cliente->telefone1}}, {{$lote->cliente->telefone2}}</td>
                    <td>
                        <div class="dropdown">
                            <a href="../../lote/gestao/{{$lote->id}}" class="btn-acao-listagem">Parcelas</a>
                            <a class="btn-acao-listagem dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('nova_venda', ['id' => $lote->id]) }}" class="dropdown-item">Novo
                                        Contrato</a>
                                </li>
                                <li>
                                    <a href="{{ route('iptuCampoGrandeAdicionarDireto', [
                                        'inscricao_municipal' => $lote->inscricao_municipal, 
                                        'lote_id' => $lote->id, 
                                        'is_empreendimento' => 0,
                                        'user_id' => Auth::user()->id, 
                                        ]) }}" class="dropdown-item">Limpar e Adicionar Débitos
                                    </a>
                                </li>
                                <li>
                                    <a href="../../lote/editar/{{$lote->id}}" class="dropdown-item">Ver/Editar</a>
                                </li>
                                <li><a href="../../lote/negativar/{{$lote->id}}" class="dropdown-item">Negativar</a>
                                </li>

                                @if($lote->cliente)

                                <li>
                                    <a href="{{ route('cliente.contato-verificado', ['id' => $lote->cliente->id] ) }}"
                                        class="dropdown-item">
                                        Telefone verificado
                                    </a>
                                </li>

                                @endif

                                <li>
                                    <a href="{{ route('lote.acordo_parcial', ['id' => $lote->id] ) }}"
                                        class="dropdown-item">
                                        Acordo parcial
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('lote.acordo', ['id' => $lote->id] ) }}" class="dropdown-item">
                                        Acordo
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        @if(isset($resultado))
        <div class="card-footer">
            <p>Exibindo {{$resultado->count()}} de {{ $total_lotes }} registros</p>
        </div>
        @endif

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// Obtém os dados do PHP e armazena em variáveis JavaScript
const debitosPagarAtrasados = {!!json_encode($data['debitosPagarAtrasados']) !!};
const debitosReceberAtrasados = {!!json_encode($data['debitosReceberAtrasados']) !!};


new Chart(graficoDividaClienteEmpresa, {
    type: 'pie',
    data: {
        labels: [
            'Empresa',
            'Clientes'
        ],
        datasets: [{
            label: 'R$ Débitos',
            data: [debitosPagarAtrasados, debitosReceberAtrasados],
            backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(54, 162, 235)',
            ],
            hoverOffset: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
    }
});

$(document).ready(function() {

    // Captura o clique no Parcelas Reajustar
    $("#importar_pmcg").click(function(event) {
        event.preventDefault();

        // Obtenha os valores dos checkboxes selecionados
        var checkboxesSelecionados = [];

        $("input[name='checkboxes[]']:checked").each(function() {
            checkboxesSelecionados.push($(this).val());
        });

        // Crie a URL com os valores dos checkboxes como parâmetros de consulta
        var url =
            "{{ route('cadastrar_scraping_empreendimento', ['usuario_id' => Auth::user()->id]) }}&checkboxes=" +
            checkboxesSelecionados.join(',');

        // Redirecione para a URL com os parâmetros
        window.location.href = url;
    });

});
</script>

@endsection