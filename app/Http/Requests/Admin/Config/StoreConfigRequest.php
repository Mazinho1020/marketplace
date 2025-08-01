<?php

namespace App\Http\Requests\Admin\Config;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Form Request para criação de configurações
 */
class StoreConfigRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->empresa_id;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'chave' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z_][a-z0-9_]*$/',
                'unique:config_definitions,chave,NULL,id,empresa_id,' . Auth::user()->empresa_id
            ],
            'descricao' => 'nullable|string|max:1000',
            'tipo' => 'required|in:string,integer,float,boolean,array,json,date,datetime',
            'grupo_id' => 'required|exists:config_groups,id',
            'valor_padrao' => 'nullable|string',
            'valor_inicial' => 'nullable|string',
            'obrigatorio' => 'boolean',
            'validacao' => 'nullable|string|max:255',
            'opcoes' => 'nullable|array',
            'visivel_admin' => 'boolean',
            'editavel' => 'boolean',
            'avancado' => 'boolean',
            'ordem' => 'integer|min:0',
            'dica' => 'nullable|string|max:500',
            // 'site_id' => 'nullable|exists:config_sites,id', // Removido - config_sites não mais usado
            'ambiente_id' => 'nullable|exists:config_environments,id',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'chave' => 'Chave',
            'descricao' => 'Descrição',
            'tipo' => 'Tipo de Dado',
            'grupo_id' => 'Grupo',
            'valor_padrao' => 'Valor Padrão',
            'valor_inicial' => 'Valor Inicial',
            'obrigatorio' => 'Obrigatório',
            'validacao' => 'Validação',
            'opcoes' => 'Opções',
            'visivel_admin' => 'Visível no Admin',
            'editavel' => 'Editável',
            'avancado' => 'Configuração Avançada',
            'ordem' => 'Ordem',
            'dica' => 'Dica de Ajuda',
            'site_id' => 'Site',
            'ambiente_id' => 'Ambiente',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'chave.required' => 'A chave da configuração é obrigatória.',
            'chave.regex' => 'A chave deve conter apenas letras minúsculas, números e underscores, começando com letra ou underscore.',
            'chave.unique' => 'Já existe uma configuração com esta chave.',
            'tipo.required' => 'O tipo de dado é obrigatório.',
            'tipo.in' => 'Tipo de dado inválido.',
            'grupo_id.required' => 'O grupo é obrigatório.',
            'grupo_id.exists' => 'Grupo inválido.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Limpar e formatar chave
        if ($this->has('chave')) {
            $this->merge([
                'chave' => strtolower(trim($this->chave))
            ]);
        }

        // Converter checkboxes para boolean
        $this->merge([
            'obrigatorio' => $this->boolean('obrigatorio'),
            'visivel_admin' => $this->boolean('visivel_admin', true),
            'editavel' => $this->boolean('editavel', true),
            'avancado' => $this->boolean('avancado'),
        ]);

        // Processar opções se for array
        if ($this->has('opcoes') && is_string($this->opcoes)) {
            try {
                $opcoes = json_decode($this->opcoes, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->merge(['opcoes' => $opcoes]);
                }
            } catch (\Exception $e) {
                // Manter valor original se não conseguir decodificar
            }
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validar valor padrão baseado no tipo
            if ($this->has('valor_padrao') && $this->valor_padrao !== null) {
                $this->validateValueByType($validator, 'valor_padrao', $this->valor_padrao, $this->tipo);
            }

            // Validar valor inicial baseado no tipo
            if ($this->has('valor_inicial') && $this->valor_inicial !== null) {
                $this->validateValueByType($validator, 'valor_inicial', $this->valor_inicial, $this->tipo);
            }

            // Validar se grupo pertence à empresa do usuário
            if ($this->has('grupo_id')) {
                $group = \App\Models\Config\ConfigGroup::find($this->grupo_id);
                if ($group && $group->empresa_id !== Auth::user()->empresa_id) {
                    $validator->errors()->add('grupo_id', 'Grupo inválido para sua empresa.');
                }
            }
        });
    }

    /**
     * Valida um valor baseado no tipo de dados
     */
    private function validateValueByType($validator, string $field, $value, string $type): void
    {
        switch ($type) {
            case 'integer':
                if (!is_numeric($value) || !ctype_digit(str_replace(['+', '-'], '', $value))) {
                    $validator->errors()->add($field, 'O valor deve ser um número inteiro.');
                }
                break;

            case 'float':
                if (!is_numeric($value)) {
                    $validator->errors()->add($field, 'O valor deve ser um número decimal.');
                }
                break;

            case 'boolean':
                if (!in_array(strtolower($value), ['true', 'false', '1', '0', 'sim', 'não', 'yes', 'no'])) {
                    $validator->errors()->add($field, 'O valor deve ser verdadeiro ou falso.');
                }
                break;

            case 'json':
                if (!is_array($value)) {
                    json_decode($value);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $validator->errors()->add($field, 'O valor deve ser um JSON válido.');
                    }
                }
                break;

            case 'date':
                try {
                    \Carbon\Carbon::createFromFormat('Y-m-d', $value);
                } catch (\Exception $e) {
                    $validator->errors()->add($field, 'O valor deve ser uma data válida (YYYY-MM-DD).');
                }
                break;

            case 'datetime':
                try {
                    \Carbon\Carbon::parse($value);
                } catch (\Exception $e) {
                    $validator->errors()->add($field, 'O valor deve ser uma data/hora válida.');
                }
                break;
        }
    }
}
