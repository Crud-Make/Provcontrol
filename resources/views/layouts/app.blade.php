<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ProvControl')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('vite')
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">
    <nav class="bg-slate-900 border-b border-slate-700 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="{{ route('dashboard') }}" class="text-xl font-bold text-sky-400">ProvControl</a>
            <div class="space-x-4 text-sm">
                <a href="{{ route('fechamentos.index') }}" class="text-slate-300 hover:text-sky-400">Fechamentos</a>
                <a href="{{ route('dashboard') }}" class="text-slate-300 hover:text-sky-400">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-slate-300 hover:text-rose-400">Sair</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container mx-auto p-4">
        @if(session('success'))
            <div class="bg-emerald-950 border-l-4 border-emerald-500 text-emerald-300 p-4 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-950 border-l-4 border-red-500 text-red-300 p-4 mb-4 rounded">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
