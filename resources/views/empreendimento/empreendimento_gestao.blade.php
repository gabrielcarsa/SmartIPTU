@extends('layouts/app')

@section('conteudo')

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif
@if (session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
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

<h2>{{$empreendimento->nome}}</h2>
<a class="btn btn-primary btn-add" href="../../quadra/novo/{{$empreendimento->id}}" style="margin-bottom: 20px">
    <span class="material-symbols-outlined">
        add
    </span>Nova Quadra</a>
<a class="btn btn-primary btn-add" href="../../lote/novo/{{$empreendimento->id}}" style="margin-bottom: 20px">
    <span class="material-symbols-outlined">
        add
    </span>Novo Lote</a>
<a class="btn btn-primary btn-add" data-bs-toggle="modal" data-bs-target="#exampleModal" style="margin-bottom: 20px">
    <span class="material-symbols-outlined">
        expand_less
    </span>Subir planilha de Lotes</a>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Selecione a planilha</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Selecione o arquivo no formato .csv</p>
                <form action="/importar_lotes/{{ Auth::user()->id}}/{{$empreendimento->id}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="csv_file" accept=".csv">
                    <button type="submit" class="btn btn-primary">Importar</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>


<div class="card">
    <h5 class="card-header">Filtros para buscar</h5>
    <div class="card-body">
        <form class="row g-3" action="/lotes" method="get" autocomplete="off">
            @csrf
            <div class="col-md-6">
                <label for="inputQuadra" class="form-label">Quadra</label>
                <input type="text" name="quadra" value="{{request('quadra')}}" class="form-control" id="inputQuadra">
            </div>
            <div class="col-md-6">
                <label for="inputLote" class="form-label">Lote</label>
                <input type="text" name="lote" value="{{request('lote')}}" class="form-control" id="inputLote">
            </div>
            <div class="col-12">
                <button type="submit" class="btn-submit">Consultar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <h5 class="card-header">Lista de cadastros</h5>
    @if(isset($resultado))
    <div class="card-footer">
        <a class="btn btn-add" href="">PDF</a>
        <a class="btn btn-add" href="">Excel</a>
    </div>
    @endif
    <div class="card-body">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Quadra</th>
                    <th scope="col">Lote</th>
                    <th scope="col">Responsabilidade</th>
                    <th scope="col">Inscrição Municipal</th>
                    <th scope="col">Data da Venda</th>
                    <th scope="col">Telefones</th>
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($resultado))
                @foreach ($resultado as $lote)
                <tr>
                    <td>{{$lote->lote_id}}</td>
                    <td>{{$lote->quadra_nome}}</td>
                    <th scope="row">{{$lote->lote}}</th>
                    @if(empty($lote->nome_cliente))
                    <td>{{$lote->razao_social__cliente}}</td>
                    @else
                    <td>{{$lote->nome_cliente}}</td>
                    @endif
                    <td>{{$lote->inscricao_municipal}}</td>
                    <td>{{$lote->data_venda == null ? '' : \Carbon\Carbon::parse($lote->data_venda)->format('d/m/Y')}}
                    </td>
                    <td>{{$lote->tel1}}, {{$lote->tel2}}</td>
                    <td>
                        <div class="dropdown">
                            <a href="../../lote/gestao/{{$lote->lote_id}}" class="btn-acao-listagem">Parcelas</a>
                            <a class="btn-acao-listagem dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Ações
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('nova_venda', ['id' => $lote->lote_id]) }}"
                                        class="dropdown-item">Novo
                                        Contrato</a></li>
                                <li><a href="{{ route('iptuCampoGrandeAdicionarDireto', ['inscricao_municipal' => $lote->inscricao_municipal, 'lote_id' => $lote->lote_id, 'user_id' => Auth::user()->id]) }}"
                                        class="dropdown-item">Adicionar Débitos</a></li>
                                <li><a href="../../lote/editar/{{$lote->lote_id}}" class="dropdown-item">Ver/Editar</a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        @if(isset($resultado))
        <div class="card-footer">
            <p>Exibindo {{$resultado->count()}} de {{ $total_lotes }} registros</p>
        </div>
        @endif

    </div>
</div>

@endsection