<?php

declare(strict_types=1);

use App\Models\Posto;
use App\Services\DecimalCalculator;

if (! function_exists('currentPostoId')) {
    function currentPostoId(): int
    {
        $id = session('posto_id') ?? request()->attributes->get('posto_id');

        abort_unless($id, 422, 'Posto não definido.');

        return (int) $id;
    }
}

if (! function_exists('formatMoney')) {
    function formatMoney(mixed $value): string
    {
        $centavos = app(DecimalCalculator::class)->toInteger($value, 2);
        $absolute = abs($centavos);
        $inteiro = intdiv($absolute, 100);
        $fracao = str_pad((string) ($absolute % 100), 2, '0', STR_PAD_LEFT);
        $sinal = $centavos < 0 ? '-' : '';

        return $sinal.number_format($inteiro, 0, ',', '.').','.$fracao;
    }
}

if (! function_exists('currentPosto')) {
    function currentPosto(): Posto
    {
        return Posto::query()->findOrFail(currentPostoId());
    }
}
