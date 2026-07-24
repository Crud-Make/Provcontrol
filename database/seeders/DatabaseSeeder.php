<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PostoSeeder::class,
            TurnoSeeder::class,
            CombustivelSeeder::class,
            BombaSeeder::class,
            BicoSeeder::class,
            FrentistaSeeder::class,
            FormaPagamentoSeeder::class,
            FechamentoPlanilhaSeeder::class,
        ]);
    }
}
