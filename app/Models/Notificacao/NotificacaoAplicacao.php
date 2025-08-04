<?php

namespace App\Models\Notificacao;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificacaoAplicacao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'notificacao_aplicacoes';

    protected $fillable = [
        'empresa_id',
        'codigo',
        'nome',
        'descricao',
        'icone_classe',
        'cor_hex',
        'webhook_url',
        'api_key',
        'configuracoes',
        'ativo',
        'ordem_exibicao',
        'sync_hash',
        'sync_status',
        'sync_data'
    ];

    protected $casts = [
        'configuracoes' => 'array',
        'ativo' => 'boolean',
        'sync_data' => 'datetime'
    ];

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(\App\Models\Empresa::class);
    }

    public function templates()
    {
        return $this->hasMany(NotificacaoTemplate::class, 'aplicacao_id');
    }

    public function notificacoes()
    {
        return $this->hasMany(NotificacaoEnviada::class, 'aplicacao_id');
    }

    public function preferenciasUsuario()
    {
        return $this->hasMany(NotificacaoPreferenciaUsuario::class, 'aplicacao_id');
    }

    // Scopes
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeOrdenado($query)
    {
        return $query->orderBy('ordem_exibicao');
    }

    // Mutators
    public function setSyncHashAttribute($value)
    {
        $this->attributes['sync_hash'] = $value ?: md5(json_encode($this->attributes));
    }

    // Accessors
    public function getStatusSyncAttribute()
    {
        return $this->sync_status === 'synced' ? 'Sincronizado' : 'Pendente';
    }
}
