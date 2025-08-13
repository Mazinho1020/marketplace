<?php

namespace App\Http\Requests\Financial;

use Illuminate\Foundation\Http\FormRequest;

class ClassificacaoDreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'codigo' => 'nullable|string|max:20',
            'classificacao_pai_id' => 'nullable|exists:classificacoes_dre,id',