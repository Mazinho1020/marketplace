<?php

namespace App\Http\Requests\Vendas;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Venda;

class UpdateVendaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Autorização será controlada via middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cliente_id' => 'nullable|exists:pessoas,id',
            'vendedor_id' => 'nullable|exists:empresa_usuarios,id',
            'caixa_id' => 'nullable|integer',
            'codigo_venda' => 'nullable|string|max:100',
            'tipo_venda' => 'sometimes|in:' . implode(',', array_keys(Venda::TIPOS_VENDA)),
            'origem_venda' => 'sometimes|in:' . implode(',', array_keys(Venda::ORIGENS_VENDA)),
            'tipo_entrega' => 'nullable|in:' . implode(',', array_keys(Venda::TIPOS_ENTREGA)),
            'observacoes' => 'nullable|string|max:1000',
            'observacoes_internas' => 'nullable|string|max:1000',
            'dados_entrega' => 'nullable|array',
            'dados_entrega.endereco' => 'nullable|string|max:255',
            'dados_entrega.numero' => 'nullable|string|max:20',
            'dados_entrega.complemento' => 'nullable|string|max:100',
            'dados_entrega.bairro' => 'nullable|string|max:100',
            'dados_entrega.cidade' => 'nullable|string|max:100',
            'dados_entrega.uf' => 'nullable|string|size:2',
            'dados_entrega.cep' => 'nullable|string|max:10',
            'tempo_estimado_entrega' => 'nullable|numeric|min:0',
            'canal_venda' => 'nullable|string|max:100',
            'valor_desconto' => 'nullable|numeric|min:0',
            'valor_acrescimo' => 'nullable|numeric|min:0',
            'valor_frete' => 'nullable|numeric|min:0',
            'valor_taxa_servico' => 'nullable|numeric|min:0',
            'aliquota_comissao' => 'nullable|numeric|min:0|max:100',
            
            // Validação dos itens (opcional para atualização)
            'itens' => 'nullable|array',
            'itens.*.id' => 'nullable|exists:venda_itens,id',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.produto_variacao_id' => 'nullable|integer',
            'itens.*.quantidade' => 'required|numeric|min:0.001',
            'itens.*.valor_unitario' => 'required|numeric|min:0',
            'itens.*.observacoes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'cliente_id.exists' => 'O cliente selecionado não existe.',
            'vendedor_id.exists' => 'O vendedor selecionado não existe.',
            'tipo_venda.in' => 'O tipo de venda deve ser um dos valores válidos.',
            'origem_venda.in' => 'A origem da venda deve ser um dos valores válidos.',
            'tipo_entrega.in' => 'O tipo de entrega deve ser um dos valores válidos.',
            'observacoes.max' => 'As observações não podem ter mais de 1000 caracteres.',
            'observacoes_internas.max' => 'As observações internas não podem ter mais de 1000 caracteres.',
            'tempo_estimado_entrega.numeric' => 'O tempo estimado de entrega deve ser um número.',
            'tempo_estimado_entrega.min' => 'O tempo estimado de entrega deve ser positivo.',
            'valor_desconto.numeric' => 'O valor de desconto deve ser um número.',
            'valor_desconto.min' => 'O valor de desconto não pode ser negativo.',
            'valor_acrescimo.numeric' => 'O valor de acréscimo deve ser um número.',
            'valor_acrescimo.min' => 'O valor de acréscimo não pode ser negativo.',
            'valor_frete.numeric' => 'O valor do frete deve ser um número.',
            'valor_frete.min' => 'O valor do frete não pode ser negativo.',
            'valor_taxa_servico.numeric' => 'O valor da taxa de serviço deve ser um número.',
            'valor_taxa_servico.min' => 'O valor da taxa de serviço não pode ser negativo.',
            'aliquota_comissao.numeric' => 'A alíquota de comissão deve ser um número.',
            'aliquota_comissao.min' => 'A alíquota de comissão não pode ser negativa.',
            'aliquota_comissao.max' => 'A alíquota de comissão não pode ser maior que 100%.',
            
            // Mensagens dos itens
            'itens.array' => 'Os itens devem ser uma lista.',
            'itens.*.id.exists' => 'Um dos itens selecionados não existe.',
            'itens.*.produto_id.required' => 'O produto é obrigatório para cada item.',
            'itens.*.produto_id.exists' => 'Um dos produtos selecionados não existe.',
            'itens.*.quantidade.required' => 'A quantidade é obrigatória para cada item.',
            'itens.*.quantidade.numeric' => 'A quantidade deve ser um número.',
            'itens.*.quantidade.min' => 'A quantidade deve ser maior que zero.',
            'itens.*.valor_unitario.required' => 'O valor unitário é obrigatório para cada item.',
            'itens.*.valor_unitario.numeric' => 'O valor unitário deve ser um número.',
            'itens.*.valor_unitario.min' => 'O valor unitário não pode ser negativo.',
            'itens.*.observacoes.max' => 'As observações do item não podem ter mais de 500 caracteres.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'cliente_id' => 'cliente',
            'vendedor_id' => 'vendedor',
            'caixa_id' => 'caixa',
            'codigo_venda' => 'código da venda',
            'tipo_venda' => 'tipo de venda',
            'origem_venda' => 'origem da venda',
            'tipo_entrega' => 'tipo de entrega',
            'observacoes' => 'observações',
            'observacoes_internas' => 'observações internas',
            'tempo_estimado_entrega' => 'tempo estimado de entrega',
            'canal_venda' => 'canal da venda',
            'valor_desconto' => 'valor de desconto',
            'valor_acrescimo' => 'valor de acréscimo',
            'valor_frete' => 'valor do frete',
            'valor_taxa_servico' => 'valor da taxa de serviço',
            'aliquota_comissao' => 'alíquota de comissão',
            'itens' => 'itens',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validações customizadas adicionais
            $this->validateCustomRules($validator);
        });
    }

    /**
     * Validações customizadas
     */
    protected function validateCustomRules($validator)
    {
        $venda = $this->route('venda');
        
        // Verificar se a venda pode ser editada
        if ($venda && $venda->status !== 'aberta') {
            $validator->errors()->add('status', 'Apenas vendas abertas podem ser editadas.');
            return;
        }

        // Validar se o cliente pertence à empresa
        if ($this->cliente_id && $venda) {
            $cliente = \App\Models\Cliente::where('id', $this->cliente_id)
                ->where('empresa_id', $venda->empresa_id)
                ->first();
            
            if (!$cliente) {
                $validator->errors()->add('cliente_id', 'O cliente selecionado não pertence a esta empresa.');
            }
        }

        // Validar se o vendedor pertence à empresa
        if ($this->vendedor_id && $venda) {
            $vendedor = \App\Models\User::whereHas('empresas', function ($query) use ($venda) {
                    $query->where('empresa_id', $venda->empresa_id);
                })->find($this->vendedor_id);
            
            if (!$vendedor) {
                $validator->errors()->add('vendedor_id', 'O vendedor selecionado não pertence a esta empresa.');
            }
        }

        // Validar dados de entrega se tipo de entrega é delivery
        if ($this->tipo_entrega === 'delivery') {
            if (!$this->dados_entrega || empty($this->dados_entrega['endereco'])) {
                $validator->errors()->add('dados_entrega.endereco', 'Endereço é obrigatório para delivery.');
            }
        }

        // Validar estoque dos produtos (apenas para novos itens ou alteração de quantidade)
        if ($this->itens && is_array($this->itens)) {
            foreach ($this->itens as $index => $item) {
                if (isset($item['produto_id'])) {
                    $produto = \App\Models\Produto::find($item['produto_id']);
                    if ($produto && $produto->controla_estoque) {
                        $quantidadeAtual = 0;
                        
                        // Se é um item existente, considerar a quantidade já alocada
                        if (!empty($item['id'])) {
                            $itemExistente = \App\Models\VendaItem::find($item['id']);
                            if ($itemExistente) {
                                $quantidadeAtual = $itemExistente->quantidade;
                            }
                        }
                        
                        $novaQuantidade = $item['quantidade'] ?? 0;
                        $diferencaQuantidade = $novaQuantidade - $quantidadeAtual;
                        
                        if ($diferencaQuantidade > $produto->estoque_atual) {
                            $validator->errors()->add(
                                "itens.{$index}.quantidade",
                                "Estoque insuficiente para o produto {$produto->nome}. Estoque disponível: {$produto->estoque_atual}"
                            );
                        }
                    }
                }
            }
        }
    }
}
