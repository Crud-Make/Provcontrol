<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Ai\Agents\Assistant;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Exceptions\AiException;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Prompt the Laravel AI SDK assistant and return generated text.')]
class PromptAgentTool extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'prompt' => ['required', 'string', 'max:50000'],
            'provider' => ['nullable', 'string'],
            'model' => ['nullable', 'string', 'max:255'],
        ]);

        $provider = isset($validated['provider'])
            ? Lab::from($validated['provider'])
            : null;

        try {
            $agent = Assistant::make();

            $response = $agent->prompt(
                $validated['prompt'],
                provider: $provider,
                model: $validated['model'] ?? null,
            );

            return Response::text((string) $response);
        } catch (AiException $exception) {
            return Response::error($exception->getMessage());
        }
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'prompt' => $schema->string()
                ->description('The prompt to send to the AI assistant.')
                ->required(),
            'provider' => $schema->string()
                ->description('Optional AI provider key (openai, anthropic, gemini, groq, ollama, etc.).'),
            'model' => $schema->string()
                ->description('Optional model name for the selected provider.'),
        ];
    }
}
