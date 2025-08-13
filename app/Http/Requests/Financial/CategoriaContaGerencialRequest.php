<?php

namespace App\Http\Requests\Financial;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoriaContaGerencialRequest extends FormRequest
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
        $categoriaId = $this->route('categoria_conta')?->id ?? $this->route('id');

        return [
            'nome' => [
                'required',
                'string',
                'max:50',
                Rule::unique('categorias_conta', 'nome')
                    ->ignore($categoriaId)
                    ->where('empresa_id', $this->user()->empresa_id ?? null)
            ],
            'nome_completo' => 'required|string|max:100',
            'descricao' => 'nullable|string|max:1000',
            'cor' => [
                'nullable',
                'string',
                'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
                'max:7'
            ],
            'icone' => 'nullable|string|max:50',
            'e_custo' => 'boolean',
            'e_despesa' => 'boolean',
            'e_receita' => 'boolean',
            'ativo' => 'boolean',
            'empresa_id' => 'nullable|integer|exists:empresas,id',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'O nome da categoria é obrigatório.',
            'nome.unique' => 'Este nome já está sendo usado por outra categoria.',
            'nome.max' => 'O nome não pode ter mais de 50 caracteres.',
            'nome_completo.required' => 'O nome completo da categoria é obrigatório.',
            'nome_completo.max' => 'O nome completo não pode ter mais de 100 caracteres.',
            'descricao.max' => 'A descrição não pode ter mais de 1000 caracteres.',
            'cor.regex' => 'A cor deve estar no formato hexadecimal (#FFFFFF ou #FFF).',
            'cor.max' => 'A cor não pode ter mais de 7 caracteres.',
            'icone.max' => 'O ícone não pode ter mais de 50 caracteres.',
            'empresa_id.exists' => 'A empresa selecionada não existe.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nome' => 'nome',
            'nome_completo' => 'nome completo',
            'descricao' => 'descrição',
            'cor' => 'cor',
            'icone' => 'ícone',
            'e_custo' => 'é custo',
            'e_despesa' => 'é despesa',
            'e_receita' => 'é receita',
            'ativo' => 'ativo',
            'empresa_id' => 'empresa',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Converte strings vazias em null
        $this->merge([
            'descricao' => $this->descricao ?: null,
            'cor' => $this->cor ?: '#007bff',
            'icone' => $this->icone ?: null,
            'empresa_id' => $this->empresa_id ?: null,
        ]);

        // Se nome_completo não foi informado, usar nome
        if (!$this->nome_completo && $this->nome) {
            $this->merge(['nome_completo' => $this->nome]);
        }

        // Define empresa_id automaticamente se não informado
        if (!$this->empresa_id && $this->user()?->empresa_id) {
            $this->merge(['empresa_id' => $this->user()->empresa_id]);
        }

        // Garantir que pelo menos um tipo seja selecionado
        if (!$this->e_custo && !$this->e_despesa && !$this->e_receita) {
            $this->merge(['e_despesa' => true]); // Padrão para despesa
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validar que pelo menos um tipo foi selecionado
            if (!$this->e_custo && !$this->e_despesa && !$this->e_receita) {
                $validator->errors()->add(
                    'tipo',
                    'Selecione pelo menos um tipo: Custo, Despesa ou Receita.'
                );
            }
        });
    }
}
