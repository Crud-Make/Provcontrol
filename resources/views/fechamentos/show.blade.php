@extends('layouts.app')

@section('title', 'Detalhes do Fechamento')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-slate-100">Detalhes do Fechamento</h2>
    <a href="{{ route('fechamentos.index') }}" class="text-slate-400 hover:text-sky-400 text-sm">← Voltar</a>
</div>

<div class="bg-slate-800/80 border border-slate-700 rounded-xl p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="border-b border-slate-700 py-2">
            <span class="text-slate-400">Data:</span>
            <span class="font-medium text-slate-100 ml-2">{{ $fechamento->data->format('d/m/Y') }}</span>
        </div>
        <div class="border-b border-slate-700 py-2">
            <span class="text-slate-400">Turno:</span>
            <span class="font-medium text-slate-100 ml-2">{{ $fechamento->turno->nome ?? '-' }}</span>
        </div>
        <div class="border-b border-slate-700 py-2">
            <span class="text-slate-400">Status:</span>
            <span class="font-medium ml-2 {{ $fechamento->isFechado() ? 'text-emerald-400' : 'text-amber-400' }}">
                {{ $fechamento->statusLabel() }}
            </span>
        </div>
        <div class="border-b border-slate-700 py-2">
            <span class="text-slate-400">Total Concentrador:</span>
            <span class="font-medium text-sky-400 ml-2">R$ {{ formatMoney($fechamento->total_vendas_bombas) }}</span>
        </div>
        <div class="border-b border-slate-700 py-2">
            <span class="text-slate-400">Total Recebido:</span>
            <span class="font-medium text-emerald-400 ml-2">R$ {{ formatMoney($fechamento->total_recebido) }}</span>
        </div>
        <div class="border-b border-slate-700 py-2">
            <span class="text-slate-400">Diferença:</span>
            <span class="font-bold ml-2 {{ $fechamento->isConferido() ? 'text-emerald-400' : 'text-rose-400' }}">
                R$ {{ formatMoney($fechamento->diferenca) }}
            </span>
        </div>
        @if($fechamento->observacoes)
        <div class="md:col-span-2 border-b border-slate-700 py-2">
            <span class="text-slate-400">Observações:</span>
            <span class="font-medium text-slate-200 ml-2">{{ $fechamento->observacoes }}</span>
        </div>
        @endif
        <div class="border-b border-slate-700 py-2">
            <span class="text-slate-400">Fechado por:</span>
            <span class="font-medium text-slate-100 ml-2">{{ $fechamento->fechadoPor->name ?? 'Sistema' }}</span>
        </div>
        <div class="border-b border-slate-700 py-2">
            <span class="text-slate-400">Fechado em:</span>
            <span class="font-medium text-slate-100 ml-2">{{ $fechamento->fechado_em ? $fechamento->fechado_em->format('d/m/Y H:i') : '-' }}</span>
        </div>
    </div>
</div>
@endsection
