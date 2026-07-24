@props(['turnos'])

<div class="bg-slate-800/80 border border-slate-700 rounded-xl p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Data</label>
            <input type="date" x-model="data"
                   class="w-full bg-slate-950 border border-slate-600 rounded-lg px-3 py-2 text-slate-100 focus:border-brand-400 focus:outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Turno</label>
            <select x-model="turnoId"
                    class="w-full bg-slate-950 border border-slate-600 rounded-lg px-3 py-2 text-slate-100 focus:border-brand-400 focus:outline-none">
                <option value="">Selecione...</option>
                @foreach($turnos as $turno)
                    <option value="{{ $turno->id }}">{{ $turno->nome }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="mt-4 flex flex-wrap gap-3">
        <button type="button" @click="calcular()" :disabled="carregando"
                class="bg-brand-500 hover:bg-brand-400 disabled:opacity-50 text-white px-5 py-2 rounded-lg font-medium">
            <span x-text="carregando ? 'Carregando...' : 'Calcular Fechamento'"></span>
        </button>
        <button type="button" x-show="resultado" x-cloak @click="salvarAlteracoes()" :disabled="salvando"
                class="bg-accent-500 hover:bg-accent-600 disabled:opacity-50 text-white px-5 py-2 rounded-lg font-medium">
            <span x-text="salvando ? 'Salvando...' : 'Salvar alterações'"></span>
        </button>
    </div>

    <div x-show="erro" x-cloak class="mt-4 bg-red-950 border-l-4 border-red-500 text-red-300 p-4 rounded">
        <p x-text="erro"></p>
    </div>
    <div x-show="sucesso" x-cloak class="mt-4 bg-emerald-950 border-l-4 border-emerald-500 text-emerald-300 p-4 rounded">
        <p x-text="sucesso"></p>
    </div>
</div>
