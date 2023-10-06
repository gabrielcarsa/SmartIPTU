@extends('layouts/app')
@section('conteudo')

<h1>Dashboard</h1>
@foreach( $data['titulares_contas'] as $item )
<p>{{$item['nome_cliente_ou_razao_social']}}</p>
<p>{{$item['total_contas_pagar']}}</p>
<p>{{$item['total_debitos']}}</p>
@endforeach



<div class="container text-center">
    <div class="row">
        <div class="col">
            <div class="card">
                <h5 class="card-header">Cadastrar categoria de contas a pagar</h5>
                <div class="card-body">
                    <canvas id="graficoDebitosTitulares"></canvas>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <h5 class="card-header">Cadastrar categoria de contas a pagar</h5>
                <div class="card-body">
                    <canvas id="graficoDividaClienteEmpresa"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Obtém os dados do PHP e armazena em variáveis JavaScript
const labels = {!!json_encode(array_column($data['titulares_contas'], 'nome_cliente_ou_razao_social')) !!};
const data = {!!json_encode(array_column($data['titulares_contas'], 'total_debitos')) !!};

const graficoDebitosTitulares = document.getElementById('graficoDebitosTitulares');
const graficoDividaClienteEmpresa = document.getElementById('graficoDividaClienteEmpresa');


new Chart(graficoDebitosTitulares, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: '# of Votes',
            data: data,
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
        labels: ['Red', 'Blue', 'Yellow'],
        datasets: [{
            label: 'My First Dataset',
            data: [300, 50, 100],
            backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(54, 162, 235)',
                'rgb(255, 205, 86)'
            ],
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

</script>

@endsection