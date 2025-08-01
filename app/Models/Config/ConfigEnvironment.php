<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfigEnvironment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'config_environments';

    protected $fillable = [
        'empresa_id',
        'codigo',
        'nome',
        'descricao',
        'is_producao',
        'ativo'
    ];

    protected $casts = [
        'is_producao' => 'boolean',
        'ativo' => 'boolean'
    ];

    // Relacionamento com valores de configuração
    public function configValues(): HasMany
    {
        return $this->hasMany(ConfigValue::class, 'ambiente_id');
    }

    // Relacionamento com histórico
    public function configHistory(): HasMany
    {
        return $this->hasMany(ConfigHistory::class, 'ambiente_id');
    }

    // Scopes
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeProducao($query)
    {
        return $query->where('is_producao', true);
    }

    public function scopeDesenvolvimento($query)
    {
        return $query->where('is_producao', false);
    }

    // Métodos estáticos
    public static function getCurrentEnvironment()
    {
        $isProduction = app()->environment('production');

        return self::where('is_producao', $isProduction)
            ->where('ativo', true)
            ->first();
    }

    public static function getProducao()
    {
        return self::where('is_producao', true)
            ->where('ativo', true)
            ->first();
    }

    public static function getDesenvolvimento()
    {
        return self::where('is_producao', false)
            ->where('ativo', true)
            ->first();
    }
}
