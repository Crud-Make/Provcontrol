<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\LeituraStatus;
use App\Models\Bico;
use App\Models\Leitura;
use App\Models\Posto;
use App\Models\Turno;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Leitura>
 */
class LeituraFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $leituraInicial = fake()->numberBetween(1_000_000, 999_000_000);
        $litrosVendidos = fake()->numberBetween(10_000, 100_000);
        $leituraFinal = $leituraInicial + $litrosVendidos;
        $precoLitro = fake()->numberBetween(40_000, 75_000);
        $valorTotal = intdiv(($litrosVendidos * $precoLitro) + 50_000, 100_000);

        return [
            'posto_id' => Posto::factory(),
            'bico_id' => static fn (array $attributes): Factory => Bico::factory()->state([
                'posto_id' => $attributes['posto_id'],
            ]),
            'turno_id' => static fn (array $attributes): Factory => Turno::factory()->state([
                'posto_id' => $attributes['posto_id'],
            ]),
            'data' => fake()->date(),
            'leitura_inicial' => $this->scaledDecimal($leituraInicial, 3),
            'leitura_final' => $this->scaledDecimal($leituraFinal, 3),
            'preco_litro' => $this->scaledDecimal($precoLitro, 4),
            'litros_vendidos' => $this->scaledDecimal($litrosVendidos, 3),
            'valor_total' => $this->scaledDecimal($valorTotal, 2),
            'status' => LeituraStatus::Rascunho,
            'user_id' => User::factory(),
            'observacoes' => null,
        ];
    }

    private function scaledDecimal(int $value, int $scale): string
    {
        $divisor = 10 ** $scale;

        return sprintf(
            '%d.%0'.$scale.'d',
            intdiv($value, $divisor),
            $value % $divisor,
        );
    }
}
