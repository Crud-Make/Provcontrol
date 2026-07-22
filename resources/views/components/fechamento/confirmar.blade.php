<div class="bg-slate-800/80 border border-slate-700 rounded-xl p-6">
    <form action="{{ route('fechamentos.store') }}" method="POST" @submit="antesConfirmar($event)">
        @csrf
        <input type="hidden" name="data" :value="data">
        <input type="hidden" name="turno_id" :value="turnoId">

        <button type="submit"
                class="w-full bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-3 rounded-lg text-lg font-bold">
            Confirmar Fechamento
        </button>
    </form>
</div>
