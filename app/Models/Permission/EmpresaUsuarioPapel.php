<?php

namespace App\Models\Permission;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpresaUsuarioPapel extends Model
{
    use SoftDeletes;

    protected $table = 'empresa_usuario_papeis';

    protected $fillable = [
        'usuario_id',
        'papel_id',
        'empresa_id',
        'atribuido_por',
        'data_atribuicao',
        'data_expiracao',
        'sync_status',
        'sync_data',
        'sync_hash'
    ];

    protected $casts = [
        'data_atribuicao' => 'datetime',
        'data_expiracao' => 'datetime',
        'sync_data' => 'datetime'
    ];

    // Relacionamentos
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User\EmpresaUsuario::class, 'usuario_id');
    }

    public function papel(): BelongsTo
    {
        return $this->belongsTo(EmpresaPapel::class, 'papel_id');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Business\Business::class, 'empresa_id');
    }

    public function atribuidoPor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User\EmpresaUsuario::class, 'atribuido_por');
    }

    // Métodos
    public function isExpired(): bool
    {
        return $this->data_expiracao && $this->data_expiracao->isPast();
    }

    public function isValid(): bool
    {
        return !$this->isExpired();
    }
}
