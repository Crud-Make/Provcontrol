<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\FechamentoStatus;
use App\Enums\Role;
use App\Models\Fechamento;
use App\Models\User;

class FechamentoPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canManage($user) && $this->hasPostoAccess($user, currentPostoId());
    }

    public function view(User $user, Fechamento $fechamento): bool
    {
        return $this->canManage($user)
            && $fechamento->posto_id === currentPostoId()
            && $this->hasPostoAccess($user, (int) $fechamento->posto_id);
    }

    public function create(User $user): bool
    {
        return $this->canManage($user) && $this->hasPostoAccess($user, currentPostoId());
    }

    public function update(User $user, Fechamento $fechamento): bool
    {
        return $this->view($user, $fechamento)
            && $fechamento->status !== FechamentoStatus::Fechado;
    }

    private function canManage(User $user): bool
    {
        return $user->ativo && in_array($user->role, [
            Role::Admin,
            Role::Proprietario,
            Role::Gerente,
            Role::Operador,
        ], true);
    }

    private function hasPostoAccess(User $user, int $postoId): bool
    {
        return $user->postos()->where('postos.id', $postoId)->exists();
    }
}
