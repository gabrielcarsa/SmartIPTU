@extends('layouts/app')

@section('conteudo')

<h2>
    @if (isset($cliente))
    Alterar Cliente
    @else
    Adicionar Cliente
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
        @if (isset($cliente))
        <p>
            Cadastrado por <strong>{{$cadastrado_por_user->name}}</strong> em
            {{ \Carbon\Carbon::parse($cliente->data_cadastro)->format('d/m/Y') }}
        </p>
        @if (isset($alterado_por_user))
        <p>
            Última alteração feita por <strong>{{$alterado_por_user->name}}</strong> em
            {{ \Carbon\Carbon::parse($cliente->data_alteracao)->format('d/m/Y') }}
        </p>
        @endif
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#exampleModal">
            Excluir cliente
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Excluir {{$cliente->nome}}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Deseja mesmo excluir esse cliente? </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não</button>
                        <a href="../excluir/{{$cliente->id}}" class="btn btn-danger">Sim, excluir</a>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        @endif

        <form class="row g-3"
            action="{{ isset($cliente) ? '/cliente/alterar/' . $cliente->id . '/' . Auth::user()->id : '/cliente/cadastrar/' . Auth::user()->id }}"
            method="post" autocomplete="off">
            @csrf
            <div class="col-md-4" id="campoNome">
                <label for="inputNome" id="nome" class="form-label">Nome*</label>
                <input type="text" name="nome" value="{{isset($cliente) ? $cliente->nome : old('nome')}}"
                    class="form-control @error('nome') is-invalid @enderror" id="inputNome">
            </div>
            <div class="col-md-4" id="campoRazaoSocial" style="display: none;">
                <label for="inputRazaoSocial" id="razao_social" class="form-label">Razão Social*</label>
                <input type="text" name="razao_social"
                    value="{{isset($cliente) ? $cliente->razao_social : old('razao_social')}}"
                    class="form-control @error('razao_social') is-invalid @enderror" id="inputRazaoSocial">
            </div>
            <div class="col-md-3" id="campoCpf">
                <label for="inputCpf" id="cpf" class="form-label">CPF*</label>
                <input type="text" name="cpf" value="{{ isset($cliente) ? $cliente->cpf : old('cpf') }}"
                    class="form-control @error('cpf') is-invalid @enderror" id="inputCpf">

            </div>
            <div class="col-md-3" id="campoCnpj" style="display: none;">
                <label for="inputCnpj" id="cnpj" class="form-label">CNPJ*</label>
                <input type="text" name="cnpj" value="{{ isset($cliente) ? $cliente->cnpj : old('cnpj') }}"
                    class="form-control @error('cnpj') is-invalid @enderror" id="inputCnpj">
            </div>
            <div class="col-md-2" id="campoInscricaoEstadual" style="display: none;">
                <label for="inputInscricao_estadual" class="form-label">Inscrição Estadual*</label>
                <input type="text" name="inscricao_estadual"
                    value="{{ isset($cliente) ? $cliente->inscricao_estadual : old('inscricao_estadual') }}"
                    class="form-control @error('inscricao_estadual') is-invalid @enderror" id="inputInscricao_estadual">
            </div>
            <div class="col-md-2" id="campoRg">
                <label for="inputRg" class="form-label">RG</label>
                <input type="text" name="rg" value="{{ isset($cliente) ? $cliente->rg : old('rg')}}"
                    class="form-control" id="inputRg">
            </div>

            @if (!isset($cliente))
            <div class="col-md-3">
                <label for="inputTipo" class="form-label">Tipo de cadastro*</label>
                <select id="inputTipo" name="tipo_cadastro" class="form-select" onchange="mostrarOcultarCampo()">
                    <option value="0" {{ old('tipo_cadastro') == 0 ? 'selected' : '' }}>Pessoa Física</option>
                    <option value="1" {{ old('tipo_cadastro') == 1 ? 'selected' : '' }}>Pessoa Jurídica</option>
                </select>

                <script>
                document.addEventListener("DOMContentLoaded", function() {
                    mostrarOcultarCampo();
                });

                function mostrarOcultarCampo() {
                    // Obtém o valor da opção selecionada
                    var opcao = document.getElementById("inputTipo").value;

                    // Obtém referências aos campos de texto que deseja mostrar/ocultar
                    var campoNome = document.getElementById("campoNome");
                    var campoRazaoSocial = document.getElementById("campoRazaoSocial");
                    var campoCpf = document.getElementById("campoCpf");
                    var campoCnpj = document.getElementById("campoCnpj");
                    var campoRg = document.getElementById("campoRg");
                    var campoInscricaoEstadual = document.getElementById("campoInscricaoEstadual");
                    var campoDataNascimento = document.getElementById("campoDataNascimento");
                    var campoProfissao = document.getElementById("campoProfissao");
                    var campoEstadoCivil = document.getElementById("campoEstadoCivil");


                    // Mostra ou oculta os campos com base na opção selecionada
                    if (opcao === "0") {
                        campoNome.style.display = "block";
                        campoCpf.style.display = "block";
                        campoRg.style.display = "block";
                        campoDataNascimento.style.display = "block";
                        campoProfissao.style.display = "block";
                        campoEstadoCivil.style.display = "block";
                        campoRazaoSocial.style.display = "none";
                        campoCnpj.style.display = "none";
                        campoInscricaoEstadual.style.display = "none";

                    } else if (opcao === "1") {
                        campoNome.style.display = "none";
                        campoCpf.style.display = "none";
                        campoRg.style.display = "none";
                        campoDataNascimento.style.display = "none";
                        campoProfissao.style.display = "none";
                        campoEstadoCivil.style.display = "none";
                        campoRazaoSocial.style.display = "block";
                        campoCnpj.style.display = "block";
                        campoInscricaoEstadual.style.display = "block";

                    } else {
                        // Lidar com outras opções, se necessário
                    }
                }
                </script>

            </div>
            @else
            <div class="col-md-3">
                <label for="inputTipo" class="form-label">Tipo de cadastro*</label>
                <input id="inputTipo" disabled name="tipo_cadastro" class="form-control"
                    value="{{$cliente->tipo_cadastro == 0 ? 'Pessoa Física' : 'Pessoa Jurídica'}}">
                <input type="hidden" id="inputTipoHidden" name="tipo_cadastro_hidden"
                    value="{{$cliente->tipo_cadastro}}">
            </div>
            <script>
            // Verifique o valor do campo hidden ao carregar a página
            document.addEventListener("DOMContentLoaded", function() {
                mostrarOcultarCampo(); // Chame a função para verificar e mostrar/ocultar campos
            });

            function mostrarOcultarCampo() {
                // Obtém o valor do campo hidden
                var tipoCadastroHidden = document.getElementById("inputTipoHidden").value;
                // Obtém referências aos campos de texto que deseja mostrar/ocultar
                var campoNome = document.getElementById("campoNome");
                var campoRazaoSocial = document.getElementById("campoRazaoSocial");
                var campoCpf = document.getElementById("campoCpf");
                var campoCnpj = document.getElementById("campoCnpj");
                var campoRg = document.getElementById("campoRg");
                var campoInscricaoEstadual = document.getElementById("campoInscricaoEstadual");
                var campoDataNascimento = document.getElementById("campoDataNascimento");
                var campoProfissao = document.getElementById("campoProfissao");
                var campoEstadoCivil = document.getElementById("campoEstadoCivil");

                // Mostra ou oculta os campos com base no valor do campo hidden
                if (tipoCadastroHidden === "0") {
                    campoNome.style.display = "block";
                    campoCpf.style.display = "block";
                    campoRg.style.display = "block";
                    campoDataNascimento.style.display = "block";
                    campoProfissao.style.display = "block";
                    campoEstadoCivil.style.display = "block";
                    campoRazaoSocial.style.display = "none";
                    campoCnpj.style.display = "none";
                    campoInscricaoEstadual.style.display = "none";
                } else if (tipoCadastroHidden === "1") {
                    campoNome.style.display = "none";
                    campoCpf.style.display = "none";
                    campoRg.style.display = "none";
                    campoDataNascimento.style.display = "none";
                    campoProfissao.style.display = "none";
                    campoEstadoCivil.style.display = "none";
                    campoRazaoSocial.style.display = "block";
                    campoCnpj.style.display = "block";
                    campoInscricaoEstadual.style.display = "block";
                } else {
                    // Lida com outras opções, se necessário
                }
            }
            </script>
            @endif

            <div class="col-md-3">
                <label for="inputRua" class="form-label">Rua*</label>
                <input type="text" name="rua_end" value="{{isset($cliente) ? $cliente->rua_end : old('rua_end') }}"
                    class="form-control @error('rua_end') is-invalid @enderror" id="inputRua" placeholder="">
            </div>
            <div class="col-md-3">
                <label for="inputBairro" class="form-label">Bairro*</label>
                <input type="text" name="bairro_end"
                    value="{{isset($cliente) ? $cliente->bairro_end : old('bairro_end') }}"
                    class="form-control @error('bairro_end') is-invalid @enderror" id="inputBairro">
            </div>
            <div class="col-md-4">
                <label for="inputCidade" class="form-label">Cidade*</label>
                <input type="text" name="cidade_end"
                    value="{{isset($cliente) ? $cliente->cidade_end : old('cidade_end') }}"
                    class="form-control @error('cidade_end') is-invalid @enderror" id="inputCidade">
            </div>
            <div class="col-md-2">
                <label for="inputEstado" class="form-label">Estado*</label>
                <input type="text" name="estado_end"
                    value="{{isset($cliente) ? $cliente->estado_end : old('estado_end') }}"
                    class="form-control @error('estado_end') is-invalid @enderror" id="inputEstado">
            </div>

            <div class="col-md-2">
                <label for="inputNumero" class="form-label">Número*</label>
                <input type="text" name="numero_end"
                    value="{{ isset($cliente) ? $cliente->numero_end : old('numero_end')  }}"
                    class="form-control @error('numero_end') is-invalid @enderror" id="inputNumero">
            </div>
            <div class="col-md-2">
                <label for="inputComplemento" class="form-label">Complemento</label>
                <input type="text" name="complemento_end"
                    value="{{ isset($cliente) ? $cliente->complemento_end : old('complemento_end') }}"
                    class="form-control" id="inputComplemento">
            </div>
            <div class="col-md-2">
                <label for="inputCep" class="form-label">CEP*</label>
                <input type="text" name="cep_end" value="{{isset($cliente) ? $cliente->cep_end : old('cep_end') }}"
                    class="form-control @error('cep_end') is-invalid @enderror" id="inputCep">
            </div>
            <div class="col-md-2" id="campoDataNascimento">
                <label for="inputDataNascimento" class="form-label">Data de nascimento</label>
                <input type="date" name="data_nascimento"
                    value="{{isset($cliente) ? $cliente->data_nascimento : old('data_nascimento')  }}"
                    class="form-control @error('email') is-invalid @enderror" id="inputDataNascimento">
            </div>
            <div class="col-md-4" id="campoProfissao">
                <label for="inputProfissao" class="form-label">Profissão</label>
                <input type="text" name="profissao"
                    value="{{isset($cliente) ? $cliente->profissao : old('profissao') }}" class="form-control"
                    id="inputProfissao">
            </div>
            <div class="col-md-3" id="campoEstadoCivil">
                <label for="inputEstadoCivil" class="form-label">Estado Civil</label>
                <input type="text" name="estado_civil"
                    value="{{isset($cliente) ? $cliente->estado_civil : old('estado_civil')  }}" class="form-control"
                    id="inputEstadoCivil">
            </div>
            <div class="col-md-3">
                <label for="inputEmail" class="form-label">Email*</label>
                <input type="email" name="email" value="{{isset($cliente) ? $cliente->email : old('email') }}"
                    class="form-control @error('email') is-invalid @enderror" id="inputEmail">
            </div>
            <div class="col-md-3">
                <label for="inputTelefone1" class="form-label">Telefone 1</label>
                <input type="text" name="telefone1" placeholder="(xx) xxxxx-xxxx"
                    value="{{isset($cliente) ? $cliente->telefone1 : old('telefone1')  }}" class="form-control"
                    id="inputTelefone1">
            </div>
            <div class="col-md-3">
                <label for="inputTelefone2" class="form-label">Telefone 2</label>
                <input type="text" name="telefone2" placeholder="(xx) xxxxx-xxxx"
                    value="{{isset($cliente) ? $cliente->telefone2 : old('telefone2')  }}" class="form-control"
                    id="inputTelefone2">
            </div>
            <div class="col-12">
                <button type="submit" class="btn-submit">
                    @if (isset($cliente))
                    Alterar
                    @else
                    Cadastrar
                    @endif
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Função para aplicar a máscara de CPF
document.getElementById('inputCpf').addEventListener('input', function(e) {
    let input = e.target;
    let value = input.value.replace(/\D/g, ''); // Remove todos os caracteres não numéricos
    let formattedValue = '';

    if (value.length > 0) {
        formattedValue = value.slice(0, 3);

        if (value.length > 3) {
            formattedValue += '.' + value.slice(3, 6);
        }

        if (value.length > 6) {
            formattedValue += '.' + value.slice(6, 9);
        }

        if (value.length > 9) {
            formattedValue += '-' + value.slice(9, 11);
        }
    }

    input.value = formattedValue;
});

// Função para aplicar a máscara de CNPJ
document.getElementById('inputCnpj').addEventListener('input', function(e) {
    let input = e.target;
    let value = input.value.replace(/\D/g, ''); // Remove todos os caracteres não numéricos
    let formattedValue = '';

    if (value.length > 0) {
        formattedValue = value.slice(0, 2);

        if (value.length > 2) {
            formattedValue += '.' + value.slice(2, 5);
        }

        if (value.length > 5) {
            formattedValue += '.' + value.slice(5, 8);
        }

        if (value.length > 8) {
            formattedValue += '/' + value.slice(8, 12);
        }

        if (value.length > 12) {
            formattedValue += '-' + value.slice(12, 14);
        }
    }

    input.value = formattedValue;
});

// Função para aplicar a máscara de telefone
function aplicarMascaraTelefone(inputId) {
    const input = document.getElementById(inputId);

    input.addEventListener('input', function(e) {
        let value = input.value.replace(/\D/g, ''); // Remove todos os caracteres não numéricos
        let formattedValue = '';

        if (value.length > 0) {
            formattedValue = '(' + value.slice(0, 2);

            if (value.length > 2) {
                formattedValue += ') ' + value.slice(2, 7);
            }

            if (value.length > 7) {
                formattedValue += '-' + value.slice(7, 11);
            }
        }

        input.value = formattedValue;
    });
}
// Aplicar a máscara para os campos de telefone 1 e telefone 2
aplicarMascaraTelefone('inputTelefone1');
aplicarMascaraTelefone('inputTelefone2');
</script>
@endsection