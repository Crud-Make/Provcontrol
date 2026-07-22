<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BombaSeeder extends Seeder
{
    public function run(): void
    {
        $postoId = (int) DB::table('postos')->value('id');

        $bombas = [
            ['nome' => 'BOMBA 01', 'localizacao' => 'Esquerda'],
            ['nome' => 'BOMBA 02', 'localizacao' => 'Centro'],
            ['nome' => 'BOMBA 03', 'localizacao' => 'Direita'],
        ];

        foreach ($bombas as $b) {
            DB::table('bombas')->insert([
                'posto_id' => $postoId,
                'nome' => $b['nome'],
                'localizacao' => $b['localizacao'],
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
