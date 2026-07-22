<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Bomba;
use App\Models\Posto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bomba>
 */
class BombaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'posto_id' => Posto::factory(),
            'nome' => 'Bomba '.fake()->numberBetween(1, 9999),
            'localizacao' => fake()->randomElement([
                'Ilha principal',
                'Ilha lateral',
                'Pátio frontal',
                'Pátio traseiro',
            ]),
            'ativo' => true,
        ];
    }
}
