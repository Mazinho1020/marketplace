<?php

namespace App\Http\Requests\Vendas;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendaRequest extends FormRequest
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
            'tipo_venda' => 'required|in:balcao,delivery,mesa,online,whatsapp',
            'origem' => 'required|in:pdv,manual,delivery,api,whatsapp',
            'caixa_id' => 'nullable|integer',
            'mesa_id' => 'nullable|integer',
            'desconto_percentual' => 'nullable|numeric|min:0|max:100',
            'desconto_valor' => 'nullable|numeric|min:0',
            'acrescimo_percentual' => 'nullable|numeric|min:0|max:100',
            'acrescimo_valor' => 'nullable|numeric|min:0',
            'observacoes' => 'nullable|string|max:1000',
            'observacoes_internas' => 'nullable|string|max:1000',
            'cupom_desconto' => 'nullable|string|max:50',
            'data_entrega_prevista' => 'nullable|date|after:now',
            'dados_entrega' => 'nullable|array',
            'dados_entrega.endereco' => 'nullable|string|max:255',
            'dados_entrega.bairro' => 'nullable|string|max:100',
            'dados_entrega.cidade' => 'nullable|string|max:100',
            'dados_entrega.cep' => 'nullable|string|max:10',
            'dados_entrega.telefone' => 'nullable|string|max:20',
            'dados_entrega.observacoes' => 'nullable|string|max:500',
            'metadados' => 'nullable|array',
            
            // Itens da venda
            'itens' => 'required|array|min:1',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.produto_variacao_id' => 'nullable|exists:produto_variacao_combinacoes,id',
            'itens.*.quantidade' => 'required|numeric|min:0.01',
            'itens.*.valor_unitario' => 'nullable|numeric|min:0',
            'itens.*.desconto_percentual' => 'nullable|numeric|min:0|max:100',
            'itens.*.desconto_valor' => 'nullable|numeric|min:0',
            'itens.*.observacoes' => 'nullable|string|max:500',
            'itens.*.configuracoes' => 'nullable|array',
            'itens.*.personalizacoes' => 'nullable|array',
            
            // Pagamentos (opcionais na criação)
            'pagamentos' => 'nullable|array',
            'pagamentos.*.forma_pagamento_id' => 'required|exists:formas_pagamento,id',
            'pagamentos.*.bandeira_id' => 'nullable|exists:forma_pag_bandeiras,id',
            'pagamentos.*.valor_pagamento' => 'required|numeric|min:0.01',
            'pagamentos.*.parcelas' => 'nullable|integer|min:1|max:12',
            'pagamentos.*.data_pagamento' => 'nullable|date',
            'pagamentos.*.observacoes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom error messages for validator.
     */
    public function messages(): array
    {
        return [
            'cliente_id.exists' => 'Cliente não encontrado.',
            'tipo_venda.required' => 'O tipo de venda é obrigatório.',
            'tipo_venda.in' => 'Tipo de venda inválido.',
            'origem.required' => 'A origem da venda é obrigatória.',
            'origem.in' => 'Origem da venda inválida.',
            'desconto_percentual.max' => 'O desconto não pode ser superior a 100%.',
            'acrescimo_percentual.max' => 'O acréscimo não pode ser superior a 100%.',
            'observacoes.max' => 'As observações não podem ter mais de 1000 caracteres.',
            'data_entrega_prevista.after' => 'A data de entrega deve ser futura.',
            
            // Mensagens para itens
            'itens.required' => 'É necessário incluir pelo menos um item na venda.',
            'itens.min' => 'É necessário incluir pelo menos um item na venda.',
            'itens.*.produto_id.required' => 'O produto é obrigatório para todos os itens.',
            'itens.*.produto_id.exists' => 'Produto não encontrado.',
            'itens.*.quantidade.required' => 'A quantidade é obrigatória para todos os itens.',
            'itens.*.quantidade.min' => 'A quantidade deve ser maior que zero.',
            'itens.*.valor_unitario.min' => 'O valor unitário deve ser maior ou igual a zero.',
            'itens.*.desconto_percentual.max' => 'O desconto do item não pode ser superior a 100%.',
            
            // Mensagens para pagamentos
            'pagamentos.*.forma_pagamento_id.required' => 'A forma de pagamento é obrigatória.',
            'pagamentos.*.forma_pagamento_id.exists' => 'Forma de pagamento não encontrada.',
            'pagamentos.*.valor_pagamento.required' => 'O valor do pagamento é obrigatório.',
            'pagamentos.*.valor_pagamento.min' => 'O valor do pagamento deve ser maior que zero.',
            'pagamentos.*.parcelas.max' => 'Máximo de 12 parcelas permitidas.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'cliente_id' => 'cliente',
            'tipo_venda' => 'tipo de venda',
            'origem' => 'origem',
            'desconto_percentual' => 'desconto percentual',
            'desconto_valor' => 'valor do desconto',
            'acrescimo_percentual' => 'acréscimo percentual',
            'acrescimo_valor' => 'valor do acréscimo',
            'observacoes' => 'observações',
            'data_entrega_prevista' => 'data de entrega prevista',
            'itens' => 'itens da venda',
            'itens.*.produto_id' => 'produto',
            'itens.*.quantidade' => 'quantidade',
            'itens.*.valor_unitario' => 'valor unitário',
            'pagamentos' => 'pagamentos',
            'pagamentos.*.forma_pagamento_id' => 'forma de pagamento',
            'pagamentos.*.valor_pagamento' => 'valor do pagamento',
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

        // Definir empresa_id do usuário logado
        if (auth()->check() && auth()->user()->empresa_id) {
            $this->merge([
                'empresa_id' => auth()->user()->empresa_id,
                'usuario_id' => auth()->id()
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