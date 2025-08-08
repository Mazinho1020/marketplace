<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'pessoas';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'empresa_id',
        'codigo',
        'tipo',
        'nome',
        'sobrenome',
        'nome_social',
        'data_nascimento',
        'cpf_cnpj',
        'rg',
        'orgao_emissor',
        'estado_civil',
        'genero',
        'telefone',
        'whatsapp',
        'email',
        'email_secundario',
        'status',
        'observacoes',
        'foto_url',
        'limite_credito',
        'limite_fiado',
        'prazo_pagamento_padrao',
        'rating',
        'categoria_id'
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'tipo' => 'array',
        'pessoa_juridica' => 'boolean',
        'limite_credito' => 'decimal:2',
        'limite_fiado' => 'decimal:2'
    ];

    // Accessor para padronizar o nome
    public function getNameAttribute()
    {
        return $this->nome;
    }

    // Accessor para compatibilidade com cpf
    public function getCpfAttribute()
    {
        return $this->cpf_cnpj;
    }

    // Mutator para compatibilidade com cpf
    public function setCpfAttribute($value)
    {
        $this->attributes['cpf_cnpj'] = $value;
    }

    // Accessor para compatibilidade com campo ativo
    public function getAtivoAttribute()
    {
        return $this->status === 'ativo' ? 1 : 0;
    }

    // Scope para filtrar apenas clientes
    public function scopeClientes($query)
    {
        return $query->where(function ($q) {
            $q->where('tipo', 'like', '%cliente%')
                ->orWhere('tipo', 'like', '%funcionario%');
        });
    }

    // Scope para clientes ativos
    public function scopeAtivos($query)
    {
        return $query->where('status', 'ativo');
    }
}
