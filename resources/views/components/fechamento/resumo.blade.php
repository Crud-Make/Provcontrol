<div class="bg-slate-800/80 border rounded-xl p-6 border-l-4"
     :class="Math.abs(resultado.diferenca) < 0.01 ? 'border-l-emerald-500 border-slate-700' : 'border-l-rose-500 border-slate-700'">
    <h3 class="text-lg font-bold text-slate-100 mb-4">Fechamento do Caixa</h3>
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-slate-900/50 rounded-lg p-4">
            <p class="text-sm text-slate-400">Total Concentrador</p>
            <p class="text-xl font-bold text-sky-400" x-text="formatMoney(resultado.total_concentrador)"></p>
        </div>
        <div class="bg-slate-900/50 rounded-lg p-4">
            <p class="text-sm text-slate-400">Total Informado</p>
            <p class="text-xl font-bold text-slate-200" x-text="formatMoney(resultado.total_informado_frentistas)"></p>
        </div>
        <div class="bg-slate-900/50 rounded-lg p-4">
            <p class="text-sm text-slate-400">Total Conferido</p>
            <p class="text-xl font-bold text-emerald-400" x-text="formatMoney(resultado.total_frentistas)"></p>
        </div>
        <div class="bg-slate-900/50 rounded-lg p-4">
            <p class="text-sm text-slate-400">Diferença</p>
            <p class="text-xl font-bold"
               :class="Math.abs(resultado.diferenca) < 0.01 ? 'text-emerald-400' : 'text-rose-400'"
               x-text="formatMoney(resultado.diferenca)"></p>
            <p x-show="Math.abs(resultado.diferenca) < 0.01" class="text-sm text-emerald-400">Fechou perfeito</p>
            <p x-show="resultado.diferenca > 0.01" class="text-sm text-emerald-400">Sobra no caixa</p>
            <p x-show="resultado.diferenca < -0.01" class="text-sm text-rose-400">Falta no caixa</p>
        </div>
        <div class="bg-slate-900/50 rounded-lg p-4">
            <p class="text-sm text-slate-400">Taxas (cartões/apps)</p>
            <p class="text-xl font-bold text-rose-400" x-text="formatMoney(resultado.total_taxas)"></p>
        </div>
    </div>
</div>
