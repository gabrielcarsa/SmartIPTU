<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <title>Relatório Mensagem</title>
    <style>
       
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
    </body>
</html>