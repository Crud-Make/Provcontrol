<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FrentistaSeeder extends Seeder
{
    public function run(): void
    {
        $postoId = DB::table('postos')->first()->id;

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
