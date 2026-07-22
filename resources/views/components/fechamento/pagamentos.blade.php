<div class="bg-slate-800/80 border border-slate-700 rounded-xl p-6">
    <h3 class="text-lg font-bold text-slate-100 mb-4">
        Pagamentos Eletrônicos
        <span class="text-xs font-normal text-slate-400 ml-2">campo valor (modelo recebimentos)</span>
    </h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-900/80 text-slate-400">
                <tr>
                    <th class="px-3 py-2 text-left">Tipo</th>
                    <th class="px-3 py-2 text-right">Valor (R$)</th>
                    <th class="px-3 py-2 text-center">%</th>
                    <th class="px-3 py-2 text-center">Taxa</th>
                    <th class="px-3 py-2 text-right">Despesa (R$)</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="pg in resultado.pagamentos" :key="pg.id">
                    <tr class="border-t border-slate-700 hover:bg-slate-900/40">
                        <td class="px-3 py-2 text-slate-200" x-text="pg.tipo"></td>
                        <td class="px-3 py-2 text-right">
                            <label class="money-field">
                                <span class="money-prefix">R$</span>
                                <input type="number" step="0.01" class="input-edit" x-model.number="pg.valor">
                            </label>
                        </td>
                        <td class="px-3 py-2 text-center text-slate-400" x-text="pg.percentual + '%'"></td>
                        <td class="px-3 py-2 text-center text-slate-400"
                            x-text="pg.taxa != null ? (pg.taxa * 100).toFixed(1) + '%' : '-'"></td>
                        <td class="px-3 py-2 text-right text-rose-400" x-text="formatMoney(pg.despesa)"></td>
                    </tr>
                </template>
                <tr class="border-t border-slate-600 font-bold bg-slate-900/60">
                    <td class="px-3 py-2 text-slate-300">TOTAL</td>
                    <td class="px-3 py-2 text-right text-emerald-400" x-text="formatMoney(resultado.total_pagamentos)"></td>
                    <td class="px-3 py-2 text-center text-slate-400">100%</td>
                    <td></td>
                    <td class="px-3 py-2 text-right text-rose-400" x-text="formatMoney(resultado.total_taxas)"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
