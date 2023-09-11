<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SmartIPTU</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter" rel="stylesheet">


    <!-- Styles -->
    <link href="{{ asset('css/welcome.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">

</head>

<body class="">
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="{{asset("storage/SmartIPTU.png")}}" width="60px" />
            </a>
            <div class="collapse navbar-collapse d-lg-flex justify-content-end" id="navbarText">
                <span class="navbar-text">
                    @if (Route::has('login'))
                    <div class="">
                        @auth
                        <a href="{{ url('/dashboard') }}" class="">Dashboard</a>
                        @else
                        <a href="{{ route('login') }}" class="">Entrar</a>

                        @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="">Registrar</a>
                        @endif
                        @endauth
                    </div>
                    @endif
                </span>
            </div>
        </div>
    </nav>

    <main>
        <section class="principal"> 
            <div class="container text-center ">
                <div class="row align-items-center">
                    <div class="col">
                        <img src="{{asset("storage/SmartIPTU.png")}}" width="200px" />
                    </div>
                    <div class="col">
                        <h1>Sistema de gestão IPTU e Financeiro</h1>
                        <p>Simplifique a Gestão Financeira e de IPTU com o nosso sistema SmartIPTU, descomplicado e eficaz para Imobiliárias e Loteadoras</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous">
    </script>
</body>

</html>