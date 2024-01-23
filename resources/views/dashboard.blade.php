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
                    <p>R$ {{number_format($data['pagarHoje'], 2, ',', '.')}}</p>
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
                    <p>R$ {{number_format($data['receberHoje'], 2, ',', '.')}}</p>
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
                    <h3>Total Débitos Empresa</h3>
                    <p>R$ {{number_format($data['pagamentosAtrasados'], 2, ',', '.')}}</p>
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
                    <h3>Total de Débitos</h3>
                    <p>R$ {{number_format($data['totalDividaDebitos'], 2, ',', '.')}}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-ranking text-center">
                <h4>Débitos em atraso - EMPRESA X CLIENTES</h5>
                <div class="card-body">
                    <canvas id="graficoDividaClienteEmpresa"></canvas>
                </div>
            </div>
        </div>
            
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-ranking">
                <h4>Ranking Maiores Saídas por Categoria - 30 dias</h4>
                <div>
                    @if(isset($data['rankingSaidas']))
                    @foreach($data['rankingSaidas'] as $categorias)
                    <div class="linha-ranking">
                        <p>{{$categorias->categoria}}</p>
                        <div class="progress" role="progressbar" aria-label="Basic example" aria-valuenow="{{($categorias->total*100)/$data['totalSaidas']}}"
                            aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar" style="width: {{($categorias->total*100)/$data['totalSaidas']}}%">R$ {{number_format($categorias->total, 2, ',', '.')}}</div>
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
            data: [debitosPagarAtrasados,debitosReceberAtrasados],
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