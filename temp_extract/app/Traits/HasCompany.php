<?php

namespace App\Traits;

use App\Models\Company\Empresa;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasCompany
{
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function scopeForCompany($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    protected static function bootHasCompany()
    {
        static::creating(function ($model) {
            if (auth()->check() && !$model->empresa_id && auth()->user()->empresa_id) {
                $model->empresa_id = auth()->user()->empresa_id;
            }
        });
    }
}