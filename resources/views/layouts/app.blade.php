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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;900&display=swap" rel="stylesheet">
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
        <div class="offcanvas-body-content" style="max-height: 80vh; overflow-y: auto;">
            <ul class="list-offcanvas">
                <li><a href="/empreendimento">
                        <span class="material-symbols-outlined">pie_chart</span>Gestão de Lotes/Inscrições
                    </a>
                </li>
                <li><a href="/cliente"><span class="material-symbols-outlined">
                            person_add
                        </span>Cadastro de clientes</a>
                </li>
                <li><a href="/usuario"><span class="material-symbols-outlined">
                            group
                        </span>Cadastro e controle de usuários</a>
                </li>
                <li><a href="/cobranca"><span class="material-symbols-outlined">
                            call_quality
                        </span>Cobrança</a>
                </li>
                <li>
                    <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="material-symbols-outlined">
                            universal_currency_alt
                        </span>Financeiro
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('calendario') }}"><span
                                    class="material-symbols-outlined">
                                    calendar_month
                                </span>Calendário Financeiro</a></li>
                        <li><a class="dropdown-item" href="{{ route('contas_receber') }}"><span
                                    class="material-symbols-outlined">
                                    attach_money
                                </span>Contas a receber</a></li>
                        <li><a class="dropdown-item" href="{{ route('contas_pagar') }}"><span
                                    class="material-symbols-outlined">
                                    money_off
                                </span>Contas a pagar</a></li>
                        <li><a class="dropdown-item" href="{{ route('movimentacao_financeira') }}"><span
                                    class="material-symbols-outlined">
                                    currency_exchange
                                </span>Movimentação Financeira</a></li>
                    </ul>
                </li>

                <li>
                    <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="material-symbols-outlined">
                            settings
                        </span>Configurações
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('titular_conta') }}">
                                <span class="material-symbols-outlined">
                                    person_pin
                                </span>Central de Contas</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('tipo_debito') }}">
                                <span class="material-symbols-outlined">
                                    request_quote
                                </span>Tipo de Débitos</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('descricao_debito') }}">
                                <span class="material-symbols-outlined">
                                    description
                                </span>Descrição de Débitos</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('categoria_pagar') }}">
                                <span class="material-symbols-outlined">
                                    attach_money
                                </span>Categoria a Pagar</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('categoria_receber') }}">
                                <span class="material-symbols-outlined">
                                    money_off
                                </span>Categoria a Receber</a>
                        </li>

                    </ul>
                </li>

            </ul>
        </div>
    </div>

    <!-- Page Content -->
    @if(Route::is('profile.show'))
    {{$slot}}
    @endif

    <main class="content">

        </script>
        @yield('conteudo')
    </main>

    @stack('modals')
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous">
    </script>

    @livewireScripts


</body>

</html>