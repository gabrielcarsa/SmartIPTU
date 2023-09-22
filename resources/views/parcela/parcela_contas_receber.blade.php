@extends('layouts/app')

@section('conteudo')

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<h2>Contas a receber</h2>
<a class="btn btn-primary btn-add" href="" style="margin-bottom: 20px">
    <span class="material-symbols-outlined">
        add
    </span>Novo</a>

@endsection