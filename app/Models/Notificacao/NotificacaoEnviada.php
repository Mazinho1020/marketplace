<?php

namespace App\Models\Notificacao;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificacaoEnviada extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'notificacao_enviadas';

    protected $fillable = [
        'empresa_id',
        'template_id',
        'tipo_evento_id',
        'aplicacao_id',
        'usuario_id',
        'empresa_relacionada_id',
        'usuario_externo_id',
        'email_destinatario',
        'telefone_destinatario',
        'titulo',
        'mensagem',
        'dados_processados',
        'canal',
        'prioridade',
        'agendado_para',
        'enviado_em',
        'entregue_em',
        'lido_em',
        'clicado_em',
        'status',
        'tentativas',
        'mensagem_erro',
        'id_externo',
        'expira_em',
        'dados_evento_origem',
        'user_agent',
        'endereco_ip',
        'info_dispositivo',
        'sync_hash',
        'sync_status',
        'sync_data'
    ];

    protected $casts = [
        'dados_processados' => 'array',
        'agendado_para' => 'datetime',
        'enviado_em' => 'datetime',
        'entregue_em' => 'datetime',
        'lido_em' => 'datetime',
        'clicado_em' => 'datetime',
        'expira_em' => 'datetime',
        'dados_evento_origem' => 'array',
        'info_dispositivo' => 'array',
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

    public function tipoEvento()
    {
        return $this->belongsTo(NotificacaoTipoEvento::class, 'tipo_evento_id');
    }

    public function aplicacao()
    {
        return $this->belongsTo(NotificacaoAplicacao::class, 'aplicacao_id');
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }

    // Scopes
    public function scopeNaoLidas($query)
    {
        return $query->whereNull('lido_em');
    }

    public function scopeEntregues($query)
    {
        return $query->where('status', 'entregue');
    }

    public function scopePorCanal($query, $canal)
    {
        return $query->where('canal', $canal);
    }

    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    // Métodos
    public function marcarComoLida()
    {
        $this->update(['lido_em' => now()]);
    }

    public function marcarComoClicada()
    {
        $this->update(['clicado_em' => now()]);
    }

    public function isLida()
    {
        return !is_null($this->lido_em);
    }

    public function isExpirada()
    {
        return $this->expira_em && $this->expira_em->isPast();
    }
}
