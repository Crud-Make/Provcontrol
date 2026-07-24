<div class="bg-slate-800/80 border border-slate-700 rounded-xl p-6">
    <form action="{{ route('fechamentos.store') }}" method="POST" @submit="antesConfirmar($event)">
        @csrf
        <input type="hidden" name="data" :value="data">
        <input type="hidden" name="turno_id" :value="turnoId">

        <button type="submit"
                class="w-full bg-brand-500 hover:bg-brand-400 text-white px-6 py-3 rounded-lg text-lg font-bold shadow-sm">
            Confirmar Fechamento
        </button>
    </form>
</div>
