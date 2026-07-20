<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BombaSeeder extends Seeder
{
    public function run(): void
    {
        $postoId = DB::table('postos')->first()->id;

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
