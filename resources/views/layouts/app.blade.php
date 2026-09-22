<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ Auth::check() ? route('home') : url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            @if(auth()->user()->rol === 'cliente')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('consolidados.*') ? 'active' : '' }}" href="{{ route('consolidados.index') }}">Consolidados</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('entradas.*') ? 'active' : '' }}" href="{{ route('entradas.index') }}">Entradas</a>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Inicio</a>
                                </li>

                                <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->routeIs('consolidados.*', 'entradas.*', 'mediciones.*', 'observaciones.*') ? 'active' : '' }}" href="#" id="operacionDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Operación
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="operacionDropdown">
                                    <li><a class="dropdown-item" href="{{ route('consolidados.index') }}">Consolidados</a></li>
                                    <li><a class="dropdown-item" href="{{ route('entradas.index') }}">Entradas</a></li>
                                    <li><a class="dropdown-item" href="{{ route('mediciones.index') }}">Mediciones</a></li>
                                    <li><a class="dropdown-item" href="{{ route('observaciones.index') }}">Observaciones</a></li>
                                </ul>
                                </li>

                                <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->routeIs('clientes.*', 'conductores.*', 'vehiculos.*', 'transportadoras.*', 'bodegas.*', 'reempacadores.*', 'codigosr.*', 'remitentes.*', 'destinatarios.*', 'oficinas.*') ? 'active' : '' }}" href="#" id="catalogosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Catálogos
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="catalogosDropdown">
                                    <li><a class="dropdown-item" href="{{ route('clientes.index') }}">Clientes</a></li>
                                    <li><a class="dropdown-item" href="{{ route('conductores.index') }}">Conductores</a></li>
                                    <li><a class="dropdown-item" href="{{ route('vehiculos.index') }}">Vehículos</a></li>
                                    <li><a class="dropdown-item" href="{{ route('transportadoras.index') }}">Transportadoras</a></li>
                                    <li><a class="dropdown-item" href="{{ route('bodegas.index') }}">Bodegas</a></li>
                                    <li><a class="dropdown-item" href="{{ route('reempacadores.index') }}">Reempacadores</a></li>
                                    <li><a class="dropdown-item" href="{{ route('codigosr.index') }}">Códigos R</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('remitentes.index') }}">Remitentes</a></li>
                                    <li><a class="dropdown-item" href="{{ route('destinatarios.index') }}">Destinatarios</a></li>
                                    <li><a class="dropdown-item" href="{{ route('oficinas.index') }}">Oficinas</a></li>
                                    @if(auth()->user()->esAdministrador())
                                        <li><a class="dropdown-item" href="{{ route('coberturas.index') }}">Coberturas</a></li>
                                    @endif
                                    @if(auth()->user()->rol === 'superadministrador')
                                        <li><a class="dropdown-item" href="{{ route('usuarios.index') }}">Usuarios</a></li>
                                    @endif
                                </ul>
                                </li>
                            @endif
                        @endauth

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
