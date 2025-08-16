<?php

namespace App\Http\Requests\Financeiro;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Financeiro\Lancamento;

/**
 * Request de validação para Lançamentos Financeiros
 * 
 * Valida todos os dados de entrada para criação e edição de lançamentos
 */
class LancamentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Implementar lógica de autorização conforme necessário
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            // Dados obrigatórios
            'natureza_financeira' => 'required|in:entrada,saida',
            'categoria' => 'required|in:venda,compra,servico,taxa,imposto,transferencia,ajuste,outros',
            'origem' => 'nullable|in:pdv,manual,delivery,api,importacao,recorrencia',
            'valor' => 'required|numeric|min:0.01',
            'descricao' => 'required|string|max:500',
            'data_emissao' => 'required|date',
            'data_competencia' => 'required|date',
            'data_vencimento' => 'required|date',
            
            // Relacionamentos
            'empresa_id' => 'nullable|integer|exists:empresas,id',
            'pessoa_id' => 'nullable|integer',
            'pessoa_tipo' => 'nullable|in:cliente,fornecedor,funcionario,empresa',
            'funcionario_id' => 'nullable|integer',
            'mesa_id' => 'nullable|integer',
            'caixa_id' => 'nullable|integer',
            'tipo_lancamento_id' => 'nullable|integer',
            'conta_gerencial_id' => 'nullable|integer',
            
            // Valores financeiros
            'valor_desconto' => 'nullable|numeric|min:0',
            'valor_acrescimo' => 'nullable|numeric|min:0',
            'valor_juros' => 'nullable|numeric|min:0',
            'valor_multa' => 'nullable|numeric|min:0',
            
            // Informações complementares
            'numero_documento' => 'nullable|string|max:100',
            'observacoes' => 'nullable|string',
            'observacoes_pagamento' => 'nullable|string',
            
            // Parcelamento
                        'total_parcelas' => 'nullable|integer|min:1|max:360',
            'intervalo_parcelas' => 'nullable|integer|min:1|max:365',
            'data_primeiro_vencimento' => 'nullable|date|after_or_equal:today',
            
            // Recorrência
            'e_recorrente' => 'nullable|boolean',
            'frequencia_recorrencia' => 'nullable|in:diaria,semanal,quinzenal,mensal,bimestral,trimestral,semestral,anual',
            'recorrencia_ativa' => 'nullable|boolean',
            
            // Forma de pagamento
            'forma_pagamento_id' => 'nullable|integer',
            'bandeira_id' => 'nullable|integer',
            'conta_bancaria_id' => 'nullable|integer',
            
            // Cobrança automática
            'cobranca_automatica' => 'nullable|boolean',
            'max_tentativas_cobranca' => 'nullable|integer|min:1|max:10',
            
            // Boleto
            'boleto_nosso_numero' => 'nullable|string|max:50',
            'boleto_url' => 'nullable|url',
            'boleto_linha_digitavel' => 'nullable|string|max:54',
            
            // Aprovação
            'status_aprovacao' => 'nullable|in:pendente_aprovacao,aprovado,rejeitado,nao_requer',
            
            // Configurações JSON
            'juros_multa_config' => 'nullable|json',
            'config_desconto' => 'nullable|json',
            'config_alertas' => 'nullable|json',
            'anexos' => 'nullable|json',
            'metadados' => 'nullable|json',
            
            // Itens do lançamento
            'itens' => 'nullable|array',
            'itens.*.produto_id' => 'required_with:itens|integer',
            'itens.*.produto_variacao_id' => 'nullable|integer',
            'itens.*.quantidade' => 'required_with:itens|numeric|min:0.01',
            'itens.*.valor_unitario' => 'required_with:itens|numeric|min:0.01',
            'itens.*.observacoes' => 'nullable|string',
            'itens.*.metadados' => 'nullable|json',
        ];

        // Regras específicas para atualização
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            // Para atualização, o número do documento deve ser único apenas para a empresa
            // excluindo o próprio registro
            $lancamentoId = $this->route('lancamento');
            
            if ($this->filled('numero_documento')) {
                $rules['numero_documento'] .= '|unique:lancamentos,numero_documento,' . $lancamentoId . ',id,empresa_id,' . $this->input('empresa_id');
            }
        } else {
            // Para criação, verificar unicidade do número do documento
            if ($this->filled('numero_documento')) {
                $rules['numero_documento'] .= '|unique:lancamentos,numero_documento,NULL,id,empresa_id,' . $this->input('empresa_id');
            }
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'natureza_financeira.required' => 'A natureza financeira é obrigatória.',
            'natureza_financeira.in' => 'A natureza financeira deve ser entrada ou saída.',
            
            'categoria.required' => 'A categoria é obrigatória.',
            'categoria.in' => 'A categoria informada não é válida.',
            
            'valor.required' => 'O valor é obrigatório.',
            'valor.numeric' => 'O valor deve ser um número.',
            'valor.min' => 'O valor deve ser maior que zero.',
            
            'descricao.required' => 'A descrição é obrigatória.',
            'descricao.max' => 'A descrição não pode ter mais de 500 caracteres.',
            
            'data_emissao.required' => 'A data de emissão é obrigatória.',
            'data_emissao.date' => 'A data de emissão deve ser uma data válida.',
            
            'data_competencia.required' => 'A data de competência é obrigatória.',
            'data_competencia.date' => 'A data de competência deve ser uma data válida.',
            
            'data_vencimento.required' => 'A data de vencimento é obrigatória.',
            'data_vencimento.date' => 'A data de vencimento deve ser uma data válida.',
            
            'total_parcelas.min' => 'O número de parcelas deve ser no mínimo 1.',
            'total_parcelas.max' => 'O número de parcelas deve ser no máximo 60.',
            
            'intervalo_parcelas.min' => 'O intervalo entre parcelas deve ser no mínimo 1 dia.',
            'intervalo_parcelas.max' => 'O intervalo entre parcelas deve ser no máximo 365 dias.',
            
            'numero_documento.unique' => 'Este número de documento já existe para esta empresa.',
            
            'itens.*.produto_id.required_with' => 'O produto é obrigatório quando há itens.',
            'itens.*.quantidade.required_with' => 'A quantidade é obrigatória quando há itens.',
            'itens.*.quantidade.min' => 'A quantidade deve ser maior que zero.',
            'itens.*.valor_unitario.required_with' => 'O valor unitário é obrigatório quando há itens.',
            'itens.*.valor_unitario.min' => 'O valor unitário deve ser maior que zero.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'natureza_financeira' => 'natureza financeira',
            'categoria' => 'categoria',
            'origem' => 'origem',
            'valor' => 'valor',
            'valor_desconto' => 'valor de desconto',
            'valor_acrescimo' => 'valor de acréscimo',
            'valor_juros' => 'valor de juros',
            'valor_multa' => 'valor de multa',
            'descricao' => 'descrição',
            'data_emissao' => 'data de emissão',
            'data_competencia' => 'data de competência',
            'data_vencimento' => 'data de vencimento',
            'numero_documento' => 'número do documento',
            'observacoes' => 'observações',
            'total_parcelas' => 'número de parcelas',
            'intervalo_parcelas' => 'intervalo entre parcelas',
            'e_recorrente' => 'é recorrente',
            'frequencia_recorrencia' => 'frequência de recorrência',
            'forma_pagamento_id' => 'forma de pagamento',
            'conta_bancaria_id' => 'conta bancária',
            'cobranca_automatica' => 'cobrança automática',
            'status_aprovacao' => 'status de aprovação',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Converter strings booleanas para boolean
        $booleanFields = [
            'e_recorrente',
            'recorrencia_ativa',
            'cobranca_automatica',
        ];

        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
                ]);
            }
        }

        // Garantir valores padrão
        $this->merge([
            'origem' => $this->input('origem', 'manual'),
            'valor_desconto' => $this->input('valor_desconto', 0),
            'valor_acrescimo' => $this->input('valor_acrescimo', 0),
            'valor_juros' => $this->input('valor_juros', 0),
            'valor_multa' => $this->input('valor_multa', 0),
            'total_parcelas' => $this->input('total_parcelas', 1),
            'intervalo_parcelas' => $this->input('intervalo_parcelas', 30),
            'status_aprovacao' => $this->input('status_aprovacao', 'nao_requer'),
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validação customizada para recorrência
            if ($this->input('e_recorrente') && !$this->input('frequencia_recorrencia')) {
                $validator->errors()->add('frequencia_recorrencia', 'A frequência de recorrência é obrigatória quando o lançamento é recorrente.');
            }

            // Validação de datas
            if ($this->input('data_vencimento') && $this->input('data_emissao')) {
                if (strtotime($this->input('data_vencimento')) < strtotime($this->input('data_emissao'))) {
                    $validator->errors()->add('data_vencimento', 'A data de vencimento não pode ser anterior à data de emissão.');
                }
            }

            // Validação de valores
            $valor = (float) $this->input('valor', 0);
            $valorDesconto = (float) $this->input('valor_desconto', 0);
            
            if ($valorDesconto > $valor) {
                $validator->errors()->add('valor_desconto', 'O valor de desconto não pode ser maior que o valor.');
            }

            // Validação de parcelas
            if ($this->input('total_parcelas') > 1) {
                if (!$this->input('intervalo_parcelas')) {
                    $validator->errors()->add('intervalo_parcelas', 'O intervalo entre parcelas é obrigatório quando há mais de uma parcela.');
                }
            }

            // Validação de itens
            if ($this->has('itens') && is_array($this->input('itens'))) {
                $valorTotalItens = 0;
                
                foreach ($this->input('itens') as $index => $item) {
                    $quantidade = (float) ($item['quantidade'] ?? 0);
                    $valorUnitario = (float) ($item['valor_unitario'] ?? 0);
                    $valorDesconto = (float) ($item['valor_desconto_item'] ?? 0);
                    
                    $valorTotalItem = ($quantidade * $valorUnitario) - $valorDesconto;
                    $valorTotalItens += $valorTotalItem;
                    
                    // Validar se o desconto do item não é maior que o valor total do item
                    if ($valorDesconto > ($quantidade * $valorUnitario)) {
                        $validator->errors()->add(
                            "itens.{$index}.valor_desconto_item",
                            'O desconto do item não pode ser maior que o valor total do item.'
                        );
                    }
                }
                
                // Verificar se o valor total dos itens bate com o valor (tolerância de R$ 0,10)
                if (abs($valorTotalItens - $valor) > 0.10) {
                    $validator->errors()->add('valor', 'O valor deve ser igual ao valor total dos itens.');
                }
            }
        });
    }
}
