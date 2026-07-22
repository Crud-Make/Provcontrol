<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TurnoSeeder extends Seeder
{
    public function run(): void
    {
        $postoId = (int) DB::table('postos')->value('id');

        $turnos = [
            ['nome' => 'Dia', 'hora_inicio' => '06:00', 'hora_fim' => '23:59', 'ordem' => 0],
            ['nome' => 'Manhã', 'hora_inicio' => '06:00', 'hora_fim' => '12:00', 'ordem' => 1],
            ['nome' => 'Tarde', 'hora_inicio' => '12:00', 'hora_fim' => '18:00', 'ordem' => 2],
            ['nome' => 'Noite', 'hora_inicio' => '18:00', 'hora_fim' => '23:59', 'ordem' => 3],
        ];

        foreach ($turnos as $t) {
            DB::table('turnos')->insert([
                'posto_id' => $postoId,
                'nome' => $t['nome'],
                'hora_inicio' => $t['hora_inicio'],
                'hora_fim' => $t['hora_fim'],
                'ordem' => $t['ordem'],
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
