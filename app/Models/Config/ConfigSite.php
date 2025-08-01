<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfigSite extends Model
{
    use SoftDeletes;

    protected $table = 'config_sites';

    protected $fillable = [
        'empresa_id',
        'codigo',
        'nome',
        'descricao',
        'base_url_padrao',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'sync_data' => 'datetime'
    ];

    // Relacionamentos
    public function valores()
    {
        return $this->hasMany(ConfigValue::class, 'site_id');
    }
}
