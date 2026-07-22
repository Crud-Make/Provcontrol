<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Posto;
use App\Models\Turno;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthAndPostoAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_e_redirecionado_para_login(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_usuario_ativo_pode_entrar_e_sair(): void
    {
        $user = User::factory()->create([
            'email' => 'gerente@example.com',
            'password' => Hash::make('senha-segura'),
            'role' => Role::Gerente,
            'ativo' => true,
        ]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'senha-segura',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_usuario_inativo_nao_pode_entrar(): void
    {
        $user = User::factory()->create([
            'email' => 'inativo@example.com',
            'password' => Hash::make('senha-segura'),
            'ativo' => false,
        ]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'senha-segura',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_middleware_bloqueia_posto_sem_vinculo(): void
    {
        $postoPermitido = Posto::factory()->create();
        $postoNegado = Posto::factory()->create();
        $user = User::factory()->create(['role' => Role::Gerente]);
        $user->postos()->attach($postoPermitido, ['role' => Role::Gerente->value]);

        $this->actingAs($user)
            ->withSession(['posto_id' => $postoNegado->id])
            ->get(route('dashboard'))
            ->assertForbidden();
    }

    public function test_preview_rejeita_turno_de_outro_posto(): void
    {
        ['posto' => $posto] = $this->authenticateForPosto();
        $outroPosto = Posto::factory()->create();
        $turnoExterno = Turno::factory()->for($outroPosto)->create();

        $response = $this->postJson(route('fechamentos.preview'), [
            'data' => now()->toDateString(),
            'turno_id' => $turnoExterno->id,
        ]);

        $this->assertSame(422, $response->getStatusCode(), $response->getContent());
        $errors = $response->json('errors');
        $this->assertIsArray($errors);
        $this->assertArrayHasKey('turno_id', $errors);

        $this->assertNotSame($posto->id, $outroPosto->id);
    }
}
