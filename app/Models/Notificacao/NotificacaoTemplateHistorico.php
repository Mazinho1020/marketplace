<?php

namespace App\Models\Notificacao;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificacaoTemplateHistorico extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'notificacao_templates_historico';

    protected $fillable = [
        'empresa_id',
        'template_id',
        'usuario_id',
        'acao',
        'alteracoes',
        'dados_anteriores',
        'dados_novos',
        'motivo',
        'endereco_ip',
        'user_agent',
        'sync_hash',
        'sync_status',
        'sync_data'
    ];

    protected $casts = [
        'alteracoes' => 'array',
        'dados_anteriores' => 'array',
        'dados_novos' => 'array',
        'sync_data' => 'datetime'
    ];

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(\App\Models\Empresa::class);
    }

    public function template()
    {
        return $this->belongsTo(NotificacaoTemplate::class, 'template_id');
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }

    // Scopes
    public function scopePorTemplate($query, $templateId)
    {
        return $query->where('template_id', $templateId);
    }

    public function scopePorAcao($query, $acao)
    {
        return $query->where('acao', $acao);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }
}
