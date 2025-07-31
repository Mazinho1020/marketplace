<?php

namespace App\Models\Fidelidade;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FichaTecnicaCategoria extends Model
{
    use SoftDeletes;

    protected $table = 'ficha_tecnica_categorias';

    protected $fillable = [
        'nome',
        'empresa_id',
        'codigo_sistema',
        'sync_hash',
        'sync_status',
        'sync_data'
    ];

    protected $casts = [
        'sync_data' => 'datetime'
    ];

    const SYNC_STATUS = [
        'pendente' => 'Pendente',
        'sincronizado' => 'Sincronizado',
        'erro' => 'Erro'
    ];

    /**
     * Relacionamento com empresa
     */
    public function empresa()
    {
        return $this->belongsTo(\App\Models\Business::class, 'empresa_id');
    }

    /**
     * Scope para filtrar por status de sincronização
     */
    public function scopeComSyncStatus($query, $status)
    {
        return $query->where('sync_status', $status);
    }

    /**
     * Scope para filtrar por empresa
     */
    public function scopeDaEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }
}
