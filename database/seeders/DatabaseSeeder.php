<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PostoSeeder::class,
            TurnoSeeder::class,
            CombustivelSeeder::class,
            BombaSeeder::class,
            BicoSeeder::class,
            FrentistaSeeder::class,
        ]);
    }
}
