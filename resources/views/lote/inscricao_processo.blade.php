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
                <textarea name="" id=""></textarea>
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

</div>

@endsection