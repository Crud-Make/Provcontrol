<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar — ProvControl</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 flex items-center justify-center p-4">
    <main class="w-full max-w-md rounded-xl border border-slate-700 bg-slate-900 p-6">
        <h1 class="text-2xl font-bold text-sky-400">ProvControl</h1>
        <p class="mt-1 text-sm text-slate-400">Entre para acessar o posto.</p>

        <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-slate-300">E-mail</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2"
                >
                @error('email')
                    <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-300">Senha</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2"
                >
                @error('password')
                    <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-300">
                <input name="remember" type="checkbox" value="1" class="rounded border-slate-600">
                Manter conectado
            </label>

            <button type="submit" class="w-full rounded-lg bg-sky-600 px-4 py-2 font-semibold hover:bg-sky-500">
                Entrar
            </button>
        </form>
    </main>
</body>
</html>
