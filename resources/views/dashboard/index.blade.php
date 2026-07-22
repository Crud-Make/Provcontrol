@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-100">Dashboard</h2>
        <p class="text-sm text-slate-400">{{ $posto->nome }} — visão geral do posto ativo</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-slate-800/80 border border-slate-700 rounded-xl p-6">
            <p class="text-sm text-slate-400">Posto ativo</p>
            <p class="text-xl font-bold text-sky-400">{{ $posto->nome }}</p>
        </div>
        <div class="bg-slate-800/80 border border-slate-700 rounded-xl p-6">
            <p class="text-sm text-slate-400">Ação rápida</p>
            <a href="{{ route('fechamentos.create') }}"
               class="inline-block mt-2 bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg text-sm font-medium">
                Novo fechamento
            </a>
        </div>
    </div>

    <div class="bg-slate-800/80 border border-slate-700 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-700 flex justify-between items-center">
            <h3 class="font-bold text-slate-100">Últimos fechamentos</h3>
            <a href="{{ route('fechamentos.index') }}" class="text-sm text-sky-400 hover:text-sky-300">Ver todos</a>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-900/80 text-slate-400">
                <tr>
                    <th class="px-6 py-3 text-left">Data</th>
                    <th class="px-6 py-3 text-left">Turno</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-right">Diferença</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ultimosFechamentos as $fechamento)
                    <tr class="border-t border-slate-700 hover:bg-slate-900/40">
                        <td class="px-6 py-4 text-slate-200">{{ $fechamento->data->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-slate-300">{{ $fechamento->turno->nome ?? '-' }}</td>
                        <td class="px-6 py-4 text-slate-300">{{ $fechamento->statusLabel() }}</td>
                        <td class="px-6 py-4 text-right font-bold {{ $fechamento->isConferido() ? 'text-emerald-400' : 'text-rose-400' }}">
                            R$ {{ formatMoney($fechamento->diferenca) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate-500">Nenhum fechamento ainda.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
