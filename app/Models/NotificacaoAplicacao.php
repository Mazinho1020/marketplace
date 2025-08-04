<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Model para aplicações de notificação
 * 
 * Representa as diferentes aplicações que podem receber notificações:
 * - Cliente
 * - Empresa  
 * - Admin
 * - Entregador
 * - Fidelidade
 */
class NotificacaoAplicacao extends BaseModel
{
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
        'sync_data',
    ];

    protected $casts = [
        'configuracoes' => 'array',
        'ativo' => 'boolean',
        'ordem_exibicao' => 'integer',
        'sync_data' => 'datetime',
    ];

    /**
     * Relacionamento com empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relacionamento com templates
     */
    public function templates(): HasMany
    {
        return $this->hasMany(NotificacaoTemplate::class, 'aplicacao_id');
    }

    /**
     * Relacionamento com notificações enviadas
     */
    public function notificacoesEnviadas(): HasMany
    {
        return $this->hasMany(NotificacaoEnviada::class, 'aplicacao_id');
    }

    /**
     * Relacionamento com preferências de usuário
     */
    public function preferenciasUsuario(): HasMany
    {
        return $this->hasMany(NotificacaoPreferenciaUsuario::class, 'aplicacao_id');
    }

    /**
     * Scope para aplicações ativas
     */
    public function scopeAtivo(Builder $query): Builder
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para buscar por código
     */
    public function scopePorCodigo(Builder $query, string $codigo): Builder
    {
        return $query->where('codigo', $codigo);
    }

    /**
     * Scope para ordenar por exibição
     */
    public function scopeOrdenadoPorExibicao(Builder $query): Builder
    {
        return $query->orderBy('ordem_exibicao')->orderBy('nome');
    }

    /**
     * Scope para filtrar por empresa
     */
    public function scopePorEmpresa(Builder $query, int $empresaId): Builder
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Verifica se a aplicação tem webhook configurado
     */
    public function temWebhook(): bool
    {
        return !empty($this->webhook_url) && !empty($this->api_key);
    }

    /**
     * Obtém configuração específica
     */
    public function getConfiguracao(string $chave, $default = null)
    {
        return data_get($this->configuracoes, $chave, $default);
    }

    /**
     * Define configuração específica
     */
    public function setConfiguracao(string $chave, $valor): void
    {
        $configuracoes = $this->configuracoes ?? [];
        data_set($configuracoes, $chave, $valor);
        $this->configuracoes = $configuracoes;
    }

    /**
     * Aplicações padrão do sistema
     */
    public static function aplicacoesPadrao(): array
    {
        return [
            'cliente' => [
                'nome' => 'Cliente',
                'descricao' => 'Aplicação para clientes do marketplace',
                'icone_classe' => 'fas fa-user',
                'cor_hex' => '#28a745',
            ],
            'empresa' => [
                'nome' => 'Empresa',
                'descricao' => 'Aplicação para empresas vendedoras',
                'icone_classe' => 'fas fa-building',
                'cor_hex' => '#007bff',
            ],
            'admin' => [
                'nome' => 'Administrador',
                'descricao' => 'Painel administrativo do marketplace',
                'icone_classe' => 'fas fa-user-shield',
                'cor_hex' => '#6c757d',
            ],
            'entregador' => [
                'nome' => 'Entregador',
                'descricao' => 'Aplicação para entregadores',
                'icone_classe' => 'fas fa-truck',
                'cor_hex' => '#ffc107',
            ],
            'fidelidade' => [
                'nome' => 'Programa de Fidelidade',
                'descricao' => 'Sistema de pontos e fidelidade',
                'icone_classe' => 'fas fa-heart',
                'cor_hex' => '#e83e8c',
            ],
        ];
    }
}
