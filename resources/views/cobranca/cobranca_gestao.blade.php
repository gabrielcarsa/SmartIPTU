@extends('layouts/app')

@section('conteudo')

<h2>
    Cobrança
</h2>

<div class="card">

    <h5 class="card-header">Parcelamentos para esse mês</h5>

    <div class="card-body">

        @if(isset($data['parcelasParcelamento']))

        <table class="table table-bordered table-striped text-center">

            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <!--<th scope="col">Titular a receber</th>-->
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

                @foreach ($data['parcelasParcelamento'] as $resultado)
                <tr>
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
                        @else
                        Pago
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>
        @if(isset($data['resultados']))
        <div class="card-footer">
            <p>Exibindo {{$data['resultados']->count()}} registros</p>
            <p>Valor total das parcelas: R$ {{number_format($data['totalValorParcelas'], 2, ',', '.')}}</p>
            <p>Valor total pago: R$ {{number_format($data['totalValorPago'], 2, ',', '.')}}</p>
        </div>
        @endif


        @endif
    </div>
</div>
@endsection