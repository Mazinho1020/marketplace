<?php

namespace App\Models\Vendas;

use App\Models\Financeiro\Lancamento;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Model para Vendas
 * 
 * Extende o modelo Lancamento existente adicionando funcionalidades 
 * específicas para gestão de vendas e pedidos
 */
class Venda extends Lancamento
{
    protected $table = 'lancamentos';

    // Status específicos para vendas
    const STATUS_RASCUNHO = 'rascunho';
    const STATUS_PENDENTE = 'pendente';  
    const STATUS_CONFIRMADO = 'confirmado';
    const STATUS_PROCESSANDO = 'processando';
    const STATUS_SEPARANDO = 'separando';
    const STATUS_ENVIADO = 'enviado';
    const STATUS_ENTREGUE = 'entregue';
    const STATUS_CANCELADO = 'cancelado';
    const STATUS_DEVOLVIDO = 'devolvido';

    // Canais de venda
    const CANAL_PDV = 'pdv';
    const CANAL_ONLINE = 'online';
    const CANAL_DELIVERY = 'delivery';
    const CANAL_TELEFONE = 'telefone';
    const CANAL_WHATSAPP = 'whatsapp';
    const CANAL_PRESENCIAL = 'presencial';

    protected $fillable = [
        // Campos do Lancamento base
        'uuid', 'empresa_id', 'usuario_id', 'pessoa_id', 'pessoa_tipo',
        'natureza_financeira', 'categoria', 'origem', 'valor_bruto',
        'valor_desconto', 'valor_acrescimo', 'descricao', 'data_emissao',
        'data_competencia', 'data_vencimento', 'observacoes',
        // Campos específicos de vendas
        'numero_venda', 'canal_venda', 'data_entrega_prevista',
        'data_entrega_realizada', 'cupom_fidelidade_id', 'pontos_utilizados',
        'pontos_gerados', 'cashback_aplicado', 'cashback_gerado',
        'transportadora', 'codigo_rastreamento', 'tipo_entrega', 'prioridade',
    ];

    protected $casts = [
        'data_entrega_prevista' => 'datetime',
        'data_entrega_realizada' => 'datetime',
        'pontos_utilizados' => 'integer',
        'pontos_gerados' => 'integer',
        'cashback_aplicado' => 'decimal:2',
        'cashback_gerado' => 'decimal:2',
    ];

    /**
     * Boot do modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->numero_venda)) {
                $model->numero_venda = $model->gerarNumeroVenda();
            }
            if (empty($model->categoria)) {
                $model->categoria = self::CATEGORIA_VENDA;
            }
            if (empty($model->natureza_financeira)) {
                $model->natureza_financeira = self::NATUREZA_ENTRADA;
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('situacao_financeira')) {
                $model->registrarMudancaStatus(
                    $model->getOriginal('situacao_financeira'),
                    $model->situacao_financeira
                );
            }
        });
    }

    /**
     * Relacionamentos específicos de vendas
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Comerciante\Models\Pessoas\Pessoa::class, 'pessoa_id');
    }

    public function cupomFidelidade(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Fidelidade\FidelidadeCupom::class, 'cupom_fidelidade_id');
    }

    public function historicoStatus(): HasMany
    {
        return $this->hasMany(VendaStatusHistorico::class, 'lancamento_id');
    }

    public function cancelamentos(): HasMany
    {
        return $this->hasMany(VendaCancelamento::class, 'lancamento_id');
    }

    /**
     * Scopes específicos para vendas
     */
    public function scopeVendas(Builder $query): Builder
    {
        return $query->where('categoria', self::CATEGORIA_VENDA)
                    ->where('natureza_financeira', self::NATUREZA_ENTRADA);
    }

    public function scopePorStatus(Builder $query, $status): Builder
    {
        return $query->where('situacao_financeira', $status);
    }

    public function scopePorCanal(Builder $query, $canal): Builder
    {
        return $query->where('canal_venda', $canal);
    }

    /**
     * Métodos de negócio específicos para vendas
     */
    public function gerarNumeroVenda(): string
    {
        $prefixo = 'VD';
        $ano = date('Y');
        $mes = date('m');
        
        // Buscar último número do mês
        $ultimaVenda = static::where('empresa_id', $this->empresa_id)
            ->where('numero_venda', 'like', "{$prefixo}{$ano}{$mes}%")
            ->orderBy('numero_venda', 'desc')
            ->first();

        if ($ultimaVenda && preg_match("/^{$prefixo}{$ano}{$mes}(\d{4})$/", $ultimaVenda->numero_venda, $matches)) {
            $proximoNumero = intval($matches[1]) + 1;
        } else {
            $proximoNumero = 1;
        }

        return $prefixo . $ano . $mes . str_pad($proximoNumero, 4, '0', STR_PAD_LEFT);
    }

    public function alterarStatus(string $novoStatus, string $motivo = null, array $dadosContexto = []): bool
    {
        $statusAnterior = $this->situacao_financeira;
        
        if (!$this->podeAlterarPara($novoStatus)) {
            return false;
        }

        $this->situacao_financeira = $novoStatus;
        $this->registrarMudancaStatus($statusAnterior, $novoStatus, $motivo, $dadosContexto);
        
        return $this->save();
    }

    public function podeAlterarPara(string $novoStatus): bool
    {
        $statusAtual = $this->situacao_financeira;
        
        $transicoesPermitidas = [
            self::STATUS_RASCUNHO => [self::STATUS_PENDENTE, self::STATUS_CANCELADO],
            self::STATUS_PENDENTE => [self::STATUS_CONFIRMADO, self::STATUS_CANCELADO],
            self::STATUS_CONFIRMADO => [self::STATUS_PROCESSANDO, self::STATUS_CANCELADO],
            self::STATUS_PROCESSANDO => [self::STATUS_SEPARANDO, self::STATUS_CANCELADO],
            self::STATUS_SEPARANDO => [self::STATUS_ENVIADO, self::STATUS_CANCELADO],
            self::STATUS_ENVIADO => [self::STATUS_ENTREGUE, self::STATUS_DEVOLVIDO],
            self::STATUS_ENTREGUE => [self::STATUS_DEVOLVIDO],
            self::STATUS_CANCELADO => [],
            self::STATUS_DEVOLVIDO => [],
        ];

        return isset($transicoesPermitidas[$statusAtual]) && 
               in_array($novoStatus, $transicoesPermitidas[$statusAtual]);
    }

    /**
     * Métodos privados
     */
    private function registrarMudancaStatus(
        ?string $statusAnterior, 
        string $statusNovo, 
        ?string $motivo = null, 
        array $dadosContexto = []
    ): void {
        $this->historicoStatus()->create([
            'empresa_id' => $this->empresa_id,
            'status_anterior' => $statusAnterior,
            'status_novo' => $statusNovo,
            'usuario_id' => auth()->id() ?? $this->usuario_id,
            'motivo' => $motivo,
            'dados_contexto' => $dadosContexto,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Formatters
     */
    public function getStatusFormatadoAttribute(): string
    {
        $status = [
            self::STATUS_RASCUNHO => 'Rascunho',
            self::STATUS_PENDENTE => 'Pendente',
            self::STATUS_CONFIRMADO => 'Confirmado',
            self::STATUS_PROCESSANDO => 'Processando',
            self::STATUS_SEPARANDO => 'Separando',
            self::STATUS_ENVIADO => 'Enviado',
            self::STATUS_ENTREGUE => 'Entregue',
            self::STATUS_CANCELADO => 'Cancelado',
            self::STATUS_DEVOLVIDO => 'Devolvido',
        ];

        return $status[$this->situacao_financeira] ?? $this->situacao_financeira;
    }
}