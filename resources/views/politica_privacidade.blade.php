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
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText"
                aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarText">
                <ul class="navbar-nav ml-auto">
                    @if (Route::has('login'))
                    @auth
                    <li class="nav-item">
                        <a href="{{ url('/dashboard') }}" class="nav-link">Dashboard</a>
                    </li>
                    @else
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="nav-link">Entrar</a>
                    </li>
                    <!--@if (Route::has('register'))
                        <li class="nav-item">
                            <a href="{{ route('register') }}" class="nav-link">Registrar</a>
                        </li>
                        @endif-->
                    @endauth
                    @endif
                </ul>
            </div>
        </div>
    </nav>


    <main>
        <section class="principal">
            <div class="container">

                <h1 class="fw-bold fs-1">Política de Privacidade do aplicativo SmartIPTU</h1>
                <p class="fs-5">Esta Política de Privacidade descreve como as informações pessoais são coletadas, usadas
                    e
                    compartilhadas quando você utiliza o aplicativo do SmartIPTU.</p>
                <ul>
                    <li>
                        <h2 class="fs-3 fw-semibold">Informações que Coletamos</h2>
                        <p class="fs-5">Ao usar o Aplicativo, podemos coletar informações sobre você, como seu email,
                            senha e seu nome que você fornece ao cadastrar seu usuário no sistema WEB SmartIPTU.</p>
                    </li>
                    <li>
                        <h2 class="fs-3 fw-semibold">Como Usamos as Informações</h2>
                        <p class="fs-5">As informações coletadas são usadas para controle de usuários e funcionalidades
                            do Aplicativo juntamente com o sistema WEB SmartIPTU.</p>
                    </li>
                    <li>
                        <h2 class="fs-3 fw-semibold">Compartilhamento de Informações</h2>
                        <p class="fs-5">Não compartilhamos suas informações pessoais com terceiros, exceto para cumprir
                            com as leis e regulamentações aplicáveis, proteger a segurança do Aplicativo ou investigar
                            qualquer violação dos nossos Termos de Serviço.</p>
                    </li>
                    <li>
                        <h2 class="fs-3 fw-semibold">Segurança</h2>
                        <p class="fs-5">Valorizamos a segurança das suas informações pessoais e implementamos medidas
                            para proteger essas informações contra acesso não autorizado ou divulgação.</p>
                    </li>
                    <li>
                        <h2 class="fs-3 fw-semibold">Alterações na Política de Privacidade</h2>
                        <p class="fs-5">Podemos atualizar esta Política de Privacidade de tempos em tempos. Recomendamos
                            que você revise periodicamente esta página para ficar ciente de quaisquer alterações.</p>
                    </li>
                    <li>
                        <h2 class="fs-3 fw-semibold">Seus Direitos</h2>
                        <p class="fs-5">Você tem o direito de acessar as informações pessoais que mantemos sobre você e
                            de solicitar que suas informações pessoais sejam corrigidas, atualizadas ou excluídas. Se
                            desejar exercer esses direitos, entre em contato conosco através dos meios fornecidos no
                            final desta Política de Privacidade.</p>
                    </li>
                    <li>
                        <h2 class="fs-3 fw-semibold">Contato</h2>
                        <p class="fs-5">Se você tiver alguma dúvida ou preocupação sobre nossa Política de Privacidade,
                            entre em contato conosco pelo email: <a href="">contato@ghctecnologia.com</a></p>
                    </li>
                </ul>

                <p class="fs-4">Obrigado!</p>

            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous">
    </script>
</body>

</html>