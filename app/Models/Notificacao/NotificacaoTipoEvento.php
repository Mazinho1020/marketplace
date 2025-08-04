<?php

namespace App\Models\Notificacao;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificacaoTipoEvento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'notificacao_tipos_evento';

    protected $fillable = [
        'empresa_id',
        'codigo',
        'nome',
        'descricao',
        'categoria',
        'automatico',
        'agendamento_cron',
        'aplicacoes_padrao',
        'variaveis_disponiveis',
        'condicoes',
        'ativo',
        'sync_hash',
        'sync_status',
        'sync_data'
    ];

    protected $casts = [
        'automatico' => 'boolean',
        'aplicacoes_padrao' => 'array',
        'variaveis_disponiveis' => 'array',
        'condicoes' => 'array',
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
        return $this->hasMany(NotificacaoTemplate::class, 'tipo_evento_id');
    }

    public function agendamentos()
    {
        return $this->hasMany(NotificacaoAgendamento::class, 'tipo_evento_id');
    }

    public function notificacoes()
    {
        return $this->hasMany(NotificacaoEnviada::class, 'tipo_evento_id');
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeAutomaticos($query)
    {
        return $query->where('automatico', true);
    }

    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }
}
