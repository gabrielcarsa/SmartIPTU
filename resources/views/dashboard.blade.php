@extends('layouts/app')
@section('conteudo')

<div class="row">
    <div class="col-md-3">
        <div class="card-dashboard d-flex align-items-center" style="background-color:RGB(255, 179, 0);">
            <div class="row">
                <div class="col-md-4">
                    <span class="material-symbols-outlined">
                        money_off
                    </span>
                </div>
                <div class="col-md-8 align-self-center">
                    <h3>Contas a Pagar Hoje</h3>
                    <p>R$ 2.352,12</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-dashboard d-flex align-items-center" style="background-color:RGB(148, 216, 45);">
            <div class="row">
                <div class="col-md-4">
                    <span class="material-symbols-outlined">
                        attach_money
                    </span>
                </div>
                <div class="col-md-8 align-self-center">
                    <h3>Contas a Receber Hoje</h3>
                    <p>R$ 2.352,12</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-dashboard d-flex align-items-center" style="background-color:RGB(250, 82, 82);">
            <div class="row">
                <div class="col-md-4">
                    <span class="material-symbols-outlined">
                        trending_down
                    </span>
                </div>
                <div class="col-md-8 align-self-center">
                    <h3>Pagamentos Atrasados</h3>
                    <p>R$ 2.352,12</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-dashboard d-flex align-items-center" style="background-color:RGB(255, 248, 91);">
            <div class="row">
                <div class="col-md-4">
                    <span class="material-symbols-outlined">
                        gavel
                    </span>
                </div>
                <div class="col-md-8 align-self-center">
                    <h3>Total de Débitos de IPTUs</h3>
                    <p>R$ 2.352,12</p>
                </div>
            </div>
        </div>
    </div>    
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <h5 class="card-header">Débitos a receber de clientes</h5>
            <div class="card-body">
                <canvas id="graficoReceberDebitos"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-ranking">
                <h4>Ranking Média Maiores Saídas por Categoria ao Mês</h4>
                <div>
                    <div class="linha-ranking">
                        <p>Pagamento de Exemplo</p>
                        <div class="progress" role="progressbar" aria-label="Basic example" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar" style="width: 75%">18.000,00/mês</div>
                        </div>
                    </div>
                    <div class="linha-ranking">
                        <p>Pagamento de Exemplo</p>
                        <div class="progress" role="progressbar" aria-label="Basic example" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar" style="width: 40%">11.000,00/mês</div>
                        </div>
                    </div><div class="linha-ranking">
                        <p>Pagamento de Exemplo</p>
                        <div class="progress" role="progressbar" aria-label="Basic example" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar" style="width: 25%">6.080,00/mês</div>
                        </div>
                    </div><div class="linha-ranking">
                        <p>Pagamento de Exemplo</p>
                        <div class="progress" role="progressbar" aria-label="Basic example" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar" style="width: 7%">2.000,00/mês</div>
                        </div>
                    </div><div class="linha-ranking">
                        <p>Pagamento de Exemplo</p>
                        <div class="progress" role="progressbar" aria-label="Basic example" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar" style="width: 2%">760,00/mês</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(isset($data['tipo_debitos'][0]))
<div class="row">
    <div class="col">
        <div class="card">
            <h5 class="card-header">Lotes</h5>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr class="text-left">
                            <th scope="col">Empreendimento</th>
                            <th scope="col">Qtnd. de lotes {{$data['tipo_debitos'][0]->descricao}}</th>
                            <th scope="col">Qtnd. de lotes {{$data['tipo_debitos'][1]->descricao}}</th>
                            <th scope="col">Qtnd. de lotes {{$data['tipo_debitos'][2]->descricao}}</th>
                            <th scope="col">Qtnd. de lotes {{$data['tipo_debitos'][3]->descricao}}</th>
                            <th scope="col">Total Lotes</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($data['lotesEmpreendimentos'] as $resultado)
                        <tr class="resultados-table text-left">
                            <td scope="row">{{$resultado->empreendimento}}</td>
                            <td scope="row">{{$resultado->total_lotes_1}}</td>
                            <td scope="row">{{$resultado->total_lotes_2}}</td>
                            <td scope="row">{{$resultado->total_lotes_3}}</td>
                            <td scope="row">{{$resultado->total_lotes_4}}</td>
                            <td scope="row">{{ $resultado->total_lotes }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col">
        <div class="card">
            <h5 class="card-header">Débitos referentes a Clientes e Empresa</h5>
            <div class="card-body">
                <canvas id="graficoDividaClienteEmpresa"></canvas>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <h5 class="card-header">Total de Contas a Pagar por titular (não incluso débitos de IPTU)</h5>
            <div class="card-body">
                <canvas id="graficoDebitosTitulares"></canvas>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>
// Obtém os dados do PHP e armazena em variáveis JavaScript
const labels_graficoDebitosTitulares = {!!json_encode(array_column($data['titulares_contas'], 'nome_cliente_ou_razao_social')) !!};
const data_graficoDebitosTitulares = {!!json_encode(array_column($data['titulares_contas'], 'total_contas_pagar')) !!};

const labels_graficoDividaClienteEmpresa = {!!json_encode(array_column($data['debitosEmpresaCliente'], 'nome_cliente_ou_razao_social')) !!};
const data_graficoDividaClienteEmpresa = {!!json_encode(array_column($data['debitosEmpresaCliente'], 'total_debitos')) !!};


const labels_graficoReceberDebitos = {!!json_encode(array_column($data['receberPorAnos'], 'ano_vencimento')) !!};
const data_graficoReceberDebitos = {!!json_encode(array_column($data['receberPorAnos'], 'total_debitos')) !!};

const graficoDebitosTitulares = document.getElementById('graficoDebitosTitulares');
const graficoDividaClienteEmpresa = document.getElementById('graficoDividaClienteEmpresa');
const graficoReceberDebitos = document.getElementById('graficoReceberDebitos');



new Chart(graficoDebitosTitulares, {
    type: 'bar',
    data: {
        labels: labels_graficoDebitosTitulares,
        datasets: [{
            label: 'R$ Contas a Pagar',
            data: data_graficoDebitosTitulares,
            backgroundColor: [
                'rgba(177, 7, 192, 0.8)',
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

new Chart(graficoDividaClienteEmpresa, {
    type: 'pie',
    data: {
        labels: labels_graficoDividaClienteEmpresa,
        datasets: [{
            label: 'R$ Débitos',
            data: data_graficoDividaClienteEmpresa,
            backgroundColor: [
                'rgb(54, 162, 235)',
                'rgb(255, 99, 132)',
            ],
            hoverOffset: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
    }
});

new Chart(graficoReceberDebitos, {
    type: 'bar',
    data: {
        labels: labels_graficoReceberDebitos,
        datasets: [{
            label: 'R$ Débitos a receber',
            data: data_graficoReceberDebitos,
            backgroundColor: [
                'RGBA(0, 139, 139)',
            ],
            borderWidth: 1
        }]
    },
    options: {
        indexAxis: 'y',
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

@endsection