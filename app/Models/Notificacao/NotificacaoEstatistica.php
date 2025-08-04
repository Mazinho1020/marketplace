<?php

namespace App\Models\Notificacao;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificacaoEstatistica extends Model
{
    use HasFactory;

    protected $table = 'notificacao_estatisticas';

    protected $fillable = [
        'empresa_id',
        'aplicacao_id',
        'tipo_evento_id',
        'template_id',
        'data_referencia',
        'hora_referencia',
        'tipo_metrica',
        'canal',
        'total_eventos',
        'total_usuarios_unicos',
        'taxa_sucesso',
        'tempo_medio_entrega',
        'tempo_medio_leitura',
        'dados_detalhados',
        'sync_hash',
        'sync_status',
        'sync_data'
    ];

    protected $casts = [
        'data_referencia' => 'date',
        'taxa_sucesso' => 'decimal:2',
        'tempo_medio_entrega' => 'decimal:2',
        'tempo_medio_leitura' => 'decimal:2',
        'dados_detalhados' => 'array',
        'sync_data' => 'datetime'
    ];

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(\App\Models\Empresa::class);
    }

    public function aplicacao()
    {
        return $this->belongsTo(NotificacaoAplicacao::class, 'aplicacao_id');
    }

    public function tipoEvento()
    {
        return $this->belongsTo(NotificacaoTipoEvento::class, 'tipo_evento_id');
    }

    public function template()
    {
        return $this->belongsTo(NotificacaoTemplate::class, 'template_id');
    }

    // Scopes
    public function scopePorPeriodo($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('data_referencia', [$dataInicio, $dataFim]);
    }

    public function scopePorMetrica($query, $tipoMetrica)
    {
        return $query->where('tipo_metrica', $tipoMetrica);
    }

    public function scopePorCanal($query, $canal)
    {
        return $query->where('canal', $canal);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    // Métodos estáticos para agregação
    public static function getTotalEnviosPorPeriodo($empresaId, $dataInicio, $dataFim)
    {
        return static::where('empresa_id', $empresaId)
            ->where('tipo_metrica', 'envio')
            ->whereBetween('data_referencia', [$dataInicio, $dataFim])
            ->sum('total_eventos');
    }

    public static function getTaxaEntregaMedia($empresaId, $dataInicio, $dataFim)
    {
        return static::where('empresa_id', $empresaId)
            ->where('tipo_metrica', 'entrega')
            ->whereBetween('data_referencia', [$dataInicio, $dataFim])
            ->avg('taxa_sucesso');
    }
}
