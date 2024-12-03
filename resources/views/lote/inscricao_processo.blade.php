@extends('layouts/app')

@section('conteudo')

<div class="container">
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
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
    <div class="row">
        <div class="col">
            <p>
                Processar inscrições
            </p>
            <form action="{{ route('lote.postInscricaoProcesso') }}" method="post">
                @csrf
                <textarea name="inscricoes" id=""></textarea>
                <button class="btn btn-primary" type="submit">
                    Salvar
                </button>
            </form>
        </div>
        <div class="col">
            <p>
                Subir PDF para extrair inscrições
            </p>
            <form action="{{ route('lote.processarPDF') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="file" name="arquivo" id="" class="form-control @error('arquivo') is-invalid @enderror">
                <button class="btn btn-primary" type="submit">
                    Salvar
                </button>
            </form>
        </div>
    </div>
    @if(isset($dados))
    <p>
        @foreach($lotesNaoEntrados as $lote)
    <p>
        {{$lote}}
    </p>
    @endforeach
    </p>
    <table>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">R / Q / L</th>
                    <th scope="col">Inscrição</th>
                    <th scope="col">Data Venda</th>
                    <th scope="col">2018</th>
                    <th scope="col">R$ 2018</th>
                    <th scope="col">2019</th>
                    <th scope="col">R$ 2019</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dados as $dado)

                @if($dado['lote'] != null)
                <tr>
                    <td>
                        {{$dado['lote']->quadra->empreendimento->nome}} / {{$dado['lote']->quadra->nome ?? ''}} /
                        {{$dado['lote']->lote}}
                    </td>
                    <td>{{$dado['lote']->inscricao_municipal}}</td>
                    <td>{{\Carbon\Carbon::parse($dado['lote']->data_venda)->format('d/m/Y')}}</td>

                    @if($dado['debitoEmpresa2018']->isNotEmpty())
                    @php
                    $total = 0;
                    @endphp

                    @foreach($dado['debitoEmpresa2018'] as $parcela)
                    @php
                    $total += $parcela->valor_parcela;
                    @endphp
                    @endforeach
                    <td>
                        <span class="text-danger">
                            Empresa
                        </span>
                    </td>
                    <td>
                        <span class="text-danger">
                            R$ {{number_format($total, 2, ',', '.')}}
                        </span>
                    </td>
                    @elseif($dado['debitoCliente2018']->isNotEmpty())
                    @php
                    $total = 0;
                    @endphp

                    @foreach($dado['debitoCliente2018'] as $parcela)
                    @php
                    $total += $parcela->valor_parcela;
                    @endphp
                    @endforeach
                    <td>
                        <span class="text-dark">
                            Cliente
                        </span>
                    </td>
                    <td>
                        <span class="text-dark">
                            R$ {{number_format($total, 2, ',', '.')}}
                        </span>
                    </td>
                    @endif
                    @if($dado['debitoEmpresa2019']->isNotEmpty())
                    @php
                    $total = 0;
                    @endphp

                    @foreach($dado['debitoEmpresa2019'] as $parcela)
                    @php
                    $total += $parcela->valor_parcela;
                    @endphp
                    @endforeach
                    <td>
                        <span class="text-danger">
                            Empresa
                        </span>
                    </td>
                    <td>
                        <span class="text-danger">
                            R$ {{number_format($total, 2, ',', '.')}}
                        </span>
                    </td>
                    @elseif($dado['debitoCliente2019']->isNotEmpty())
                    @php
                    $total = 0;
                    @endphp

                    @foreach($dado['debitoCliente2019'] as $parcela)
                    @php
                    $total += $parcela->valor_parcela;
                    @endphp
                    @endforeach
                    <td>
                        <span class="text-dark">
                            Cliente
                        </span>
                    </td>
                    <td>
                        <span class="text-dark">
                            R$ {{number_format($total, 2, ',', '.')}}
                        </span>
                    </td>
                    @endif
                </tr>
                @endif

                @endforeach
            </tbody>
        </table>
    </table>
    @endif

</div>

@endsection