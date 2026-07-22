<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_home_redireciona_para_dashboard(): void
    {
        $this->get('/')->assertRedirect(route('dashboard'));
    }
}
