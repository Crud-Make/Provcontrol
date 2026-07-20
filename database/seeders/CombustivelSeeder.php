<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CombustivelSeeder extends Seeder
{
    public function run(): void
    {
        $postoId = DB::table('postos')->first()->id;

        $combustiveis = [
            ['nome' => 'Gasolina Comum', 'codigo' => 'GC', 'preco_atual' => 6.2800],
            ['nome' => 'Gasolina Aditivada', 'codigo' => 'GA', 'preco_atual' => 6.2800],
            ['nome' => 'Etanol', 'codigo' => 'ET', 'preco_atual' => 4.5800],
            ['nome' => 'Diesel S10', 'codigo' => 'DS10', 'preco_atual' => 6.2800],
        ];

        foreach ($combustiveis as $c) {
            DB::table('combustiveis')->insert([
                'posto_id' => $postoId,
                'nome' => $c['nome'],
                'codigo' => $c['codigo'],
                'preco_atual' => $c['preco_atual'],
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
