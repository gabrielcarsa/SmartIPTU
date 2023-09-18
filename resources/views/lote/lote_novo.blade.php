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
                <select id="inputQuadra" name="quadra_id"
                    class="form-select form-control @error('quadra_id') is-invalid @enderror">
                    <option value="0" {{ old('quadra_id') == 0 ? 'selected' : '' }}>-- Selecione --</option>
                    @foreach ($quadras as $quadra)
                    <option value="{{$quadra->quadra_id}}"
                        {{ old('quadra_id') == $quadra->quadra_id ? 'selected' : '' }}>
                        {{$quadra->quadra_nome}}
                    </option>
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
                <select id="inputReponsabilidade" name="cliente_id"
                    class="form-select form-control @error('cliente_id') is-invalid @enderror">
                    <option value="0" {{ old('cliente_id') == 0 ? 'selected' : '' }}>-- Selecione --</option>
                    @foreach ($clientes as $cliente)
                    <option value="{{$cliente->id}}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                        @if(empty($cliente->nome))
                        {{$cliente->razao_social}}
                        @else
                        {{$cliente->nome}}
                        @endif
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="inputMatricula" id="matricula" class="form-label">Matrícula*</label>
                <input type="text" name="matricula" value="{{isset($lote) ? $lote->matricula : old('matricula')}}"
                    class="form-control @error('matricula') is-invalid @enderror" id="inputMatricula">
            </div>
            <div class="col-md-3">
                <label for="inputInscricaoMunicipal" id="inscricao_municipal" class="form-label">Inscrição
                    Municipal*</label>
                <input type="text" name="inscricao_municipal"
                    value="{{isset($lote) ? $lote->inscricao_municipal : old('inscricao_municipal')}}"
                    class="form-control @error('inscricao_municipal') is-invalid @enderror"
                    id="inputInscricaoMunicipal">
            </div>
            <div class="col-md-3">
                <label for="inputValor" id="valor" class="form-label">Valor</label>
                <input type="text" name="valor" value="{{isset($lote) ? $lote->valor : old('valor')}}"
                    class="form-control @error('valor') is-invalid @enderror" id="inputValor">
            </div>
            <div class="col-md-3">
                <label for="inputMetrosQuadrados" id="metros_quadrados" class="form-label">Metros quadrados</label>
                <input type="text" name="metros_quadrados"
                    value="{{isset($lote) ? $lote->metros_quadrados : old('metros_quadrados')}}"
                    class="form-control @error('metros_quadrados') is-invalid @enderror" id="inputMetrosQuadrados">
            </div>
            <div class="col-md-3">
                <label for="inputEndereco" id="endereco" class="form-label">Endereço</label>
                <input type="text" name="endereco" value="{{isset($lote) ? $lote->endereco : old('endereco')}}"
                    class="form-control @error('endereco') is-invalid @enderror" id="inputEndereco">
            </div>
            <div class="col-md-2">
                <label for="inputMetragemFrente" id="metragem_frente" class="form-label">Metragem Frente</label>
                <input type="text" name="metragem_frente"
                    value="{{isset($lote) ? $lote->metragem_frente : old('metragem_frente')}}"
                    class="form-control @error('metragem_frente') is-invalid @enderror" id="inputMetragemFrente">
            </div>
            <div class="col-md-2">
                <label for="inputMetragemFundo" id="metragem_fundo" class="form-label">Metragem Fundo</label>
                <input type="text" name="metragem_fundo"
                    value="{{isset($lote) ? $lote->metragem_fundo : old('metragem_fundo')}}"
                    class="form-control @error('metragem_fundo') is-invalid @enderror" id="inputMetragemFundo">
            </div>
            <div class="col-md-2">
                <label for="inputMetragemDireita" id="metragem_direita" class="form-label">Metragem Direita</label>
                <input type="text" name="metragem_direita"
                    value="{{isset($lote) ? $lote->metragem_direita : old('metragem_direita')}}"
                    class="form-control @error('metragem_direita') is-invalid @enderror" id="inputMetragemDireita">
            </div>
            <div class="col-md-2">
                <label for="inputMetragemEsquerda" id="metragem_esquerda" class="form-label">Metragem Esquerda</label>
                <input type="text" name="metragem_esquerda"
                    value="{{isset($lote) ? $lote->metragem_esquerda : old('metragem_esquerda')}}"
                    class="form-control @error('metragem_esquerda') is-invalid @enderror" id="inputMetragemEsquerda">
            </div>
            <div class="col-md-2">
                <label for="inputMetragemEsquina" id="metragem_esquina" class="form-label">Metragem Esquina</label>
                <input type="text" name="metragem_esquina"
                    value="{{isset($lote) ? $lote->metragem_esquina : old('metragem_esquina')}}"
                    class="form-control @error('metragem_esquina') is-invalid @enderror" id="inputMetragemEsquina">
            </div>
            <div class="col-md-3">
                <label for="inputConfrontacaoFrente" id="confrontacao_frente" class="form-label">Confrontação
                    Frente</label>
                <input type="text" name="confrontacao_frente"
                    value="{{isset($lote) ? $lote->confrontacao_frente : old('confrontacao_frente')}}"
                    class="form-control @error('confrontacao_frente') is-invalid @enderror"
                    id="inputConfrontacaoFrente">
            </div>
            <div class="col-md-3">
                <label for="inputConfrontacaoFundo" id="confrontacao_frente" class="form-label">Confrontação
                    Fundo</label>
                <input type="text" name="confrontacao_frente"
                    value="{{isset($lote) ? $lote->confrontacao_frente : old('confrontacao_frente')}}"
                    class="form-control @error('confrontacao_frente') is-invalid @enderror" id="inputConfrontacaoFundo">
            </div>
            <div class="col-md-3">
                <label for="inputConfrontacaoDireita" id="confrontacao_direita" class="form-label">Confrontação
                    Direita</label>
                <input type="text" name="confrontacao_direita"
                    value="{{isset($lote) ? $lote->confrontacao_direita : old('confrontacao_direita')}}"
                    class="form-control @error('confrontacao_direita') is-invalid @enderror"
                    id="inputConfrontacaoDireita">
            </div>
            <div class="col-md-3">
                <label for="inputEsquerda" id="confrontacao_esquerda" class="form-label">Confrontação Esquerda</label>
                <input type="text" name="confrontacao_esquerda"
                    value="{{isset($lote) ? $lote->confrontacao_esquerda : old('confrontacao_esquerda')}}"
                    class="form-control @error('confrontacao_esquerda') is-invalid @enderror" id="inputEsquerda">
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