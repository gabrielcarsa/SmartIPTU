<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <title>Relatório Movimentação Financeira</title>

    <style>
    /* Estilo básico para tabelas */
    .table {
        width: 100%;
        margin-bottom: 1rem;
        font-size: 8px;
        color: #212529;
    }

    /* Estilo para cabeçalho de tabela */
    .table th {
        color: #6002ee;
        padding: 0.75rem;
        vertical-align: top;
        border-top: 1px solid #dee2e6;
    }

    /* Estilo para células de tabela */
    .table td {
        padding: 0.75rem;
        vertical-align: top;
        border-top: 1px solid #dee2e6;
    }

    /* Estilo para tabelas listradas (alternância de cores) */
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.05);
    }

    /* Estilo para tabelas com bordas */
    .table-bordered {
        border: 1px solid #dee2e6;
    }

    /* Estilo para tabelas responsivas em dispositivos móveis */
    .table-responsive {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Estilo para tabelas pequenas */
    .table-sm th,
    .table-sm td {
        padding: 0.3rem;
    }

    h4 {
        font-weight: 900;

    }
    </style>
</head>

<body>
    <p style="font-size: 8px;">GHC Tecnologia - <strong>SmartIPTU</strong></p>
    <div class="">
        <h4 style="text-align: center !important; font-size: 216px;">
            Movimentações do dia {{\Carbon\Carbon::parse($data['data'])->format('d/m/Y')}} -
            {{\Carbon\Carbon::parse($data['data_fim'])->format('d/m/Y')}}
        </h4>
        @if($movimentacao[0]->nome_titular != null)
        <p style="font-size: 10px;">{{$movimentacao[0]->nome_titular}}</p>
        @else
        <p style="font-size: 10px">{{$movimentacao[0]->razao_social_titular}}</p>
        @endif
        <p style="font-size: 10px;">{{$movimentacao[0]->conta_corrente}}</p>

        <table class="table table-striped text-center">
            <thead>
                <tr>
                    <th scope="col">Data</th>
                    <th scope="col">Cliente / Fornecedor</th>
                    <th scope="col">Categoria</th>
                    <th scope="col">Descrição</th>
                    <th scope="col">Valor da Entrada (R$)</th>
                    <th scope="col">Valor da Saída (R$)</th>
                    <th scope="col">Saldo (R$)</th>
                </tr>
            </thead>
            @php
            $saldoAtual = isset($data['saldo_anterior'][0]) ? $data['saldo_anterior'][0]->saldo : 0; // Inicializa
            @endphp
            <tbody>
                @if(isset($movimentacao))
                @foreach ($movimentacao as $mov)
                <!-- Calcula o saldo atual com base na movimentação e no saldo anterior -->
                @php
                if ($mov->tipo_movimentacao == 0) { // Se for uma entrada
                $saldoAtual += $mov->valor; // Adiciona o valor da entrada ao saldo atual
                } else { // Se for uma saída
                $saldoAtual -= $mov->valor; // Subtrai o valor da saída do saldo atual
                }
                @endphp

                <tr>
                    <td>{{\Carbon\Carbon::parse($mov->data_movimentacao)->format('d/m/Y')}}</td>
                    @if($mov->tipo_cadastro == 0)
                    <td class="align-middle">{{$mov->nome}}</td>
                    @else
                    <td class="align-middle">{{$mov->razao_social}}</td>
                    @endif

                    @if($mov->tipo_movimentacao == 0)
                    <td class="align-middle">{{$mov->tipo_debito == null ? $mov->categoria_receber : $mov->tipo_debito}}
                    </td>
                    @else
                    <td class="align-middle">{{$mov->tipo_debito == null ? $mov->categoria_pagar : $mov->tipo_debito}}
                    </td>
                    @endif

                    <td class="align-middle">{{$mov->descricao}}</td>

                    @if($mov->tipo_movimentacao == 0)
                    <td class="align-middle entradaMovimentacao">{{number_format($mov->valor, 2, ',', '.')}}</td>
                    <td class="align-middle"></td>
                    <td class="align-middle">{{number_format($saldoAtual, 2, ',', '.')}}</td>
                    @else
                    <td class="align-middle"></td>
                    <td class="align-middle saidaMovimentacao">{{number_format($mov->valor, 2, ',', '.')}}</td>
                    <td class="align-middle">{{number_format($saldoAtual, 2, ',', '.')}}</td>
                    @endif
                </tr>

                @endforeach
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><strong>Valor Total</strong></td>
                    <td><strong>{{number_format($data['valorEntradas'], 2, ',', '.')}}</strong></td>
                    <td><strong>{{number_format($data['valorSaidas'], 2, ',', '.')}}</strong></td>
                    <td><strong>{{number_format($data['saldo_atual'][0]->saldo, 2, ',', '.')}}</strong></td>

                </tr>
            </tfoot>
        </table>

    </div>

</body>

</html>