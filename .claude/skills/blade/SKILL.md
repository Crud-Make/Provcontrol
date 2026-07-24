---
name: blade
description: Convenções de Laravel Blade para o ProvControl — componentes, escaping de output, segurança contra XSS, estrutura e formatação. Use ao criar ou editar qualquer arquivo em resources/views/**/*.blade.php.
---

**Nome:** Blade Templates
**Stack alvo:** Laravel 13 + Blade + Alpine.js 3 + Tailwind 4 + Vite (sem Livewire)
**Tags:** resources/views/**/*.blade.php, laravel, php, frontend, blade, template, html

> **Fonte original:** [majiayu000/claude-skill-registry](https://github.com/majiayu000/claude-skill-registry) — `skills/development/blade/SKILL.md` (MIT).
> Adaptado para o ProvControl: idioma pt-BR em vez de inglês, e referências a Livewire removidas (o projeto não usa Livewire — a interatividade é feita com Alpine.js).

## Regras

- Use componentes Blade (`<x-componente>`) para pedaços de UI reutilizáveis
- Prefira componentes anônimos para elementos simples, só de apresentação
- Use componentes baseados em classe quando houver lógica envolvida
- Use `{{ }}` para output escapado (padrão) — **nunca** imprima entrada do usuário sem escapar
- Use `{!! !!}` apenas quando o conteúdo for comprovadamente seguro (ex.: HTML já sanitizado)
- Sem tags `<style>` inline em arquivos Blade — o estilo vem do Tailwind
- Sem tags `<script>` inline em arquivos Blade — use diretivas do Alpine.js, ou `@push('scripts')` para JS de página
- Mantenha os textos voltados ao usuário em **português (pt-BR)**, consistentes com o resto do sistema
- Indente diretivas Blade de forma consistente (4 espaços)
- Use a sintaxe curta de atributos onde houver suporte: `@class`, `@style`

## Exemplos

```blade
{{-- Output escapado — sempre use {{ }} para dados do usuário --}}
<h1>{{ $user->name }}</h1>
<p>{{ $post->excerpt }}</p>

{{-- Output cru — apenas para conteúdo já sanitizado --}}
{!! $article->sanitizedBody !!}

{{-- Componente anônimo --}}
<x-card>
    <x-slot:title>{{ $fechamento->titulo }}</x-slot:title>
    <p>{{ $fechamento->valor }}</p>
</x-card>
```

```blade
{{-- Diretiva @class para classes condicionais --}}
<div @class(['p-4 rounded', 'bg-green-100' => $ativo, 'bg-red-100' => $erro])>
    {{ $mensagem }}
</div>

{{-- Diretiva @style para estilos condicionais --}}
<div @style(['color: green' => $sucesso, 'color: red' => $falha])>
    {{ $texto }}
</div>
```

```blade
{{-- Alpine.js via diretiva — sem tags de script inline --}}
<div x-data="{ aberto: false }">
    <button @click="aberto = !aberto">Alternar</button>
    <div x-show="aberto" x-cloak>Conteúdo</div>
</div>
```

## Anti-patterns

- Usar `{!! $user->input !!}` em dados fornecidos pelo usuário — vulnerabilidade de XSS
- Adicionar tags `<style>` ou `<script>` direto nos arquivos Blade
- Colocar lógica PHP complexa dentro do template (extraia para componentes, controllers ou services)
- Usar indentação inconsistente em blocos `@if`, `@foreach`, `@while`
- Misturar inglês e português nos textos exibidos ao usuário

## Referências

- [Laravel Blade Templates](https://laravel.com/docs/blade)
- [Alpine.js](https://alpinejs.dev/)
