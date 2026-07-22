<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Posto;
use App\Models\Turno;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Turno>
 */
class TurnoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $turno = fake()->randomElement([
            ['nome' => 'Manhã', 'hora_inicio' => '06:00:00', 'hora_fim' => '14:00:00', 'ordem' => 1],
            ['nome' => 'Tarde', 'hora_inicio' => '14:00:00', 'hora_fim' => '22:00:00', 'ordem' => 2],
            ['nome' => 'Noite', 'hora_inicio' => '22:00:00', 'hora_fim' => '06:00:00', 'ordem' => 3],
        ]);

        return [
            'posto_id' => Posto::factory(),
            'nome' => $turno['nome'],
            'hora_inicio' => $turno['hora_inicio'],
            'hora_fim' => $turno['hora_fim'],
            'ordem' => $turno['ordem'],
            'ativo' => true,
        ];
    }
}
