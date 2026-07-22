<?php

declare(strict_types=1);

namespace App\Models\Concerns;

trait InteractsWithScaledDecimals
{
    protected function decimalToInteger(mixed $value, int $scale): int
    {
        $normalized = str_replace(',', '.', trim((string) ($value ?? '0')));
        $negative = str_starts_with($normalized, '-');
        $unsigned = ltrim($normalized, '+-');

        if (! preg_match('/^\d+(?:\.\d+)?$/', $unsigned)) {
            throw new \InvalidArgumentException("Valor decimal inválido: {$normalized}");
        }

        [$whole, $fraction] = array_pad(explode('.', $unsigned, 2), 2, '');
        $paddedFraction = str_pad($fraction, $scale + 1, '0');
        $scaled = ((int) $whole * (10 ** $scale))
            + (int) substr($paddedFraction, 0, $scale);

        if ((int) $paddedFraction[$scale] >= 5) {
            $scaled++;
        }

        return $negative ? -$scaled : $scaled;
    }

    protected function integerToDecimal(int $value, int $scale): string
    {
        $absolute = abs($value);
        $divisor = 10 ** $scale;
        $whole = intdiv($absolute, $divisor);
        $fraction = str_pad((string) ($absolute % $divisor), $scale, '0', STR_PAD_LEFT);

        return ($value < 0 ? '-' : '').$whole.'.'.$fraction;
    }

    protected function roundScaledDivision(int $value, int $divisor): int
    {
        $rounded = intdiv(abs($value) + intdiv($divisor, 2), $divisor);

        return $value < 0 ? -$rounded : $rounded;
    }
}
