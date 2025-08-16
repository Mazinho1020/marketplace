<?php

namespace App\Http\Requests\Vendas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVendaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Autorização será gerenciada pelo middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'cliente_id' => 'nullable|exists:pessoas,id',
            'status_venda' => 'nullable|in:orcamento,pendente,confirmada,paga,entregue,finalizada,cancelada',
            'status_pagamento' => 'nullable|in:pendente,parcial,pago,estornado',
            'status_entrega' => 'nullable|in:pendente,preparando,pronto,saiu_entrega,entregue,cancelado',
            'desconto_percentual' => 'nullable|numeric|min:0|max:100',
            'desconto_valor' => 'nullable|numeric|min:0',
            'acrescimo_percentual' => 'nullable|numeric|min:0|max:100',
            'acrescimo_valor' => 'nullable|numeric|min:0',
            'observacoes' => 'nullable|string|max:1000',
            'observacoes_internas' => 'nullable|string|max:1000',
            'cupom_desconto' => 'nullable|string|max:50',
            'data_entrega_prevista' => 'nullable|date',
            'data_entrega_realizada' => 'nullable|date',
            'dados_entrega' => 'nullable|array',
            'dados_entrega.endereco' => 'nullable|string|max:255',
            'dados_entrega.bairro' => 'nullable|string|max:100',
            'dados_entrega.cidade' => 'nullable|string|max:100',
            'dados_entrega.cep' => 'nullable|string|max:10',
            'dados_entrega.telefone' => 'nullable|string|max:20',
            'dados_entrega.observacoes' => 'nullable|string|max:500',
            'metadados' => 'nullable|array',
            
            // Nota fiscal
            'nf_numero' => 'nullable|string|max:50',
            'nf_chave' => 'nullable|string|max:44',
            'nf_data_emissao' => 'nullable|date',
        ];
    }

    /**
     * Get custom error messages for validator.
     */
    public function messages(): array
    {
        return [
            'cliente_id.exists' => 'Cliente não encontrado.',
            'status_venda.in' => 'Status da venda inválido.',
            'status_pagamento.in' => 'Status do pagamento inválido.',
            'status_entrega.in' => 'Status da entrega inválido.',
            'desconto_percentual.max' => 'O desconto não pode ser superior a 100%.',
            'acrescimo_percentual.max' => 'O acréscimo não pode ser superior a 100%.',
            'observacoes.max' => 'As observações não podem ter mais de 1000 caracteres.',
            'observacoes_internas.max' => 'As observações internas não podem ter mais de 1000 caracteres.',
            'nf_numero.max' => 'O número da nota fiscal não pode ter mais de 50 caracteres.',
            'nf_chave.max' => 'A chave da nota fiscal deve ter exatamente 44 caracteres.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'cliente_id' => 'cliente',
            'status_venda' => 'status da venda',
            'status_pagamento' => 'status do pagamento',
            'status_entrega' => 'status da entrega',
            'desconto_percentual' => 'desconto percentual',
            'desconto_valor' => 'valor do desconto',
            'acrescimo_percentual' => 'acréscimo percentual',
            'acrescimo_valor' => 'valor do acréscimo',
            'observacoes' => 'observações',
            'observacoes_internas' => 'observações internas',
            'data_entrega_prevista' => 'data de entrega prevista',
            'data_entrega_realizada' => 'data de entrega realizada',
            'nf_numero' => 'número da nota fiscal',
            'nf_chave' => 'chave da nota fiscal',
            'nf_data_emissao' => 'data de emissão da nota fiscal',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Limpar e formatar dados se necessário
        if ($this->has('dados_entrega.cep')) {
            $this->merge([
                'dados_entrega' => array_merge($this->dados_entrega ?? [], [
                    'cep' => preg_replace('/[^0-9]/', '', $this->dados_entrega['cep'] ?? '')
                ])
            ]);
        }

        // Limpar chave da NF
        if ($this->has('nf_chave')) {
            $this->merge([
                'nf_chave' => preg_replace('/[^0-9]/', '', $this->nf_chave)
            ]);
        }
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation($validator)
    {
        if ($this->wantsJson()) {
            $response = response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $validator->errors()
            ], 422);

            throw new \Illuminate\Validation\ValidationException($validator, $response);
        }

        parent::failedValidation($validator);
    }
}