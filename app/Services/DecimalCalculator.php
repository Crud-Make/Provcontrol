<?php

declare(strict_types=1);

namespace App\Services;

final class DecimalCalculator
{
    public function toInteger(mixed $value, int $scale): int
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

    public function toDecimal(int $value, int $scale): string
    {
        $absolute = abs($value);
        $divisor = 10 ** $scale;
        $whole = intdiv($absolute, $divisor);
        $fraction = str_pad((string) ($absolute % $divisor), $scale, '0', STR_PAD_LEFT);

        return ($value < 0 ? '-' : '').$whole.'.'.$fraction;
    }

    public function roundDivide(int $value, int $divisor): int
    {
        $rounded = intdiv(abs($value) + intdiv($divisor, 2), $divisor);

        return $value < 0 ? -$rounded : $rounded;
    }

    public function percentage(int $value, int $total): string
    {
        if ($total <= 0) {
            return '0.00';
        }

        return $this->toDecimal($this->roundDivide($value * 10_000, $total), 2);
    }

    public function money(mixed $value): string
    {
        return $this->toDecimal($this->toInteger($value, 2), 2);
    }
}
