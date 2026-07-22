<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostoSeeder extends Seeder
{
    public function run(): void
    {
        $postoId = DB::table('postos')->insertGetId([
            'nome' => 'Posto Providência',
            'cnpj' => '12.345.678/0001-90',
            'endereco' => 'Rua Luiz Viana Filho',
            'cidade' => 'Tucano',
            'uf' => 'BA',
            'ativo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('posto_user')->insert([
            'posto_id' => $postoId,
            'user_id' => (int) DB::table('users')->value('id'),
            'role' => Role::Admin->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
