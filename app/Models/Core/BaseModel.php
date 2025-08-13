<?php

namespace App\Models\Core;

use App\Enums\SyncStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model
{
    use SoftDeletes;

    /**
     * Campos que podem ser assignados em massa
     */
    protected $guarded = ['id'];

    /**
     * Casts dos campos
     */
    protected $casts = [
        'sync_status' => SyncStatusEnum::class,
        'sync_data' => 'datetime',
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Campos hidden por padrão
     */
    protected $hidden = [
        'sync_hash',
        'sync_data',
        'deleted_at',
    ];

    /**
     * Scope para registros ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para registros inativos
     */
    public function scopeInativos($query)
    {
        return $query->where('ativo', false);
    }

    /**
     * Scope para ordenar por nome
     */
    public function scopeOrdenado($query, string $campo = 'nome', string $direcao = 'asc')
    {
        return $query->orderBy($campo, $direcao);
    }

    /**
     * Scope para busca geral
     */
    public function scopeBuscar($query, string $termo, array $campos = ['nome'])
    {
        return $query->where(function ($q) use ($termo, $campos) {
            foreach ($campos as $campo) {
                $q->orWhere($campo, 'like', "%{$termo}%");
            }
        });
    }

    /**
     * Retorna o status formatado
     */
    public function getStatusFormatadoAttribute(): string
    {
        return $this->ativo ? 'Ativo' : 'Inativo';
    }

    /**
     * Retorna a classe CSS do status
     */
    public function getStatusClasseAttribute(): string
    {
        return $this->ativo ? 'badge-success' : 'badge-secondary';
    }
}
