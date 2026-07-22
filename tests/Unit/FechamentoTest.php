<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\FechamentoFrentistaStatus;
use App\Models\FechamentoFrentista;
use PHPUnit\Framework\TestCase;

class FechamentoTest extends TestCase
{
    public function test_atualiza_valores_e_identifica_divergencia_do_frentista(): void
    {
        $fechamento = new FechamentoFrentista;

        $fechamento->atualizarValores([
            'pix' => '100.00',
            'cartao_credito' => '50.00',
            'cartao_debito' => '25.00',
            'valor_conferido' => '170.00',
        ]);

        $this->assertSame('175.00', $fechamento->total_informado);
        $this->assertSame('5.00', $fechamento->diferenca);
        $this->assertSame(FechamentoFrentistaStatus::Divergente, $fechamento->status);
    }
}
