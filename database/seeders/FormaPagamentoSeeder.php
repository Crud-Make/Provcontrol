<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormaPagamentoSeeder extends Seeder
{
    public function run(): void
    {
        $postoId = DB::table('postos')->first()->id;

        $formas = [
            ['nome' => 'Cartão C', 'tipo' => 'cartao', 'taxa_percentual' => 0.70],
            ['nome' => 'Cartão B', 'tipo' => 'cartao', 'taxa_percentual' => 2.50],
            ['nome' => 'Pix', 'tipo' => 'digital', 'taxa_percentual' => null],
            ['nome' => 'APP Baratão', 'tipo' => 'digital', 'taxa_percentual' => 1.90],
            ['nome' => 'APP Providência', 'tipo' => 'digital', 'taxa_percentual' => null],
        ];

        foreach ($formas as $forma) {
            DB::table('formas_pagamento')->insert([
                'posto_id' => $postoId,
                'nome' => $forma['nome'],
                'tipo' => $forma['tipo'],
                'taxa_percentual' => $forma['taxa_percentual'],
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
