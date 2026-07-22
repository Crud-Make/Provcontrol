<?php

declare(strict_types=1);

namespace App\Mcp\Servers;

use App\Mcp\Tools\GenerateEmbeddingsTool;
use App\Mcp\Tools\PromptAgentTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Laravel AI')]
#[Version('1.0.0')]
#[Instructions('Official Laravel AI MCP server powered by laravel/ai. Use prompt-agent for text generation and generate-embeddings for semantic vectors. Configure provider API keys in the application .env file.')]
class AiServer extends Server
{
    protected array $tools = [
        PromptAgentTool::class,
        GenerateEmbeddingsTool::class,
    ];

    protected array $resources = [
        //
    ];

    protected array $prompts = [
        //
    ];
}
