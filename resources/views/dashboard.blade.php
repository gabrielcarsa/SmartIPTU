@extends('layouts/app')
@section('conteudo')

<h1>Dashboard</h1>


<p>
A fazer
- Reajustar/ Vencimento/ Baixar Parcelas de Contas a Pagar/Receber (mascara dinheiro)
- Validações
- 
</p>



<div class="container text-center">
    <div class="row">
        <div class="col">
            <div class="card">
                <h5 class="card-header">Total de Contas a Pagar por titular (não incluso débitos de IPTU)</h5>
                <div class="card-body">
                    <canvas id="graficoDebitosTitulares"></canvas>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <h5 class="card-header">Débitos referentes a Clientes e Empresa</h5>
                <div class="card-body">
                    <canvas id="graficoDividaClienteEmpresa"></canvas>
                </div>
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
const data_graficoDividaClienteEmpresa= {!!json_encode(array_column($data['debitosEmpresaCliente'], 'total_debitos')) !!};

const graficoDebitosTitulares = document.getElementById('graficoDebitosTitulares');
const graficoDividaClienteEmpresa = document.getElementById('graficoDividaClienteEmpresa');


new Chart(graficoDebitosTitulares, {
    type: 'bar',
    data: {
        labels: labels_graficoDebitosTitulares,
        datasets: [{
            label: 'R$ Contas a Pagar',
            data: data_graficoDebitosTitulares,
            backgroundColor: [
             'rgba(224, 49, 49, 0.8)',
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
    type: 'doughnut',
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

</script>

@endsection