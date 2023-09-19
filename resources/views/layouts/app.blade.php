<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter" rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    <link href="{{ asset('css/master.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    @livewireStyles
</head>

<body class="">

    @livewire('navigation-menu')

    <div class="offcanvas offcanvas-start" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1"
        id="offcanvasScrolling" aria-labelledby="offcanvasScrollingLabel">
        <div class="offcanvas-header">
            <a href="/dashboard"><img src="{{asset("storage/SmartIPTU.png")}}" width="100px" /></a>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body-content">
            <ul class="list-offcanvas">
                <li><a href="/empreendimento">
                        <span class="material-symbols-outlined">pie_chart</span>Gestão de Lotes/Inscrições
                    </a></li>
                <li><a href="/cliente"><span class="material-symbols-outlined">
                            person_add
                        </span>Cadastro de clientes</a></li>
                <li><a href="/usuario"><span class="material-symbols-outlined">
                            group
                        </span>Cadastro e controle de usuários</a></li>
                <li>
                    <a data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false"
                        aria-controls="collapseExample">
                        <span class="material-symbols-outlined">
                            universal_currency_alt
                        </span>Financeiro
                    </a>
                </li>
                <div class="collapse" id="collapseExample">
                    <ul class="sublist-offcanvas">
                        <li><a href="">Contas a receber</a></li>
                        <li><a href="">Contas a pagar</a></li>
                    </ul>
                </div>

                <li><a href="/"><span class="material-symbols-outlined">
                            settings
                        </span>Configurações</a></li>

            </ul>
        </div>
    </div>

    <!-- Page Content -->
    @if(Route::is('profile.show'))
    {{$slot}}
    @endif

    <main class="content">
        @yield('conteudo')
    </main>

    @stack('modals')

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>

</body>

</html>