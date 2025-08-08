<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProdutoMarca extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'empresa_id',
        'nome',
        'descricao',
        'logo',
        'site',
        'telefone',
        'email',
        'ativo',
        'sync_status'
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function produtos()
    {
        return $this->hasMany(Produto::class, 'marca_id');
    }

    // Scopes
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }
}
