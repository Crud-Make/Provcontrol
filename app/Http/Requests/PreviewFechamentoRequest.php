<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Fechamento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PreviewFechamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Fechamento::class) ?? false;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'data' => ['required', 'date'],
            'turno_id' => [
                'required',
                Rule::exists('turnos', 'id')
                    ->where('posto_id', currentPostoId()),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'data.required' => 'Informe a data do fechamento.',
            'data.date' => 'A data informada é inválida.',
            'turno_id.required' => 'Selecione o turno.',
            'turno_id.exists' => 'O turno selecionado não existe.',
        ];
    }
}
