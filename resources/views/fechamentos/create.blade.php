@extends('layouts.app')

@section('title', 'Fechamento Diário')

@push('vite')
    @vite(['resources/js/fechamento-page.js'])
@endpush

@section('content')
<div
    x-data="fechamentoForm({
        data: @js($data),
        turnoId: @js($turnoIdInicial),
        routes: {
            preview: @js(route('fechamentos.preview')),
            atualizar: @js(route('fechamentos.atualizar')),
        },
    })"
    class="space-y-6"
>
    <x-fechamento.cabecalho />
    <x-fechamento.filtros :turnos="$turnos" />

    <div x-show="resultado" x-cloak x-transition class="space-y-6">
        <x-fechamento.concentrador />
        <x-fechamento.pagamentos />
        <x-fechamento.frentistas />
        <x-fechamento.resumo />
        <x-fechamento.graficos />
        <x-fechamento.confirmar />
    </div>
</div>
@endsection
