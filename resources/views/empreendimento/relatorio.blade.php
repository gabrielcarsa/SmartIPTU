@extends('layouts/app')

@section('conteudo')
<table class="table">
    <thead>
        <tr>
            <th scope="col">Bairro</th>
            <th scope="col">Qd</th>
            <th scope="col">Lt</th>
            <th scope="col">Responsabilidade</th>
            <th scope="col">Inscrição</th>
            <th scope="col">Data compra</th>
            <th scope="col">Telefone</th>
            <th scope="col">Escriturado?</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($data['resultado']))

        @foreach ($data['resultado'] as $lote)
        <tr>
            <td>{{$data['empreendimento']->nome}}</td>
            <td>{{$lote->quadra->nome}}</td>
            <td>{{$lote->lote}}</td>
            <td>
                @if(empty($lote->cliente->nome))
                {{$lote->cliente->razao_social}}
                @else
                {{$lote->cliente->nome}}
                @endif
            </td>
            <td>
                {{ $lote->inscricao_municipal }}
            </td>
            <td>
                @if($lote->data_venda != null)
                <span class="text-secondary">
                    {{\Carbon\Carbon::parse($lote->data_venda)->format('d/m/Y')}}
                </span>
                <br>
                @endif
            </td>
            <td>
                {{$lote->cliente->telefone1}}, {{$lote->cliente->telefone2}}
            </td>
            <td>
                @if($lote->is_escriturado == true)
                <span class="bg-primary p-1 text-white rounded fw-bold">
                    ESCR
                </span>
                @endif
            </td>
        </tr>

        @endforeach
        @endif
    </tbody>
</table>
@endsection