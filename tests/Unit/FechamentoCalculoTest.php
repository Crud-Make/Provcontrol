<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Leitura;
use PHPUnit\Framework\TestCase;

class FechamentoCalculoTest extends TestCase
{
    public function test_calculo_litros(): void
    {
        $leitura = new Leitura([
            'leitura_inicial' => '1481883.453',
            'leitura_final' => '1482477.273',
            'preco_litro' => '6.3800',
        ]);

        $leitura->recalcular();

        $this->assertSame('593.820', $leitura->litros_vendidos);
    }

    public function test_calculo_valor_total(): void
    {
        $leitura = new Leitura([
            'leitura_inicial' => '1481883.453',
            'leitura_final' => '1482477.273',
            'preco_litro' => '6.3800',
        ]);

        $leitura->recalcular();

        $this->assertSame('3788.57', $leitura->valor_total);
    }

    public function test_calculo_arredonda_valor_monetario_em_centavos(): void
    {
        $leitura = new Leitura([
            'leitura_inicial' => '323886.093',
            'leitura_final' => '324361.883',
            'preco_litro' => '4.5800',
        ]);

        $leitura->recalcular();

        $this->assertSame('2179.12', $leitura->valor_total);
    }

    public function test_calculo_aceita_venda_sem_movimento(): void
    {
        $leitura = new Leitura([
            'leitura_inicial' => '373826.093',
            'leitura_final' => '373826.093',
            'preco_litro' => '6.2800',
        ]);

        $leitura->recalcular();

        $this->assertSame('0.000', $leitura->litros_vendidos);
        $this->assertSame('0.00', $leitura->valor_total);
    }

    public function test_calculo_rejeita_leitura_final_menor_que_inicial(): void
    {
        $leitura = new Leitura([
            'leitura_inicial' => '100.000',
            'leitura_final' => '99.999',
            'preco_litro' => '6.3800',
        ]);

        $this->expectException(\InvalidArgumentException::class);

        $leitura->recalcular();
    }
}
