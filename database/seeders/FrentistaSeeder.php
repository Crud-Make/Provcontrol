<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FrentistaSeeder extends Seeder
{
    public function run(): void
    {
        $postoId = (int) DB::table('postos')->value('id');

        // Nomes reais da planilha Posto Jorro, na ordem das colunas.
        $frentistas = [
            'Filip',
            'Paulo',
            'Barbra',
            'Rosimeire',
            'Sinho',
            'Nayla',
            'Elyon',
        ];

        foreach ($frentistas as $nome) {
            DB::table('frentistas')->insert([
                'posto_id' => $postoId,
                'nome' => $nome,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
