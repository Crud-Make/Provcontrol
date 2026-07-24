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

    {{-- x-if (não x-show): a grade só é montada quando `resultado` existe,
         evitando que os x-for avaliem `resultado.leituras` com resultado null. --}}
    <template x-if="resultado">
        <div x-cloak x-transition class="space-y-6">
            <x-fechamento.concentrador />
            <x-fechamento.pagamentos />
            <x-fechamento.frentistas />
            <x-fechamento.resumo />
            <x-fechamento.graficos />
            <x-fechamento.confirmar />
        </div>
    </template>
</div>
@endsection
