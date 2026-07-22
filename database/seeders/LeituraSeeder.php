<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeituraSeeder extends Seeder
{
    /**
     * Leituras do Dia 01 — planilha Posto,Jorro, 2025.xlsx, aba "Mes, 01.".
     */
    public function run(): void
    {
        $postoId = (int) DB::table('postos')->first()->id;
        $userId = (int) DB::table('users')->first()->id;
        $turnoId = (int) DB::table('turnos')->where('nome', 'Dia')->value('id');
        $data = '2025-01-01';

        $leituras = [
            // numero_bico => [inicial, final, preco, litros, valor]
            1 => ['1481883.453', '1482477.273', '6.3800', '593.820', '3788.57'],
            2 => ['571071.052', '571280.552', '6.3800', '209.500', '1336.61'],
            3 => ['323886.093', '324361.883', '4.5800', '475.790', '2179.12'],
            4 => ['373826.093', '373826.093', '6.2800', '0.000', '0.00'],
            5 => ['360844.232', '360942.842', '6.3800', '98.610', '629.13'],
            6 => ['316659.842', '316702.231', '6.3800', '42.389', '270.44'],
        ];

        foreach ($leituras as $numero => [$inicial, $final, $preco, $litros, $valor]) {
            $bicoId = DB::table('bicos')->where('numero', $numero)->value('id');

            DB::table('leituras')->insert([
                'posto_id' => $postoId,
                'bico_id' => $bicoId,
                'turno_id' => $turnoId,
                'data' => $data,
                'leitura_inicial' => $inicial,
                'leitura_final' => $final,
                'preco_litro' => $preco,
                'litros_vendidos' => $litros,
                'valor_total' => $valor,
                'status' => 'confirmado',
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
