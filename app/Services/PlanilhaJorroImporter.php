<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Faz o parse fiel do CSV exportado da planilha "Posto Jorro" (aba de um mês,
 * blocos "Caixa Dia NN"). Converte cada dia em uma estrutura pronta para seed,
 * preservando encerrantes, preços, pagamentos eletrônicos e vendas por frentista.
 */
class PlanilhaJorroImporter
{
    /**
     * Mapeamento do número do bico para o código do combustível (estrutura da planilha).
     *
     * @var array<int, string>
     */
    private const BICO_COMBUSTIVEL = [
        1 => 'GC',
        2 => 'GA',
        3 => 'ET',
        4 => 'DS10',
        5 => 'GC',
        6 => 'GC',
    ];

    /**
     * Nome da forma de pagamento na planilha => nome cadastrado no sistema.
     *
     * @var array<string, string>
     */
    private const PAGAMENTO_NOMES = [
        'Cartao,C.' => 'Cartão C',
        'Cartao,B.' => 'Cartão B',
        'Pix' => 'Pix',
        'APP, Baratao' => 'APP Baratão',
        'APP, Providencia' => 'APP Providência',
    ];

    /**
     * @return list<array{
     *     data: string,
     *     concentrador: list<array{numero: int, codigo: string, inicial: string, final: string, preco: string}>,
     *     pagamentos: list<array{nome: string, valor: string}>,
     *     frentistas: array<string, array{pix: string, cartao_credito: string, cartao_debito: string, moeda: string, notas: string, baratao: string, dinheiro: string, informado: string, conferido: string}>
     * }>
     */
    public function importar(string $caminho, int $ano = 2025, int $mes = 1): array
    {
        $linhas = $this->lerLinhas($caminho);

        $dias = [];
        $atual = null;
        $estado = 'nenhum';
        /** @var array<int, string> $nomesFrentistas */
        $nomesFrentistas = [];

        foreach ($linhas as $colunas) {
            $titulo = (string) ($colunas[1] ?? '');

            // O bloco final "Caixa Dia 01 a 31" é o resumo mensal — não é um dia.
            if (preg_match('/Caixa Dia \d+ a \d+/', $titulo) === 1) {
                break;
            }

            $tituloDia = $this->detectarDia($titulo);

            if ($tituloDia !== null) {
                if ($atual !== null && $this->diaValido($atual)) {
                    $dias[] = $atual;
                }

                $atual = [
                    'data' => sprintf('%04d-%02d-%02d', $ano, $mes, $tituloDia),
                    'concentrador' => [],
                    'pagamentos' => [],
                    'frentistas' => [],
                ];
                $estado = 'nenhum';
                $nomesFrentistas = [];

                continue;
            }

            if ($atual === null) {
                continue;
            }

            $rotulo = trim((string) ($colunas[2] ?? ''));

            if ($rotulo === 'Venda Concentrador' && $estado !== 'frentista') {
                $estado = 'concentrador';

                continue;
            }

            if (($colunas[3] ?? '') === 'Inter pog') {
                $estado = 'pagamentos';

                continue;
            }

            if ($rotulo === 'Venda Frentista') {
                $estado = 'frentista_cabecalho';

                continue;
            }

            $atual = $this->processarLinha($atual, $estado, $rotulo, $colunas, $nomesFrentistas);

            if ($estado === 'frentista_cabecalho' && $nomesFrentistas !== []) {
                $estado = 'frentista';
            }
        }

        if ($atual !== null && $this->diaValido($atual)) {
            $dias[] = $atual;
        }

        return $dias;
    }

    /**
     * @param  array{data: string, concentrador: list<array<string, mixed>>, pagamentos: list<array<string, mixed>>, frentistas: array<string, array<string, string>>}  $atual
     * @param  list<string>  $colunas
     * @param  array<int, string>  $nomesFrentistas
     * @return array{data: string, concentrador: list<array<string, mixed>>, pagamentos: list<array<string, mixed>>, frentistas: array<string, array<string, string>>}
     */
    private function processarLinha(array $atual, string $estado, string $rotulo, array $colunas, array &$nomesFrentistas): array
    {
        if ($estado === 'concentrador' && $numero = $this->numeroBico($rotulo)) {
            $inicial = $colunas[3] ?? '';
            $final = $colunas[4] ?? '';

            if (trim((string) $final) !== '') {
                $preco = trim((string) ($colunas[6] ?? ''));
                $precoPadrao = $atual['concentrador'][0]['preco'] ?? '0.0000';

                $atual['concentrador'][] = [
                    'numero' => $numero,
                    'codigo' => self::BICO_COMBUSTIVEL[$numero] ?? 'GC',
                    'inicial' => $this->decimal($inicial, 3),
                    'final' => $this->decimal($final, 3),
                    'preco' => $preco !== '' ? $this->decimal($preco, 4) : $precoPadrao,
                ];
            }

            return $atual;
        }

        if ($estado === 'pagamentos' && isset(self::PAGAMENTO_NOMES[$rotulo])) {
            $atual['pagamentos'][] = [
                'nome' => self::PAGAMENTO_NOMES[$rotulo],
                'valor' => $this->decimal($colunas[5] ?? '0', 2),
            ];

            return $atual;
        }

        if ($estado === 'frentista_cabecalho') {
            foreach (range(3, 9) as $indice) {
                $nome = trim((string) ($colunas[$indice] ?? ''));

                if ($nome !== '') {
                    $nomesFrentistas[$indice] = $nome;
                }
            }

            return $atual;
        }

        if ($estado === 'frentista') {
            $campo = $this->campoFrentista($rotulo);

            if ($campo !== null) {
                foreach ($nomesFrentistas as $indice => $nome) {
                    $atual['frentistas'][$nome] ??= $this->frentistaVazio();
                    $atual['frentistas'][$nome][$campo] = $this->decimal($colunas[$indice] ?? '0', 2);
                }
            }
        }

        return $atual;
    }

    /**
     * @return array{pix: string, cartao_credito: string, cartao_debito: string, moeda: string, notas: string, baratao: string, dinheiro: string, informado: string, conferido: string}
     */
    private function frentistaVazio(): array
    {
        return [
            'pix' => '0.00',
            'cartao_credito' => '0.00',
            'cartao_debito' => '0.00',
            'moeda' => '0.00',
            'notas' => '0.00',
            'baratao' => '0.00',
            'dinheiro' => '0.00',
            'informado' => '0.00',
            'conferido' => '0.00',
        ];
    }

    private function campoFrentista(string $rotulo): ?string
    {
        return match ($rotulo) {
            'Pix' => 'pix',
            'Cartao Credito' => 'cartao_credito',
            'Cartao Debito' => 'cartao_debito',
            'Moeda' => 'moeda',
            'Notas' => 'notas',
            'Baratao' => 'baratao',
            'Dinheiro' => 'dinheiro',
            'Venda Frentistas.' => 'informado',
            'Venda Concentrador' => 'conferido',
            default => null,
        };
    }

    private function numeroBico(string $rotulo): int
    {
        if (preg_match('/Bico\s*0?(\d)/', $rotulo, $m) === 1) {
            return (int) $m[1];
        }

        return 0;
    }

    private function detectarDia(string $titulo): ?int
    {
        if (preg_match('/Caixa Dia (\d+) Posto/', $titulo, $m) === 1) {
            $dia = (int) $m[1];

            return $dia >= 1 && $dia <= 31 ? $dia : null;
        }

        return null;
    }

    /**
     * @param  array{concentrador: list<array<string, mixed>>}  $dia
     */
    private function diaValido(array $dia): bool
    {
        return count($dia['concentrador']) > 0;
    }

    private function decimal(mixed $valor, int $casas): string
    {
        $normalizado = str_replace(',', '.', trim((string) ($valor ?? '')));

        if ($normalizado === '' || ! is_numeric($normalizado)) {
            $normalizado = '0';
        }

        return sprintf('%.'.$casas.'f', (float) $normalizado);
    }

    /**
     * @return list<list<string>>
     */
    private function lerLinhas(string $caminho): array
    {
        $handle = fopen($caminho, 'r');

        if ($handle === false) {
            throw new \RuntimeException("Não foi possível abrir a planilha: {$caminho}");
        }

        $linhas = [];

        while (($colunas = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            $linhas[] = array_map(static fn ($v): string => (string) ($v ?? ''), $colunas);
        }

        fclose($handle);

        return $linhas;
    }
}
