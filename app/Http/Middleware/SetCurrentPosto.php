<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentPosto
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_if($user === null, 401);

        $frentista = $user->frentista;

        if ($frentista !== null) {
            $postoId = (int) $frentista->posto_id;
        } else {
            $postoId = session('posto_id')
                ?? $request->header('X-Posto-Id')
                ?? $user->postos()
                    ->where('postos.ativo', true)
                    ->orderBy('postos.id')
                    ->value('postos.id');
        }

        abort_if($postoId === null, 422, 'Selecione um posto.');

        $postoId = (int) $postoId;

        abort_unless(
            $user->postos()->where('postos.id', $postoId)->exists(),
            403,
            'Você não possui acesso a este posto.'
        );

        session(['posto_id' => $postoId]);
        $request->attributes->set('posto_id', $postoId);

        return $next($request);
    }
}
