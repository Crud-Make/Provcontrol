<?php

declare(strict_types=1);

namespace Tests;

use App\Enums\Role;
use App\Models\Posto;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @return array{user: User, posto: Posto}
     */
    protected function authenticateForPosto(?Posto $posto = null, ?User $user = null): array
    {
        $posto ??= Posto::factory()->create();
        $user ??= User::factory()->create([
            'role' => Role::Admin,
            'ativo' => true,
        ]);

        $user->postos()->syncWithoutDetaching([
            $posto->id => ['role' => Role::Admin->value],
        ]);

        $this->actingAs($user)->withSession(['posto_id' => $posto->id]);

        return ['user' => $user, 'posto' => $posto];
    }
}
