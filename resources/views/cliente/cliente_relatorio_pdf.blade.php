<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <title>Relatório Clientes</title>

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">

    <style>
    /* Estilo básico para tabelas */
    .table {
        width: 100%;
        margin-bottom: 1rem;
        color: #212529;
    }

    /* Estilo para cabeçalho de tabela */
    .table th {
        padding: 0.75rem;
        vertical-align: top;
        border-top: 1px solid #dee2e6;
    }

    /* Estilo para células de tabela */
    .table td {
        padding: 0.75rem;
        vertical-align: top;
        border-top: 1px solid #dee2e6;
    }

    /* Estilo para tabelas listradas (alternância de cores) */
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.05);
    }

    /* Estilo para tabelas com bordas */
    .table-bordered {
        border: 1px solid #dee2e6;
    }

    /* Estilo para tabelas responsivas em dispositivos móveis */
    .table-responsive {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Estilo para tabelas pequenas */
    .table-sm th,
    .table-sm td {
        padding: 0.3rem;
    }
    </style>
</head>

<body>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nome/Razão Social</th>
                    <th scope="col">CPF/CNPJ</th>
                    <th scope="col">Telefone</th>
                    <th scope="col">Email</th>
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($clientes))
                @foreach ($clientes as $cliente)
                <tr>
                    <th scope="row">{{$cliente->id}}</th>
                    @if($cliente->tipo_cadastro == 0)
                    <td>{{$cliente->nome}}</td>
                    <td>{{$cliente->cpf}}</td>
                    @else
                    <td>{{$cliente->razao_social}}</td>
                    <td>{{$cliente->cnpj}}</td>
                    @endif
                    <td>{{$cliente->telefone1}}</td>
                    <td>{{$cliente->email}}</td>
                    <td><a href="editar/{{$cliente->id}}">Ver/Editar</a></td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous">
    </script>
</body>

</html>