<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BicoSeeder extends Seeder
{
    public function run(): void
    {
        $postoId = (int) DB::table('postos')->value('id');
        $bombas = DB::table('bombas')->where('posto_id', $postoId)->get();
        $combustiveis = DB::table('combustiveis')->where('posto_id', $postoId)->get();

        $gc = $combustiveis->where('codigo', 'GC')->first();
        $ga = $combustiveis->where('codigo', 'GA')->first();
        $et = $combustiveis->where('codigo', 'ET')->first();
        $ds10 = $combustiveis->where('codigo', 'DS10')->first();

        // Estrutura da planilha
        $bicos = [
            ['bomba' => 'BOMBA 01', 'numero' => 1, 'combustivel' => $gc->id],
            ['bomba' => 'BOMBA 01', 'numero' => 2, 'combustivel' => $ga->id],
            ['bomba' => 'BOMBA 02', 'numero' => 3, 'combustivel' => $et->id],
            ['bomba' => 'BOMBA 02', 'numero' => 4, 'combustivel' => $ds10->id],
            ['bomba' => 'BOMBA 03', 'numero' => 5, 'combustivel' => $gc->id],
            ['bomba' => 'BOMBA 03', 'numero' => 6, 'combustivel' => $gc->id],
        ];

        foreach ($bicos as $b) {
            $bomba = $bombas->where('nome', $b['bomba'])->first();

            DB::table('bicos')->insert([
                'posto_id' => $postoId,
                'bomba_id' => $bomba->id,
                'combustivel_id' => $b['combustivel'],
                'numero' => $b['numero'],
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
