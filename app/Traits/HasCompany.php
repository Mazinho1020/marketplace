<?php

namespace App\Traits;

use App\Models\Empresa;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasCompany
{
    /**
     * Relacionamento com empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    /**
     * Scope para filtrar por empresa
     */
    public function scopeForCompany(Builder $query, int $empresaId): Builder
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Scope para empresa atual do usuário autenticado
     */
    public function scopeForCurrentCompany(Builder $query): Builder
    {
        $empresaId = Auth::user()?->empresa_id ?? session('empresa_id');

        if ($empresaId) {
            return $query->where('empresa_id', $empresaId);
        }

        return $query;
    }

    /**
     * Boot do trait
     */
    protected static function bootHasCompany()
    {
        static::creating(function ($model) {
            if (!$model->empresa_id) {
                $empresaId = Auth::user()?->empresa_id ?? session('empresa_id');
                if ($empresaId) {
                    $model->empresa_id = $empresaId;
                }
            }
        });

        // Aplicar scope global para filtrar por empresa do usuário
        static::addGlobalScope('company', function (Builder $builder) {
            $empresaId = Auth::user()?->empresa_id ?? session('empresa_id');

            if ($empresaId && !request()->routeIs('admin.*')) {
                $builder->where($builder->getModel()->getTable() . '.empresa_id', $empresaId);
            }
        });
    }
}
