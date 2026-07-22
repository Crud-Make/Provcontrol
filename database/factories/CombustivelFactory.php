<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Combustivel;
use App\Models\Posto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Combustivel>
 */
class CombustivelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $combustivel = fake()->randomElement([
            ['nome' => 'Gasolina Comum', 'prefixo' => 'GC', 'cor' => '#EAB308', 'preco' => '5.8990'],
            ['nome' => 'Gasolina Aditivada', 'prefixo' => 'GA', 'cor' => '#DC2626', 'preco' => '6.0990'],
            ['nome' => 'Etanol', 'prefixo' => 'ET', 'cor' => '#16A34A', 'preco' => '4.1990'],
            ['nome' => 'Diesel S10', 'prefixo' => 'DS10', 'cor' => '#F97316', 'preco' => '6.2990'],
        ]);

        return [
            'posto_id' => Posto::factory(),
            'nome' => $combustivel['nome'],
            'codigo' => $combustivel['prefixo'].fake()->unique()->numerify('######'),
            'cor' => $combustivel['cor'],
            'preco_atual' => $combustivel['preco'],
            'ativo' => true,
        ];
    }
}
