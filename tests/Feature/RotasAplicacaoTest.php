<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Posto;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RotasAplicacaoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->actingAs(User::query()->firstOrFail())
            ->withSession(['posto_id' => Posto::query()->value('id')]);
    }

    public function test_rotas_do_menu_respondem_200(): void
    {
        $this->get(route('dashboard'))->assertOk();
        $this->get(route('fechamentos.index'))->assertOk();
        $this->get(route('fechamentos.create'))->assertOk();
    }

    public function test_rotas_nao_implementadas_nao_existem(): void
    {
        $this->get('/fechamentos/1/edit')->assertNotFound();
        $this->delete('/fechamentos/1')->assertMethodNotAllowed();
        $this->put('/fechamentos/1')->assertMethodNotAllowed();
    }

    public function test_home_redireciona_para_dashboard(): void
    {
        $this->get('/')->assertRedirect(route('dashboard'));
    }
}
