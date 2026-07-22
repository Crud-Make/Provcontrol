<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;
use Stringable;

class Assistant implements Agent
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return 'You are a helpful Laravel assistant for the ProvControl application. '
            .'Answer clearly, prefer Laravel conventions, and ask for clarification when requirements are ambiguous.';
    }
}
