<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Posto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Posto>
 */
class PostoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->company().' Posto',
            'cnpj' => fake()->unique()->numerify('##.###.###/####-##'),
            'endereco' => fake()->streetAddress(),
            'cidade' => fake()->city(),
            'uf' => fake()->stateAbbr(),
            'ativo' => true,
        ];
    }
}
