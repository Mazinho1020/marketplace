<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfigGroup extends Model
{
    use SoftDeletes;

    protected $table = 'config_groups';

    protected $fillable = [
        'empresa_id',
        'codigo',
        'nome',
        'descricao',
        'grupo_pai_id',
        'icone',
        'icone_class',
        'ordem',
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
    public function configDefinitions()
    {
        return $this->hasMany(ConfigDefinition::class, 'grupo_id');
    }

    // Alias para facilitar uso no controller
    public function definicoes()
    {
        return $this->hasMany(ConfigDefinition::class, 'grupo_id');
    }

    public function grupoPai()
    {
        return $this->belongsTo(ConfigGroup::class, 'grupo_pai_id');
    }

    public function subgrupos()
    {
        return $this->hasMany(ConfigGroup::class, 'grupo_pai_id');
    }
}
