<?php

namespace App\Http\Requests\Financial;

use Illuminate\Foundation\Http\FormRequest;

class PagamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'valor' => 'required|numeric|min:0.01',
            'data_pagamento' => 'required|date',
            'forma_pagamento_id' => 'nullable|integer',
            'bandeira_id' => 'nullable|integer',
            'conta_bancaria_id' => 'nullable|integer',
            'numero_comprovante' => 'nullable|string|max:100',
            'observacoes' => 'nullable|string|max:1000',
            'dados_confirmacao' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'valor.required' => 'O valor é obrigatório',
            'valor.min' => 'O valor deve ser maior que zero',
            'data_pagamento.required' => 'A data de pagamento é obrigatória',
            'data_pagamento.date' => 'Data de pagamento inválida',
        ];
    }

    public function attributes(): array
    {
        return [
            'valor' => 'valor',
            'data_pagamento' => 'data de pagamento',
            'forma_pagamento_id' => 'forma de pagamento',
            'conta_bancaria_id' => 'conta bancária',
            'numero_comprovante' => 'número do comprovante',
            'observacoes' => 'observações',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('valor')) {
            $this->merge([
                'valor' => str_replace(',', '.', str_replace('.', '', $this->valor))
            ]);
        }
    }
}