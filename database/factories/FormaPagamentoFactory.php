<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\FormaPagamento;
use App\Models\Posto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FormaPagamento>
 */
class FormaPagamentoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $formaPagamento = fake()->randomElement([
            ['nome' => 'Dinheiro', 'tipo' => 'dinheiro', 'taxa' => null],
            ['nome' => 'Pix', 'tipo' => 'pix', 'taxa' => '0.00'],
            ['nome' => 'Cartão de débito', 'tipo' => 'debito', 'taxa' => '1.49'],
            ['nome' => 'Cartão de crédito', 'tipo' => 'credito', 'taxa' => '2.99'],
        ]);

        return [
            'posto_id' => Posto::factory(),
            'nome' => $formaPagamento['nome'],
            'tipo' => $formaPagamento['tipo'],
            'taxa_percentual' => $formaPagamento['taxa'],
            'ativo' => true,
        ];
    }
}
