<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CombustivelSeeder extends Seeder
{
    public function run(): void
    {
        $postoId = (int) DB::table('postos')->value('id');

        $combustiveis = [
            ['nome' => 'Gasolina Comum', 'codigo' => 'GC', 'preco_atual' => '6.2800', 'cor' => '#ef4444'],
            ['nome' => 'Gasolina Aditivada', 'codigo' => 'GA', 'preco_atual' => '6.2800', 'cor' => '#3b82f6'],
            ['nome' => 'Etanol', 'codigo' => 'ET', 'preco_atual' => '4.5800', 'cor' => '#22c55e'],
            ['nome' => 'Diesel S10', 'codigo' => 'DS10', 'preco_atual' => '6.2800', 'cor' => '#eab308'],
        ];

        foreach ($combustiveis as $c) {
            DB::table('combustiveis')->insert([
                'posto_id' => $postoId,
                'nome' => $c['nome'],
                'codigo' => $c['codigo'],
                'cor' => $c['cor'],
                'preco_atual' => $c['preco_atual'],
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
