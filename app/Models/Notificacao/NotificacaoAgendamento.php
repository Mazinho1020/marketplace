<?php

namespace App\Models\Notificacao;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificacaoAgendamento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'notificacao_agendamentos';

    // Constantes para os valores de status
    const STATUS_PENDENTE = 'pendente';
    const STATUS_AGENDADO = 'agendado';
    const STATUS_PROCESSANDO = 'processando';
    const STATUS_ENVIADO = 'enviado';
    const STATUS_FALHOU = 'falhou';
    const STATUS_CANCELADO = 'cancelado';

    // Constantes para sync_status
    const SYNC_PENDING = 'pending';
    const SYNC_SYNCED = 'synced';
    const SYNC_ERROR = 'error';
    const SYNC_IGNORED = 'ignored';

    protected $fillable = [
        'empresa_id',
        'tipo_evento_id',
        'nome',
        'descricao',
        'expressao_cron',
        'aplicacoes_alvo',
        'condicoes',
        'parametros',
        'ativo',
        'status', // Nova coluna para controle de estado dos agendamentos
        'max_execucoes',
        'total_execucoes',
        'proxima_execucao',
        'ultima_execucao',
        'status_ultima_execucao',
        'log_ultima_execucao',
        'timeout_segundos',
        'sync_hash',
        'sync_status',
        'sync_data'
    ];

    protected $casts = [
        'aplicacoes_alvo' => 'array',
        'condicoes' => 'array',
        'parametros' => 'array',
        'ativo' => 'boolean',
        'proxima_execucao' => 'datetime',
        'ultima_execucao' => 'datetime',
        'sync_data' => 'datetime'
    ];

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(\App\Models\Empresa::class);
    }

    public function tipoEvento()
    {
        return $this->belongsTo(NotificacaoTipoEvento::class, 'tipo_evento_id');
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeParaExecucao($query)
    {
        return $query->where('ativo', true)
            ->where('proxima_execucao', '<=', now())
            ->whereRaw('(max_execucoes IS NULL OR total_execucoes < max_execucoes)');
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopePendentes($query)
    {
        return $query->where('status', self::STATUS_PENDENTE);
    }

    public function scopeAgendados($query)
    {
        return $query->where('status', self::STATUS_AGENDADO);
    }

    public function scopeProcessando($query)
    {
        return $query->where('status', self::STATUS_PROCESSANDO);
    }

    // Métodos
    public function calcularProximaExecucao()
    {
        // Aqui você implementaria a lógica do cron
        // Por simplicidade, vamos adicionar 1 hora
        $this->proxima_execucao = now()->addHour();
        $this->save();
    }

    public function registrarExecucao($status, $log = null)
    {
        $this->update([
            'total_execucoes' => $this->total_execucoes + 1,
            'ultima_execucao' => now(),
            'status_ultima_execucao' => $status,
            'log_ultima_execucao' => $log
        ]);

        $this->calcularProximaExecucao();
    }
}
