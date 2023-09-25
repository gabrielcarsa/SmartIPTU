@extends('layouts/app')

@section('conteudo')

<h2>
    @if (isset($data['prescricao']))
    Alterar Prescrição
    @else
    Nova Prescrição
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
        @if (isset($data['prescricao']))
        <p>
            Cadastrado por <strong>{{$data['cadastrado_por_user']->name}}</strong> em
            {{ \Carbon\Carbon::parse($data['prescricao']->data_cadastro)->format('d/m/Y') }}
        </p>
        @if (isset($data['alterado_por_user']))
        <p>
            Última alteração feita por <strong>{{$data['alterado_por_user']->name}}</strong> em
            {{ \Carbon\Carbon::parse($data['prescricao']->data_alteracao)->format('d/m/Y') }}
        </p>
        @endif
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#exampleModal">
            Excluir prescrição
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Excluir {{$data['prescricao']->nome}}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Deseja mesmo excluir essa prescrição? </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não</button>
                        <a href="../excluir/{{$data['prescricao']->id}}" class="btn btn-danger">Sim, excluir</a>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        @endif

        <form class="row g-3"
            action="{{ isset($data['prescricao']) ? '/prescricao/alterar/' . $data['prescricao']->id . '/' . Auth::user()->id : '/prescricao/cadastrar/' . Auth::user()->id . '/' . $lote_id }}"
            method="post" autocomplete="off">
            @csrf
            <div class="col-md-3">
                <label for="inputProcesso" id="processo" class="form-label">Processo*</label>
                <input type="text" name="processo"
                    value="{{isset($data['prescricao']) ? $data['prescricao']->processo : old('processo')}}"
                    class="form-control @error('processo') is-invalid @enderror" id="inputProcesso">
            </div>
            <div class="col-md-3">
                <label for="inputEntradaPedido" id="entrada_pedido" class="form-label">Entrada Pedido*</label>
                <input type="date" name="entrada_pedido"
                    value="{{isset($data['prescricao']) ? $data['prescricao']->entrada_pedido : old('entrada_pedido')}}"
                    class="form-control @error('entrada_pedido') is-invalid @enderror" id="inputEntradaPedido">
            </div>

            <div class="col-md-3">
                <label for="inputAno" id="anos_referencia" class="form-label">Ano referência*</label>
                <input type="text" name="anos_referencia" value="{{isset($data['prescricao']) ? $data['prescricao']->anos_referencia : old('anos_referencia')}}"
                    class="form-control @error('anos_referencia') is-invalid @enderror" id="inputAno" placeholder="2014, 2015...">
            </div>

            <div class="col-md-3">
                <label for="inputObservacao" id="observacao" class="form-label">Observação</label>
                <textarea name="observacao" id="inputObservacao" rows="2"
                    class="form-control @error('observacao') is-invalid @enderror">{{isset($data['prescricao']) ? $data['prescricao']->observacao : old('observacao')}}</textarea>
            </div>

            <div class="col-12">
                <button type="submit" class="btn-submit">
                    @if (isset($data['prescricao']))
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>


<script>
    $(document).ready(function() {
        $('#inputAno').mask('0000, 0000, 0000', {reverse: true});
    });
</script>
