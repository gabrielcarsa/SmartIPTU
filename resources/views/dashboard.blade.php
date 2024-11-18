@extends('layouts/app')
@section('conteudo')

<div class="row g-3">
    <div class="col-md-3">
        <div class="p-3 shadow-md rounded d-flex align-items-center bg-white">
            <div class="row">
                <div class="col-md-4">
                    <span class="material-symbols-outlined bg-fundo text-danger p-3 fs-1 rounded">
                        money_off
                    </span>
                </div>
                <div class="col-md-8 align-self-center">
                    <h3 class="fs-4">
                        A pagar de hoje
                    </h3>
                    <p class="m-0">
                        R$ {{number_format($data['pagarHoje'], 2, ',', '.')}}
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="p-3 shadow-md rounded d-flex align-items-center bg-white">
            <div class="row">
                <div class="col-md-4">
                    <span class="material-symbols-outlined bg-fundo text-success p-3 fs-1 rounded">
                        attach_money
                    </span>
                </div>
                <div class="col-md-8 align-self-center">
                    <h3 class="fs-4">
                        A Receber Hoje
                    </h3>
                    <p class="m-0">
                        R$ {{number_format($data['receberHoje'], 2, ',', '.')}}
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="p-3 shadow-md rounded d-flex align-items-center bg-white">
            <div class="row">
                <div class="col-md-4">
                    <span class="material-symbols-outlined bg-fundo text-danger p-3 fs-1 rounded">
                        trending_down
                    </span>
                </div>
                <div class="col-md-8 align-self-center">
                    <h3 class="fs-4">
                        Contas atrasadas
                    </h3>
                    <p class="m-0">
                        R$ {{number_format($data['pagamentosAtrasadosOutros'], 2, ',', '.')}}
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="p-3 shadow-md rounded d-flex align-items-center bg-white">
            <div class="row">
                <div class="col-md-4">
                    <span class="material-symbols-outlined bg-fundo text-danger p-3 fs-1 rounded">
                        gavel
                    </span>
                </div>
                <div class="col-md-8 align-self-center">
                    <h3 class="fs-4">
                        IPTU em atraso total
                    </h3>
                    <p class="m-0">
                        R$ {{number_format($data['totalDebitos'], 2, ',', '.')}}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row my-3 g-3">
    <div class="col-md-2">
        <div class="bg-white rounded p-3 shadow-md">
            <div class="d-flex">
                <p class="m-0 fs-1 fw-semibold text-primary">
                    {{$data['lotesTotal']}}
                </p>
                <p class="my-0 mx-3 fs-5">
                    Lotes<br>cadastrados
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="bg-white rounded p-3 shadow-md">
            <div class="d-flex">
                <p class="m-0 fs-1 fw-semibold">
                    {{$data['lotesEmpresa']}}
                </p>
                <p class="my-0 mx-3 fs-5">
                    Lotes<br>Empresa
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="bg-white rounded p-3 shadow-md">
            <div class="d-flex">
                <p class="m-0 fs-1 fw-semibold">
                    {{$data['lotesClientes']}}
                </p>
                <p class="my-0 mx-3 fs-5">
                    Lotes<br>Clientes
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="bg-white rounded p-3 shadow-md">
            <p class="m-0 text-secondary">
                Bem-vindo novamente,
            </p>
            <p class="my-0 fs-5">
                {{Auth::guard()->user()->name}}
            </p>
        </div>
    </div>
</div>

<div class="row mt-3 g-3">
    <div class="col-md-6">
        <div class="bg-white rounded p-3 shadow-md">
            <div class="text-center">
                <h4 class="fs-4">
                    IPTU débitos em atraso
                </h4>
                <div class="card-body">
                    <canvas id="graficoDividaClienteEmpresa"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="bg-white rounded p-3 shadow-md">
            <div class="c">
                <h4 class="fs-4 m-0">
                    Saídas por categoria
                </h4>
                <p class="text-secondary">
                    Últimos 30 dias
                </p>
                <div>
                    @if(isset($data['rankingSaidas']))
                    @foreach($data['rankingSaidas'] as $categorias)
                    <div class="my-3 border p-3 rounded">
                        <p class="m-0 fw-semibold">
                            {{$categorias->categoria}}
                        </p>
                        <p class="m-0">
                            R$ {{number_format($categorias->total, 2, ',', '.')}}
                        </p>
                        <div class="progress" role="progressbar" aria-label="Basic example"
                            aria-valuenow="{{($categorias->total*100)/$data['totalSaidas']}}" aria-valuemin="0"
                            aria-valuemax="100">
                            <div class="progress-bar" style="width: {{($categorias->total*100)/$data['totalSaidas']}}%">
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p>Não foi possível carregar esse recurso!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

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
</script>

@endsection