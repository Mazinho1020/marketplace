<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class BaseModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $dates = ['deleted_at'];

    /**
     * Scope para filtrar por empresa
     */
    public function scopeForBusiness($query, $businessId = null)
    {
        $businessId = $businessId ?? auth()->user()?->business_id;

        if ($businessId && $this->getTable() !== 'businesses') {
            return $query->where('business_id', $businessId);
        }

        return $query;
    }

    /**
     * Relacionamento com Business (quando aplicável)
     */
    public function business()
    {
        if ($this->getTable() !== 'businesses') {
            return $this->belongsTo(\App\Models\Business\Business::class);
        }

        return null;
    }
}
