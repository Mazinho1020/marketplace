<?php

namespace App\Models\Notificacao;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificacaoTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'notificacao_templates';

    protected $fillable = [
        'empresa_id',
        'tipo_evento_id',
        'aplicacao_id',
        'nome',
        'categoria',
        'titulo',
        'mensagem',
        'subtitulo',
        'texto_acao',
        'url_acao',
        'canais',
        'prioridade',
        'expira_em_minutos',
        'variaveis',
        'condicoes',
        'segmentos_usuario',
        'icone_classe',
        'cor_hex',
        'arquivo_som',
        'url_imagem',
        'ativo',
        'padrao',
        'versao',
        'percentual_ab_test',
        'total_uso',
        'taxa_conversao',
        'ultimo_uso_em',
        'sync_hash',
        'sync_status',
        'sync_data'
    ];

    protected $casts = [
        'canais' => 'array',
        'variaveis' => 'array',
        'condicoes' => 'array',
        'segmentos_usuario' => 'array',
        'ativo' => 'boolean',
        'padrao' => 'boolean',
        'percentual_ab_test' => 'decimal:2',
        'taxa_conversao' => 'decimal:2',
        'ultimo_uso_em' => 'datetime',
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

    public function aplicacao()
    {
        return $this->belongsTo(NotificacaoAplicacao::class, 'aplicacao_id');
    }

    public function notificacoes()
    {
        return $this->hasMany(NotificacaoEnviada::class, 'template_id');
    }

    public function historico()
    {
        return $this->hasMany(NotificacaoTemplateHistorico::class, 'template_id');
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePadrao($query)
    {
        return $query->where('padrao', true);
    }

    public function scopePorEventoEApp($query, $tipoEventoId, $aplicacaoId)
    {
        return $query->where('tipo_evento_id', $tipoEventoId)
            ->where('aplicacao_id', $aplicacaoId);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    // Métodos
    public function processarVariaveis($dados)
    {
        $titulo = $this->titulo;
        $mensagem = $this->mensagem;

        foreach ($dados as $variavel => $valor) {
            $titulo = str_replace("{{{$variavel}}}", $valor, $titulo);
            $mensagem = str_replace("{{{$variavel}}}", $valor, $mensagem);
        }

        return [
            'titulo' => $titulo,
            'mensagem' => $mensagem,
            'subtitulo' => $this->subtitulo,
            'texto_acao' => $this->texto_acao,
            'url_acao' => str_replace(array_keys($dados), array_values($dados), $this->url_acao),
            'icone_classe' => $this->icone_classe,
            'cor_hex' => $this->cor_hex
        ];
    }

    public function incrementarUso()
    {
        $this->increment('total_uso');
        $this->update(['ultimo_uso_em' => now()]);
    }
}
