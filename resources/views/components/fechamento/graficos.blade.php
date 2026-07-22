<div class="bg-slate-800/80 border border-slate-700 rounded-xl p-6">
    <h3 class="text-lg font-bold text-slate-100 mb-4">Gráficos do Fechamento</h3>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-slate-900/50 rounded-xl p-4">
            <p class="text-sm text-slate-400 mb-3 text-center">Vendas por Produto</p>
            <div class="h-56">
                <canvas id="chartProdutos"></canvas>
            </div>
        </div>
        <div class="bg-slate-900/50 rounded-xl p-4">
            <p class="text-sm text-slate-400 mb-3 text-center">Vendas por Frentista</p>
            <div class="h-56">
                <canvas id="chartFrentistas"></canvas>
            </div>
        </div>
        <div class="bg-slate-900/50 rounded-xl p-4">
            <p class="text-sm text-slate-400 mb-3 text-center">Pagamentos Eletrônicos</p>
            <div class="h-56">
                <canvas id="chartPagamentos"></canvas>
            </div>
        </div>
    </div>
</div>
