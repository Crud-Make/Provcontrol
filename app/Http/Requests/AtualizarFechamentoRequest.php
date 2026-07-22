<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Fechamento;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AtualizarFechamentoRequest extends FormRequest
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
            'leituras' => ['nullable', 'array'],
            'leituras.*.id' => [
                'required',
                Rule::exists('leituras', 'id')->where(function (Builder $query): void {
                    $query
                        ->where('posto_id', currentPostoId())
                        ->where('data', $this->input('data'))
                        ->where('turno_id', $this->input('turno_id'));
                }),
            ],
            'leituras.*.leitura_inicial' => ['required', 'numeric'],
            'leituras.*.leitura_final' => [
                'required',
                'numeric',
                'gte:leituras.*.leitura_inicial',
            ],
            'leituras.*.preco_litro' => ['required', 'numeric', 'min:0'],
            'frentistas' => ['nullable', 'array'],
            'frentistas.*.id' => [
                'required',
                Rule::exists('fechamento_frentistas', 'id')->where(function (Builder $query): void {
                    $query->whereIn(
                        'fechamento_id',
                        Fechamento::query()
                            ->select('id')
                            ->forPosto(currentPostoId())
                            ->where('data', $this->input('data'))
                            ->where('turno_id', $this->input('turno_id'))
                    );
                }),
            ],
            'frentistas.*.pix' => ['nullable', 'numeric', 'min:0'],
            'frentistas.*.cartao_credito' => ['nullable', 'numeric', 'min:0'],
            'frentistas.*.cartao_debito' => ['nullable', 'numeric', 'min:0'],
            'frentistas.*.moeda' => ['nullable', 'numeric', 'min:0'],
            'frentistas.*.notas' => ['nullable', 'numeric', 'min:0'],
            'frentistas.*.baratao' => ['nullable', 'numeric', 'min:0'],
            'frentistas.*.produtos' => ['nullable', 'numeric', 'min:0'],
            'frentistas.*.dinheiro' => ['nullable', 'numeric', 'min:0'],
            'frentistas.*.valor_conferido' => ['required', 'numeric', 'min:0'],
            'pagamentos' => ['nullable', 'array'],
            'pagamentos.*.id' => [
                'required',
                Rule::exists('recebimentos', 'id')->where(function (Builder $query): void {
                    $query->whereIn(
                        'fechamento_id',
                        Fechamento::query()
                            ->select('id')
                            ->forPosto(currentPostoId())
                            ->where('data', $this->input('data'))
                            ->where('turno_id', $this->input('turno_id'))
                    );
                }),
            ],
            'pagamentos.*.valor' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'data.required' => 'Informe a data do fechamento.',
            'turno_id.required' => 'Selecione o turno.',
            'turno_id.exists' => 'O turno selecionado não existe.',
            'leituras.*.id.exists' => 'Leitura inválida.',
            'frentistas.*.id.exists' => 'Frentista inválido.',
            'pagamentos.*.id.exists' => 'Pagamento inválido.',
            'pagamentos.*.valor.required' => 'Informe o valor do pagamento.',
            'leituras.*.leitura_final.gte' => 'A leitura final deve ser maior ou igual à inicial.',
        ];
    }
}
