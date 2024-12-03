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
    <table>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">R / Q / L</th>
                    <th scope="col">Inscrição</th>
                    <th scope="col">Data Venda</th>
                    <th scope="col">2018</th>
                    <th scope="col">2019</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dados as $dado)
     
                <tr>
                    <td>{{$dado['lote']->quadra->empreendimento->nome}} / {{$dado['lote']->quadra->nome}} / {{$dado['lote']->lote}}</td>
                    <td>{{$dado['lote']->inscricao_municipal}}</td>
                    <td>{{\Carbon\Carbon::parse($dado['lote']->data_venda)->format('d/m/Y')}}</td>
                    <td>
                        @if($dado['debitoEmpresa2018']->isNotEmpty())
                        @php
                        $total = 0;
                        @endphp

                        @foreach($dado['debitoEmpresa2018'] as $parcela)
                        @php
                        $total += $parcela->valor_parcela;
                        @endphp
                        @endforeach
                        <span class="text-danger">
                            Empresa - R$ {{number_format($total, 2, ',', '.')}}
                        </span>
                        @elseif($dado['debitoCliente2018']->isNotEmpty())
                        @php
                        $total = 0;
                        @endphp

                        @foreach($dado['debitoCliente2018'] as $parcela)
                        @php
                        $total += $parcela->valor_parcela;
                        @endphp
                        @endforeach
                        <span class="text-dark">
                            Cliente - R$ {{number_format($total, 2, ',', '.')}}
                        </span>
                        @endif
                    </td>
                    <td>
                        @if($dado['debitoEmpresa2019']->isNotEmpty())
                        @php
                        $total = 0;
                        @endphp

                        @foreach($dado['debitoEmpresa2019'] as $parcela)
                        @php
                        $total += $parcela->valor_parcela;
                        @endphp
                        @endforeach
                        <span class="text-danger">
                            Empresa - R$ {{number_format($total, 2, ',', '.')}}
                        </span>
                        @elseif($dado['debitoCliente2019']->isNotEmpty())
                        @php
                        $total = 0;
                        @endphp

                        @foreach($dado['debitoCliente2019'] as $parcela)
                        @php
                        $total += $parcela->valor_parcela;
                        @endphp
                        @endforeach
                        <span class="text-dark">
                            Cliente - R$ {{number_format($total, 2, ',', '.')}}
                        </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </table>
    @endif

</div>

@endsection