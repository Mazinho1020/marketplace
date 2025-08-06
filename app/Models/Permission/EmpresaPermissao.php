<?php

namespace App\Models\Permission;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class EmpresaPermissao extends Model
{
    use SoftDeletes;

    protected $table = 'empresa_permissoes';

    protected $fillable = [
        'nome',
        'codigo',
        'descricao',
        'categoria',
        'is_sistema',
        'empresa_id',
        'sync_status',
        'sync_data',
        'sync_hash'
    ];

    protected $casts = [
        'is_sistema' => 'boolean',
        'sync_data' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Constantes
    const SYNC_PENDING = 'pendente';
    const SYNC_SYNCED = 'sincronizado';
    const SYNC_ERROR = 'erro';

    // Relacionamentos
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Business\Business::class, 'empresa_id');
    }

    public function papelPermissoes(): HasMany
    {
        return $this->hasMany(EmpresaPapelPermissao::class, 'permissao_id');
    }

    public function usuarioPermissoes(): HasMany
    {
        return $this->hasMany(EmpresaUsuarioPermissao::class, 'permissao_id');
    }

    // Scopes
    public function scopeSistema($query)
    {
        return $query->where('is_sistema', true);
    }

    public function scopeEmpresa($query, $empresaId)
    {
        return $query->where(function ($q) use ($empresaId) {
            $q->where('empresa_id', $empresaId)
                ->orWhere('is_sistema', true);
        });
    }

    public function scopeCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    // Métodos estáticos
    public static function getByCode(string $codigo, int $empresaId = null): ?self
    {
        $cacheKey = "permissao_{$codigo}_" . ($empresaId ?? 'sistema');

        return Cache::remember($cacheKey, 3600, function () use ($codigo, $empresaId) {
            return static::where('codigo', $codigo)
                ->where(function ($q) use ($empresaId) {
                    if ($empresaId) {
                        $q->where('empresa_id', $empresaId)
                            ->orWhere('is_sistema', true);
                    } else {
                        $q->where('is_sistema', true);
                    }
                })
                ->first();
        });
    }

    public static function getCategorias(int $empresaId = null): array
    {
        $query = static::select('categoria')
            ->distinct()
            ->whereNotNull('categoria');

        if ($empresaId) {
            $query->empresa($empresaId);
        } else {
            $query->sistema();
        }

        return $query->pluck('categoria')->toArray();
    }
}
