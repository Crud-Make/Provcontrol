@extends('layouts.app')

@section('title', 'Fechamentos')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-slate-100">Fechamentos</h2>
    <a href="{{ route('fechamentos.create') }}"
       class="bg-sky-600 hover:bg-sky-500 text-white px-4 py-2 rounded-lg font-medium">
        + Novo Fechamento
    </a>
</div>

<div class="bg-slate-800/80 border border-slate-700 rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-900/80 text-slate-400">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Data</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Turno</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Concentrador</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Recebido</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Diferença</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($fechamentos as $f)
            <tr class="border-t border-slate-700 hover:bg-slate-900/40">
                <td class="px-6 py-4 text-slate-200">{{ $f->data->format('d/m/Y') }}</td>
                <td class="px-6 py-4 text-slate-300">{{ $f->turno->nome ?? '-' }}</td>
                <td class="px-6 py-4 text-sky-400">R$ {{ formatMoney($f->total_vendas_bombas) }}</td>
                <td class="px-6 py-4 text-emerald-400">R$ {{ formatMoney($f->total_recebido) }}</td>
                <td class="px-6 py-4 font-bold {{ $f->isConferido() ? 'text-emerald-400' : 'text-rose-400' }}">
                    R$ {{ formatMoney($f->diferenca) }}
                </td>
                <td class="px-6 py-4">
                    @if($f->isFechado())
                        <span class="px-2 py-1 bg-emerald-950 text-emerald-300 text-xs rounded border border-emerald-800">Fechado</span>
                    @else
                        <span class="px-2 py-1 bg-amber-950 text-amber-300 text-xs rounded border border-amber-800">{{ $f->statusLabel() }}</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <a href="{{ route('fechamentos.show', $f) }}" class="text-sky-400 hover:text-sky-300">Ver</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-slate-500">Nenhum fechamento encontrado.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $fechamentos->links() }}
</div>
@endsection
