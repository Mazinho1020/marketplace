<?php

namespace App\Http\Requests\Fidelidade;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ajuste conforme suas regras de autorização
    }

    public function rules(): array
    {
        return [
            'cliente_id' => 'required|exists:funforcli,id',
            'empresa_id' => 'required|exists:empresas,id',
            'tipo' => 'required|in:credito,debito',
            'valor' => 'required|numeric|min:0.01|max:99999.99',
            'descricao' => 'required|string|max:255',
            'pedido_id' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.required' => 'O cliente é obrigatório.',
            'cliente_id.exists' => 'Cliente não encontrado.',
            'empresa_id.required' => 'A empresa é obrigatória.',
            'empresa_id.exists' => 'Empresa não encontrada.',
            'tipo.required' => 'O tipo de transação é obrigatório.',
            'tipo.in' => 'Tipo deve ser crédito ou débito.',
            'valor.required' => 'O valor é obrigatório.',
            'valor.min' => 'O valor deve ser maior que zero.',
            'valor.max' => 'O valor não pode ser maior que R$ 99.999,99.',
            'descricao.required' => 'A descrição é obrigatória.',
            'descricao.max' => 'A descrição não pode ter mais de 255 caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'cliente_id' => 'cliente',
            'empresa_id' => 'empresa',
            'valor' => 'valor',
            'descricao' => 'descrição',
            'pedido_id' => 'ID do pedido',
        ];
    }
}
