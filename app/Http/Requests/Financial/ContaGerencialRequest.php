<?php

namespace App\Http\Requests\Financial;

use App\Enums\NaturezaContaEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContaGerencialRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Ajustar conforme suas regras de autorização
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $contaId = $this->route('conta_gerencial')?->id ?? $this->route('id');

        return [
            'codigo' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('conta_gerencial', 'codigo')
                    ->ignore($contaId)
                    ->where('empresa_id', $this->user()->empresa_id ?? null)
            ],
            'conta_pai_id' => [
                'nullable',
                'integer',
                'exists:conta_gerencial,id',
                function ($attribute, $value, $fail) use ($contaId) {
                    if ($value == $contaId) {
                        $fail('Uma conta não pode ser pai de si mesma.');
                    }
                }
            ],
            'nivel' => 'nullable|integer|min:1|max:10',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:255',
            'ativo' => 'boolean',
            'ordem_exibicao' => 'integer|min:0',
            'permite_lancamento' => 'boolean',
            'natureza' => [
                'nullable',
                Rule::in(array_column(NaturezaContaEnum::cases(), 'value'))
            ],
            'configuracoes' => 'nullable|array',
            'usuario_id' => 'nullable|integer|exists:users,id',
            'empresa_id' => 'nullable|integer|exists:empresas,id',
            'classificacao_dre_id' => 'nullable|integer|exists:classificacoes_dre,id',
            'tipo_id' => 'nullable|integer|exists:tipo,id',
            'categoria_id' => 'nullable|integer|exists:categorias_conta,id',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'codigo.unique' => 'Este código já está sendo usado por outra conta.',
            'conta_pai_id.exists' => 'A conta pai selecionada não existe.',
            'nome.required' => 'O nome da conta é obrigatório.',
            'nome.max' => 'O nome da conta não pode ter mais de 255 caracteres.',
            'descricao.max' => 'A descrição não pode ter mais de 255 caracteres.',
            'nivel.min' => 'O nível deve ser no mínimo 1.',
            'nivel.max' => 'O nível não pode ser maior que 10.',
            'ordem_exibicao.min' => 'A ordem de exibição deve ser no mínimo 0.',
            'natureza.in' => 'A natureza da conta deve ser débito ou crédito.',
            'usuario_id.exists' => 'O usuário selecionado não existe.',
            'empresa_id.exists' => 'A empresa selecionada não existe.',
            'classificacao_dre_id.exists' => 'A classificação DRE selecionada não existe.',
            'tipo_id.exists' => 'O tipo selecionado não existe.',
            'categoria_id.exists' => 'A categoria selecionada não existe.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'codigo' => 'código',
            'conta_pai_id' => 'conta pai',
            'nivel' => 'nível',
            'nome' => 'nome',
            'descricao' => 'descrição',
            'ativo' => 'ativo',
            'ordem_exibicao' => 'ordem de exibição',
            'permite_lancamento' => 'permite lançamento',
            'natureza' => 'natureza da conta',
            'configuracoes' => 'configurações',
            'usuario_id' => 'usuário',
            'empresa_id' => 'empresa',
            'classificacao_dre_id' => 'classificação DRE',
            'tipo_id' => 'tipo',
            'categoria_id' => 'categoria',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Converte strings vazias em null
        $this->merge([
            'codigo' => $this->codigo ?: null,
            'conta_pai_id' => $this->conta_pai_id ?: null,
            'nivel' => $this->nivel ?: null,
            'descricao' => $this->descricao ?: null,
            'natureza' => $this->natureza ?: null,
            'usuario_id' => $this->usuario_id ?: null,
            'empresa_id' => $this->empresa_id ?: null,
            'classificacao_dre_id' => $this->classificacao_dre_id ?: null,
            'tipo_id' => $this->tipo_id ?: null,
            'categoria_id' => $this->categoria_id ?: null,
        ]);

        // Define empresa_id automaticamente se não informado
        if (!$this->empresa_id && $this->user()?->empresa_id) {
            $this->merge(['empresa_id' => $this->user()->empresa_id]);
        }

        // Define usuario_id automaticamente se não informado
        if (!$this->usuario_id && $this->user()?->id) {
            $this->merge(['usuario_id' => $this->user()->id]);
        }
    }
}
