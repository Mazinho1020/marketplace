<?php

namespace App\Http\Requests\Financial;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContaGerencialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $contaId = $this->route('conta_gerencial');
        
        return [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:255',
            'codigo' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('conta_gerencial', 'codigo')
                    ->where('empresa_id', auth()->user()->empresa_id ?? 1)
                    ->ignore($contaId)
            ],
            'conta_pai_id' => 'nullable|exists:conta_gerencial,id',
            'nivel' => 'integer|min:1|max:10',
            'ativo' => 'boolean',
            'ordem_exibicao' => 'integer|min:0',
            'permite_lancamento' => 'boolean',
            'natureza_conta' => 'nullable|in:debito,credito',
            'configuracoes' => 'nullable|array',
            'classificacao_dre_id' => 'nullable|exists:classificacoes_dre,id',
            'tipo_id' => 'nullable|exists:tipo,id',
            'naturezas' => 'nullable|array',
            'naturezas.*' => 'exists:conta_gerencial_natureza,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome da conta é obrigatório',
            'codigo.unique' => 'Este código já está em uso para esta empresa',
            'conta_pai_id.exists' => 'A conta pai selecionada não existe',
            'classificacao_dre_id.exists' => 'A classificação DRE selecionada não existe',
            'tipo_id.exists' => 'O tipo selecionado não existe',
            'naturezas.*.exists' => 'Uma ou mais naturezas selecionadas não existem',
        ];
    }
}