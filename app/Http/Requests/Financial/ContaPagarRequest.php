<?php

namespace App\Http\Requests\Financial;

use Illuminate\Foundation\Http\FormRequest;

class ContaPagarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Adicionar lógica de autorização conforme necessário
    }

    public function rules(): array
    {
        return [
            'empresa_id' => 'required|integer|exists:empresas,id',
            'pessoa_tipo' => 'required|in:funcionario,fornecedor,cliente',
            'pessoa_id' => 'required|integer',
            'conta_gerencial_id' => 'required|integer|exists:conta_gerencial,id',
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0.01',
            'data_vencimento' => 'required|date|after_or_equal:today',
            'numero_documento' => 'nullable|string|max:100',
            'observacoes' => 'nullable|string|max:1000',
            'data_emissao' => 'nullable|date',
            'data_competencia' => 'nullable|date',
            
            // Parcelamento
            'parcelado' => 'boolean',
            'parcelas' => 'nullable|required_if:parcelado,true|integer|min:2|max:360',
            
            // Configurações
            'config_alertas' => 'nullable|array',
            'config_alertas.*.dias_antes' => 'integer|min:1|max:365',
            'config_alertas.*.tipo_alerta' => 'in:email,sms,notificacao',
            
            'juros_multa_config' => 'nullable|array',
            'juros_multa_config.multa_percentual' => 'nullable|numeric|min:0|max:100',
            'juros_multa_config.juros_dia_percentual' => 'nullable|numeric|min:0|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'empresa_id.required' => 'A empresa é obrigatória',
            'empresa_id.exists' => 'Empresa não encontrada',
            'pessoa_tipo.required' => 'O tipo de pessoa é obrigatório',
            'pessoa_tipo.in' => 'Tipo de pessoa inválido',
            'pessoa_id.required' => 'A pessoa é obrigatória',
            'conta_gerencial_id.required' => 'A conta gerencial é obrigatória',
            'conta_gerencial_id.exists' => 'Conta gerencial não encontrada',
            'descricao.required' => 'A descrição é obrigatória',
            'valor.required' => 'O valor é obrigatório',
            'valor.min' => 'O valor deve ser maior que zero',
            'data_vencimento.required' => 'A data de vencimento é obrigatória',
            'data_vencimento.after_or_equal' => 'A data de vencimento não pode ser anterior a hoje',
            'parcelas.required_if' => 'O número de parcelas é obrigatório quando parcelado',
            'parcelas.min' => 'O número mínimo de parcelas é 2',
            'parcelas.max' => 'O número máximo de parcelas é 360',
        ];
    }

    public function attributes(): array
    {
        return [
            'empresa_id' => 'empresa',
            'pessoa_tipo' => 'tipo de pessoa',
            'pessoa_id' => 'pessoa',
            'conta_gerencial_id' => 'conta gerencial',
            'descricao' => 'descrição',
            'valor' => 'valor',
            'data_vencimento' => 'data de vencimento',
            'numero_documento' => 'número do documento',
            'observacoes' => 'observações',
            'data_emissao' => 'data de emissão',
            'data_competencia' => 'data de competência',
            'parcelas' => 'número de parcelas',
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