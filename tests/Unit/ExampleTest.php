<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\DecimalCalculator;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function test_decimal_calculator_arredonda_sem_float(): void
    {
        $calculator = new DecimalCalculator;

        $this->assertSame(12346, $calculator->toInteger('123.456', 2));
        $this->assertSame('123.46', $calculator->money('123.456'));
        $this->assertSame('33.33', $calculator->percentage(1, 3));
    }
}
