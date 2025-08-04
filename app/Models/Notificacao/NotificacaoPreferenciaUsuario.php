<?php

namespace App\Models\Notificacao;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificacaoPreferenciaUsuario extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'notificacao_preferencias_usuario';

    protected $fillable = [
        'empresa_id',
        'usuario_id',
        'aplicacao_id',
        'websocket_habilitado',
        'push_habilitado',
        'email_habilitado',
        'sms_habilitado',
        'in_app_habilitado',
        'horario_silencio_inicio',
        'horario_silencio_fim',
        'frequencia_digest',
        'horario_digest',
        'tipos_evento_bloqueados',
        'configuracoes_adicionais',
        'sync_hash',
        'sync_status',
        'sync_data'
    ];

    protected $casts = [
        'websocket_habilitado' => 'boolean',
        'push_habilitado' => 'boolean',
        'email_habilitado' => 'boolean',
        'sms_habilitado' => 'boolean',
        'in_app_habilitado' => 'boolean',
        'horario_silencio_inicio' => 'datetime:H:i',
        'horario_silencio_fim' => 'datetime:H:i',
        'horario_digest' => 'datetime:H:i',
        'tipos_evento_bloqueados' => 'array',
        'configuracoes_adicionais' => 'array',
        'sync_data' => 'datetime'
    ];

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(\App\Models\Empresa::class);
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }

    public function aplicacao()
    {
        return $this->belongsTo(NotificacaoAplicacao::class, 'aplicacao_id');
    }

    // Scopes
    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    public function scopePorAplicacao($query, $aplicacaoId)
    {
        return $query->where('aplicacao_id', $aplicacaoId);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    // Métodos
    public function getCanalHabilitado(string $canal): bool
    {
        return match ($canal) {
            'websocket' => $this->websocket_habilitado,
            'push' => $this->push_habilitado,
            'email' => $this->email_habilitado,
            'sms' => $this->sms_habilitado,
            'in_app' => $this->in_app_habilitado,
            default => false
        };
    }

    public function isHorarioSilencio(): bool
    {
        if (!$this->horario_silencio_inicio || !$this->horario_silencio_fim) {
            return false;
        }

        $now = now()->format('H:i');
        $inicio = $this->horario_silencio_inicio->format('H:i');
        $fim = $this->horario_silencio_fim->format('H:i');

        return $now >= $inicio && $now <= $fim;
    }

    public function isTipoEventoBloqueado(string $tipoEvento): bool
    {
        return in_array($tipoEvento, $this->tipos_evento_bloqueados ?? []);
    }
}
