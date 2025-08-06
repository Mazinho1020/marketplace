<?php

namespace App\Models\Permission;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpresaPapelPermissao extends Model
{
    use SoftDeletes;

    protected $table = 'empresa_papel_permissoes';

    protected $fillable = [
        'papel_id',
        'permissao_id',
        'empresa_id',
        'sync_status',
        'sync_data',
        'sync_hash'
    ];

    protected $casts = [
        'sync_data' => 'datetime'
    ];

    // Relacionamentos
    public function papel(): BelongsTo
    {
        return $this->belongsTo(EmpresaPapel::class, 'papel_id');
    }

    public function permissao(): BelongsTo
    {
        return $this->belongsTo(EmpresaPermissao::class, 'permissao_id');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Business\Business::class, 'empresa_id');
    }
}
