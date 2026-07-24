<div class="bg-slate-800/80 border border-slate-700 rounded-xl p-6">
    <h3 class="flex items-center gap-2 text-lg font-bold text-slate-100 mb-4">
        <span class="w-6 h-6 rounded-md bg-brand-500/20 text-brand-200 grid place-items-center text-xs font-bold shrink-0">1</span>
        Venda Concentrador
        <span class="text-xs font-normal text-slate-400 ml-1">· salve para recalcular no servidor</span>
    </h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-900/80 text-slate-400">
                <tr>
                    <th class="px-3 py-2 text-left">Produto</th>
                    <th class="px-3 py-2 text-right">Inicial</th>
                    <th class="px-3 py-2 text-right">Fechamento</th>
                    <th class="px-3 py-2 text-right">Litros</th>
                    <th class="px-3 py-2 text-right">Valor LT (R$)</th>
                    <th class="px-3 py-2 text-right">Venda (R$)</th>
                    <th class="px-3 py-2 text-center">%</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="leitura in resultado.leituras" :key="leitura.id">
                    <tr class="border-t border-slate-700 hover:bg-slate-900/40">
                        <td class="px-3 py-2">
                            <span class="inline-flex items-center gap-2 font-medium" :style="`color: ${leitura.cor}`">
                                <span class="inline-block w-3 h-3 rounded-full shrink-0" :style="`background:${leitura.cor}`"></span>
                                <span x-text="leitura.produto"></span>
                            </span>
                        </td>
                        <td class="px-3 py-2 text-right">
                            <input type="number" step="0.001" class="input-edit" x-model.number="leitura.leitura_inicial">
                        </td>
                        <td class="px-3 py-2 text-right">
                            <input type="number" step="0.001" class="input-edit" x-model.number="leitura.leitura_final">
                        </td>
                        <td class="px-3 py-2 text-right text-slate-300 font-mono" x-text="fmtNum(leitura.litros_vendidos, 3)"></td>
                        <td class="px-3 py-2 text-right">
                            <label class="money-field">
                                <span class="money-prefix">R$</span>
                                <input type="number" step="0.01" class="input-edit input-edit-sm" x-model.number="leitura.preco_litro">
                            </label>
                        </td>
                        <td class="px-3 py-2 text-right font-bold" :style="`color: ${leitura.cor}`" x-text="formatMoney(leitura.valor_total)"></td>
                        <td class="px-3 py-2 text-center text-slate-400" x-text="leitura.percentual + '%'"></td>
                    </tr>
                </template>
                <tr class="border-t border-slate-600 font-bold bg-slate-900/60">
                    <td colspan="5" class="px-3 py-2 text-right text-slate-300">TOTAL</td>
                    <td class="px-3 py-2 text-right text-brand-300" x-text="formatMoney(resultado.total_concentrador)"></td>
                    <td class="px-3 py-2 text-center text-slate-400">100%</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
