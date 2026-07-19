<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Ai\Embeddings;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Exceptions\AiException;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Generate vector embeddings for text using the Laravel AI SDK.')]
class GenerateEmbeddingsTool extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'text' => ['required', 'string', 'max:50000'],
            'provider' => ['nullable', 'string'],
        ]);

        $provider = isset($validated['provider'])
            ? Lab::from($validated['provider'])
            : null;

        try {
            $response = Embeddings::for([$validated['text']])
                ->generate(provider: $provider);

            return Response::json([
                'dimensions' => count($response->embeddings[0] ?? []),
                'embeddings' => $response->embeddings,
            ]);
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
            'text' => $schema->string()
                ->description('The text to convert into vector embeddings.')
                ->required(),
            'provider' => $schema->string()
                ->description('Optional embeddings provider key (openai, gemini, cohere, etc.).'),
        ];
    }
}
