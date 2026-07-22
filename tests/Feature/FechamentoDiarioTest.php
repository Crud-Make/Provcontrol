<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class FechamentoDiarioTest extends TestCase
{
    public function test_tela_de_login_esta_disponivel_para_operacao_diaria(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('ProvControl');
    }
}
