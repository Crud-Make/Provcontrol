<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('postos')->insert([
            'nome' => 'Posto Providência',
            'cnpj' => '12.345.678/0001-90',
            'endereco' => 'Rua Luiz Viana Filho',
            'cidade' => 'Tucano',
            'uf' => 'BA',
            'ativo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
