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
                    <h3>Total de Parcelas Atrasadas</h3>
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
                    <h3>Total de Débitos Atrasados</h3>
                    <p>R$ {{number_format($data['totalDividaDebitos'], 2, ',', '.')}}</p>
                </div>
            </div>
        </div>
    </div>    
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>

</script>

@endsection