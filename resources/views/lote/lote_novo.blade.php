@extends('layouts/app')

@section('conteudo')

<h2>
    @if (isset($lote))
    Alterar Lote
    @else
    Novo Lote
    @endif
</h2>

<div class="card">
    <h5 class="card-header">Preencha os campos requisitados *</h5>
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

    <div class="card-body">
        @if (isset($lote))
        <p>
            Cadastrado por <strong>{{$cadastrado_por_user->name}}</strong> em
            {{ \Carbon\Carbon::parse($lote->data_cadastro)->format('d/m/Y') }}
        </p>
        @if (isset($alterado_por_user))
        <p>
            Última alteração feita por <strong>{{$alterado_por_user->name}}</strong> em
            {{ \Carbon\Carbon::parse($lote->data_alteracao)->format('d/m/Y') }}
        </p>
        @endif
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#exampleModal">
            Excluir lote
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Excluir {{$lote->nome}}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Deseja mesmo excluir esse lote? </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não</button>
                        <a href="../excluir/{{$lote->id}}" class="btn btn-danger">Sim, excluir</a>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        @endif

        <form class="row g-3"
            action="{{ isset($lote) ? '/lote/alterar/' . $lote->id . '/' . Auth::user()->id : '/lote/cadastrar/' . Auth::user()->id . '/' . $empreendimento_id}}"
            method="post" autocomplete="off">
            @csrf
            <div class="col-md-3">
                <label for="inputQuadra" class="form-label">Quadra*</label>
                <select id="inputQuadra" name="quadra" class="form-select">
                    <option value="0">-- Selecione --</option>
                    @foreach ($quadras as $quadra)
                    <option value="{{$quadra->quadra_id}}">{{$quadra->quadra_nome}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="inputLote" id="lote" class="form-label">Lote*</label>
                <input type="text" name="lote" value="{{isset($lote) ? $lote->lote : old('lote')}}"
                    class="form-control @error('lote') is-invalid @enderror" id="inputLote">
            </div>
            <div class="col-md-3">
                <label for="inputReponsabilidade" class="form-label">Responsabilidade*</label>
                <select id="inputReponsabilidade" name="responsabilidade" class="form-select">
                    <option value="0">-- Selecione --</option>
                    @foreach ($clientes as $cliente)
                    <option value="{{$cliente->id}}">
                        @if(empty($cliente->nome))
                        {{$cliente->razao_social}}
                        @else
                        {{$cliente->nome}}
                        @endif
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn-submit">
                    @if (isset($lote))
                    Alterar
                    @else
                    Cadastrar
                    @endif
                </button>
            </div>
        </form>
    </div>
</div>
@endsection