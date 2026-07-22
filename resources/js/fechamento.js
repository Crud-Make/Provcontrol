/**
 * Alpine: UI do fechamento. Sem fórmulas de negócio — só formatação e sync com PHP.
 */
export default function fechamentoForm(config = {}) {
    return {
        data: config.data ?? '',
        turnoId: config.turnoId ?? '',
        routes: config.routes ?? {},
        resultado: null,
        carregando: false,
        salvando: false,
        erro: null,
        sucesso: null,
        charts: {
            produtos: null,
            frentistas: null,
            pagamentos: null,
        },

        async calcular() {
            this.erro = null;
            this.sucesso = null;

            if (!this.data || !this.turnoId) {
                this.erro = 'Preencha a data e o turno.';

                return;
            }

            this.carregando = true;

            try {
                const payload = await this.requestJson(this.routes.preview, {
                    data: this.data,
                    turno_id: Number(this.turnoId),
                });

                if (!payload.ok) {
                    this.erro = payload.erro;
                    this.resultado = null;

                    return;
                }

                this.aplicarResultado(payload.data);
            } catch {
                this.erro = 'Falha de conexão ao calcular o fechamento.';
                this.resultado = null;
            } finally {
                this.carregando = false;
            }
        },

        async salvarAlteracoes({ silencioso = false } = {}) {
            if (!this.resultado) {
                return;
            }

            this.erro = null;

            if (!silencioso) {
                this.sucesso = null;
            }

            this.salvando = true;

            try {
                const payload = await this.requestJson(this.routes.atualizar, {
                    data: this.data,
                    turno_id: Number(this.turnoId),
                    leituras: this.resultado.leituras.map((l) => ({
                        id: l.id,
                        leitura_inicial: l.leitura_inicial,
                        leitura_final: l.leitura_final,
                        preco_litro: l.preco_litro,
                    })),
                    frentistas: this.resultado.frentistas.map((f) => ({
                        id: f.id,
                        pix: f.pix,
                        cartao_credito: f.cartao_credito,
                        cartao_debito: f.cartao_debito,
                        moeda: f.moeda,
                        notas: f.notas,
                        baratao: f.baratao,
                        produtos: f.produtos,
                        dinheiro: f.dinheiro,
                        valor_conferido: f.valor_conferido,
                    })),
                    pagamentos: this.resultado.pagamentos.map((p) => ({
                        id: p.id,
                        valor: p.valor,
                    })),
                });

                if (!payload.ok) {
                    this.erro = payload.erro;

                    return;
                }

                this.aplicarResultado(payload.data);

                if (!silencioso) {
                    this.sucesso = 'Alterações salvas com sucesso.';
                }
            } catch {
                this.erro = 'Falha ao salvar as alterações.';
            } finally {
                this.salvando = false;
            }
        },

        async antesConfirmar(event) {
            if (!this.resultado) {
                event.preventDefault();

                return;
            }

            event.preventDefault();
            await this.salvarAlteracoes({ silencioso: true });

            if (this.erro) {
                return;
            }

            event.target.submit();
        },

        aplicarResultado(data) {
            this.resultado = {
                ...data,
                leituras: (data.leituras || []).map((l) => ({
                    ...l,
                    leitura_inicial: Number(l.leitura_inicial),
                    leitura_final: Number(l.leitura_final),
                    preco_litro: Number(l.preco_litro),
                    litros_vendidos: Number(l.litros_vendidos),
                    valor_total: Number(l.valor_total),
                    percentual: Number(l.percentual ?? 0),
                    cor: l.cor || '#94a3b8',
                })),
                frentistas: (data.frentistas || []).map((f) => ({
                    ...f,
                    pix: Number(f.pix || 0),
                    cartao_credito: Number(f.cartao_credito || 0),
                    cartao_debito: Number(f.cartao_debito || 0),
                    moeda: Number(f.moeda || 0),
                    notas: Number(f.notas || 0),
                    baratao: Number(f.baratao || 0),
                    produtos: Number(f.produtos || 0),
                    dinheiro: Number(f.dinheiro || 0),
                    total: Number(f.total || 0),
                    valor_conferido: Number(f.valor_conferido || 0),
                    diferenca: Number(f.diferenca || 0),
                    percentual: Number(f.percentual ?? 0),
                })),
                pagamentos: (data.pagamentos || []).map((p) => ({
                    ...p,
                    valor: Number(p.valor ?? p.total ?? 0),
                    total: Number(p.total ?? p.valor ?? 0),
                    despesa: Number(p.despesa || 0),
                    percentual: Number(p.percentual ?? 0),
                })),
            };

            this.$nextTick(() => this.atualizarGraficos());
        },

        atualizarGraficos() {
            if (!this.resultado || typeof Chart === 'undefined') {
                return;
            }

            const moneyTooltip = (value) => this.formatMoney(value);
            const textColor = '#94a3b8';
            const gridColor = 'rgba(148, 163, 184, 0.15)';

            const ctxProdutos = document.getElementById('chartProdutos');
            if (ctxProdutos) {
                this.charts.produtos?.destroy();
                this.charts.produtos = new Chart(ctxProdutos, {
                    type: 'doughnut',
                    data: {
                        labels: this.resultado.leituras.map((l) => l.produto),
                        datasets: [{
                            data: this.resultado.leituras.map((l) => l.valor_total),
                            backgroundColor: this.resultado.leituras.map((l) => l.cor),
                            borderWidth: 0,
                        }],
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { color: textColor, boxWidth: 12, font: { size: 10 } },
                            },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => ` ${moneyTooltip(ctx.raw)}`,
                                },
                            },
                        },
                    },
                });
            }

            const frentistasComVenda = this.resultado.frentistas.filter((f) => f.total > 0);
            const ctxFrentistas = document.getElementById('chartFrentistas');
            if (ctxFrentistas) {
                this.charts.frentistas?.destroy();
                this.charts.frentistas = new Chart(ctxFrentistas, {
                    type: 'bar',
                    data: {
                        labels: frentistasComVenda.map((f) => f.nome),
                        datasets: [{
                            label: 'Total (R$)',
                            data: frentistasComVenda.map((f) => f.total),
                            backgroundColor: '#34d399',
                            borderRadius: 6,
                        }],
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => ` ${moneyTooltip(ctx.raw)}`,
                                },
                            },
                        },
                        scales: {
                            x: {
                                ticks: { color: textColor, font: { size: 10 } },
                                grid: { color: gridColor },
                            },
                            y: {
                                ticks: {
                                    color: textColor,
                                    font: { size: 10 },
                                    callback: (v) => 'R$ ' + Number(v).toLocaleString('pt-BR'),
                                },
                                grid: { color: gridColor },
                            },
                        },
                    },
                });
            }

            const pagamentosComValor = this.resultado.pagamentos.filter((p) => p.total > 0);
            const coresPagamentos = ['#38bdf8', '#a78bfa', '#f472b6', '#fbbf24', '#4ade80'];
            const ctxPagamentos = document.getElementById('chartPagamentos');
            if (ctxPagamentos) {
                this.charts.pagamentos?.destroy();
                this.charts.pagamentos = new Chart(ctxPagamentos, {
                    type: 'doughnut',
                    data: {
                        labels: pagamentosComValor.map((p) => p.tipo),
                        datasets: [{
                            data: pagamentosComValor.map((p) => p.total),
                            backgroundColor: pagamentosComValor.map((_, i) => coresPagamentos[i % coresPagamentos.length]),
                            borderWidth: 0,
                        }],
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { color: textColor, boxWidth: 12, font: { size: 10 } },
                            },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => ` ${moneyTooltip(ctx.raw)}`,
                                },
                            },
                        },
                    },
                });
            }
        },

        async requestJson(url, body) {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(body),
            });

            const data = await response.json().catch(() => null);

            if (!response.ok) {
                const firstError = data?.errors
                    ? Object.values(data.errors).flat()[0]
                    : null;

                return {
                    ok: false,
                    erro: data?.message ?? firstError ?? `Erro (${response.status})`,
                };
            }

            return { ok: true, data };
        },

        formatMoney(value) {
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }).format(Number(value ?? 0));
        },

        fmtNum(value, digits = 2) {
            return Number(value ?? 0).toLocaleString('pt-BR', {
                minimumFractionDigits: digits,
                maximumFractionDigits: digits,
            });
        },
    };
}
