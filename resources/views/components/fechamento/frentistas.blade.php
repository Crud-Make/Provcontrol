<div class="bg-slate-800/80 border border-slate-700 rounded-xl p-6">
    <h3 class="flex items-center gap-2 text-lg font-bold text-slate-100 mb-4">
        <span class="w-6 h-6 rounded-md bg-brand-500/20 text-brand-200 grid place-items-center text-xs font-bold shrink-0">3</span>
        Venda Frentistas
        <span class="text-xs font-normal text-slate-400 ml-1">· informado (declaratório) × conferido</span>
    </h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-900/80 text-slate-400">
                <tr>
                    <th class="px-2 py-2 text-left">Frentista</th>
                    <th class="px-2 py-2 text-right">Pix (R$)</th>
                    <th class="px-2 py-2 text-right">Crédito (R$)</th>
                    <th class="px-2 py-2 text-right">Débito (R$)</th>
                    <th class="px-2 py-2 text-right">Moeda (R$)</th>
                    <th class="px-2 py-2 text-right">Notas (R$)</th>
                    <th class="px-2 py-2 text-right">Baratão (R$)</th>
                    <th class="px-2 py-2 text-right">Produtos (R$)</th>
                    <th class="px-2 py-2 text-right">Dinheiro (R$)</th>
                    <th class="px-2 py-2 text-right">Informado (R$)</th>
                    <th class="px-2 py-2 text-right">Conferido (R$)</th>
                    <th class="px-2 py-2 text-right">Diferença (R$)</th>
                    <th class="px-2 py-2 text-center">%</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="f in resultado.frentistas" :key="f.id">
                    <tr class="border-t border-slate-700 hover:bg-slate-900/40">
                        <td class="px-2 py-2 font-medium text-slate-200 whitespace-nowrap" x-text="f.nome"></td>
                        <td class="px-1 py-2">
                            <label class="money-field"><span class="money-prefix">R$</span>
                                <input type="number" step="0.01" class="input-edit input-edit-sm" x-model.number="f.pix">
                            </label>
                        </td>
                        <td class="px-1 py-2">
                            <label class="money-field"><span class="money-prefix">R$</span>
                                <input type="number" step="0.01" class="input-edit input-edit-sm" x-model.number="f.cartao_credito">
                            </label>
                        </td>
                        <td class="px-1 py-2">
                            <label class="money-field"><span class="money-prefix">R$</span>
                                <input type="number" step="0.01" class="input-edit input-edit-sm" x-model.number="f.cartao_debito">
                            </label>
                        </td>
                        <td class="px-1 py-2">
                            <label class="money-field"><span class="money-prefix">R$</span>
                                <input type="number" step="0.01" class="input-edit input-edit-sm" x-model.number="f.moeda">
                            </label>
                        </td>
                        <td class="px-1 py-2">
                            <label class="money-field"><span class="money-prefix">R$</span>
                                <input type="number" step="0.01" class="input-edit input-edit-sm" x-model.number="f.notas">
                            </label>
                        </td>
                        <td class="px-1 py-2">
                            <label class="money-field"><span class="money-prefix">R$</span>
                                <input type="number" step="0.01" class="input-edit input-edit-sm" x-model.number="f.baratao">
                            </label>
                        </td>
                        <td class="px-1 py-2">
                            <label class="money-field"><span class="money-prefix">R$</span>
                                <input type="number" step="0.01" class="input-edit input-edit-sm" x-model.number="f.produtos">
                            </label>
                        </td>
                        <td class="px-1 py-2">
                            <label class="money-field"><span class="money-prefix">R$</span>
                                <input type="number" step="0.01" class="input-edit input-edit-sm" x-model.number="f.dinheiro">
                            </label>
                        </td>
                        <td class="px-2 py-2 text-right font-bold text-emerald-400" x-text="formatMoney(f.total)"></td>
                        <td class="px-1 py-2">
                            <label class="money-field"><span class="money-prefix">R$</span>
                                <input type="number" step="0.01" class="input-edit input-edit-sm" x-model.number="f.valor_conferido">
                            </label>
                        </td>
                        <td class="px-2 py-2 text-right font-bold"
                            :class="Math.abs(f.diferenca) < 0.01 ? 'text-emerald-400' : 'text-brandred-300'"
                            x-text="formatMoney(f.diferenca)"></td>
                        <td class="px-2 py-2 text-center text-slate-400" x-text="f.percentual + '%'"></td>
                    </tr>
                </template>
                <tr class="border-t border-slate-600 font-bold bg-slate-900/60">
                    <td class="px-2 py-2 text-slate-300">TOTAL</td>
                    <td class="px-2 py-2 text-right text-slate-300" x-text="formatMoney(resultado.total_pix_frentistas)"></td>
                    <td class="px-2 py-2 text-right text-slate-300" x-text="formatMoney(resultado.total_cartao_credito_frentistas)"></td>
                    <td class="px-2 py-2 text-right text-slate-300" x-text="formatMoney(resultado.total_cartao_debito_frentistas)"></td>
                    <td class="px-2 py-2 text-right text-slate-300" x-text="formatMoney(resultado.total_moeda_frentistas)"></td>
                    <td class="px-2 py-2 text-right text-slate-300" x-text="formatMoney(resultado.total_notas_frentistas)"></td>
                    <td class="px-2 py-2 text-right text-slate-300" x-text="formatMoney(resultado.total_baratao_frentistas)"></td>
                    <td class="px-2 py-2 text-right text-slate-300" x-text="formatMoney(resultado.total_produtos_frentistas)"></td>
                    <td class="px-2 py-2 text-right text-slate-300" x-text="formatMoney(resultado.total_dinheiro_frentistas)"></td>
                    <td class="px-2 py-2 text-right text-emerald-400" x-text="formatMoney(resultado.total_informado_frentistas)"></td>
                    <td class="px-2 py-2 text-right text-emerald-400" x-text="formatMoney(resultado.total_frentistas)"></td>
                    <td class="px-2 py-2"></td>
                    <td class="px-2 py-2 text-center text-slate-400">100%</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
