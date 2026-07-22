<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Frentista;
use App\Models\Posto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Frentista>
 */
class FrentistaFactory extends Factory
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
            'user_id' => User::factory(),
            'nome' => fake()->name(),
            'cpf' => fake()->unique()->numerify('###.###.###-##'),
            'telefone' => fake()->numerify('(##) #####-####'),
            'data_admissao' => fake()->dateTimeBetween('-10 years', 'now'),
            'foto_url' => null,
            'ativo' => true,
        ];
    }
}
