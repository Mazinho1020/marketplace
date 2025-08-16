<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Model Venda
 * 
 * Representa uma venda no sistema seguindo o padrão definido em PADRONIZACAO_COMPLETA.md
 */
class Venda extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vendas';

    // =========================================
    // CONSTANTES ORGANIZADAS POR CATEGORIA
    // =========================================

    // Tipos de venda
    const TIPOS_VENDA = [
        'balcao' => 'Balcão',
        'delivery' => 'Delivery',
        'online' => 'Online',
        'telefone' => 'Telefone',
        'mesa' => 'Mesa'
    ];

    // Status da venda
    const STATUS = [
        'aberta' => 'Aberta',
        'finalizada' => 'Finalizada',
        'cancelada' => 'Cancelada',
        'em_andamento' => 'Em Andamento',
        'aguardando_pagamento' => 'Aguardando Pagamento'
    ];

    // Tipos de entrega
    const TIPOS_ENTREGA = [
        'retirada' => 'Retirada',
        'delivery' => 'Delivery',
        'correios' => 'Correios',
        'transportadora' => 'Transportadora'
    ];

    // Origens de venda
    const ORIGENS_VENDA = [
        'pdv' => 'PDV',
        'app' => 'Aplicativo',
        'site' => 'Site',
        'whatsapp' => 'WhatsApp',
        'telefone' => 'Telefone',
        'marketplace' => 'Marketplace'
    ];

    // Status de sincronização
    const SYNC_STATUS = [
        'pending' => 'Pendente',
        'synced' => 'Sincronizado',
        'error' => 'Erro',
        'ignored' => 'Ignorado'
    ];

    // =========================================
    // CAMPOS FILLABLE
    // =========================================

    protected $fillable = [
        'empresa_id',
        'cliente_id',
        'vendedor_id',
        'caixa_id',
        'numero_venda',
        'codigo_venda',
        'uuid',
        'tipo_venda',
        'status',
        'valor_bruto',
        'valor_desconto',
        'valor_acrescimo',
        'valor_frete',
        'valor_taxa_servico',
        'valor_total',
        'valor_comissao_marketplace',
        'valor_liquido_vendedor',
        'valor_impostos',
        'aliquota_comissao',
        'data_venda',
        'data_finalizacao',
        'data_cancelamento',
        'observacoes',
        'observacoes_internas',
        'metadados',
        'tipo_entrega',
        'dados_entrega',
        'tempo_estimado_entrega',
        'origem_venda',
        'canal_venda',
        'motivo_cancelamento',
        'cancelado_por',
        'sync_hash',
        'sync_status',
        'sync_data'
    ];

    // =========================================
    // CASTS
    // =========================================

    protected $casts = [
        'valor_bruto' => 'decimal:2',
        'valor_desconto' => 'decimal:2',
        'valor_acrescimo' => 'decimal:2',
        'valor_frete' => 'decimal:2',
        'valor_taxa_servico' => 'decimal:2',
        'valor_total' => 'decimal:2',
        'valor_comissao_marketplace' => 'decimal:2',
        'valor_liquido_vendedor' => 'decimal:2',
        'valor_impostos' => 'decimal:2',
        'aliquota_comissao' => 'decimal:2',
        'tempo_estimado_entrega' => 'decimal:2',
        'data_venda' => 'datetime',
        'data_finalizacao' => 'datetime',
        'data_cancelamento' => 'datetime',
        'sync_data' => 'datetime',
        'metadados' => 'array',
        'dados_entrega' => 'array'
    ];

    // =========================================
    // RELACIONAMENTOS
    // =========================================

    /**
     * Relacionamento com Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relacionamento com Cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Relacionamento com Vendedor
     */
    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    /**
     * Relacionamento com usuário que cancelou
     */
    public function canceladoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelado_por');
    }

    /**
     * Itens da venda
     */
    public function itens(): HasMany
    {
        return $this->hasMany(VendaItem::class)->orderBy('ordem_item');
    }

    /**
     * Itens ativos da venda
     */
    public function itensAtivos(): HasMany
    {
        return $this->hasMany(VendaItem::class)->where('status_item', 'ativo');
    }

    /**
     * Produtos relacionados através dos itens
     */
    public function produtos(): HasManyThrough
    {
        return $this->hasManyThrough(
            Produto::class,
            VendaItem::class,
            'venda_id',
            'id',
            'id',
            'produto_id'
        );
    }

    /**
     * Pagamentos da venda
     */
    public function pagamentos(): HasMany
    {
        return $this->hasMany(\App\Models\Financial\Pagamento::class, 'venda_id');
    }

    // =========================================
    // SCOPES
    // =========================================

    /**
     * Scope para vendas da empresa
     */
    public function scopePorEmpresa(Builder $query, int $empresaId): Builder
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Scope para vendas por status
     */
    public function scopeComStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para vendas finalizadas
     */
    public function scopeFinalizadas(Builder $query): Builder
    {
        return $query->where('status', 'finalizada');
    }

    /**
     * Scope para vendas abertas
     */
    public function scopeAbertas(Builder $query): Builder
    {
        return $query->where('status', 'aberta');
    }

    /**
     * Scope para vendas canceladas
     */
    public function scopeCanceladas(Builder $query): Builder
    {
        return $query->where('status', 'cancelada');
    }

    /**
     * Scope para vendas por período
     */
    public function scopePorPeriodo(Builder $query, Carbon $inicio, Carbon $fim): Builder
    {
        return $query->whereBetween('data_venda', [$inicio, $fim]);
    }

    /**
     * Scope para vendas de hoje
     */
    public function scopeHoje(Builder $query): Builder
    {
        return $query->whereDate('data_venda', now()->toDateString());
    }

    /**
     * Scope para vendas do mês
     */
    public function scopeMesAtual(Builder $query): Builder
    {
        return $query->whereBetween('data_venda', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }

    /**
     * Scope para vendas por cliente
     */
    public function scopePorCliente(Builder $query, int $clienteId): Builder
    {
        return $query->where('cliente_id', $clienteId);
    }

    /**
     * Scope para vendas por vendedor
     */
    public function scopePorVendedor(Builder $query, int $vendedorId): Builder
    {
        return $query->where('vendedor_id', $vendedorId);
    }

    /**
     * Scope para vendas por tipo
     */
    public function scopePorTipo(Builder $query, string $tipo): Builder
    {
        return $query->where('tipo_venda', $tipo);
    }

    // =========================================
    // ACCESSORS
    // =========================================

    /**
     * Accessor para valor total formatado
     */
    public function getValorTotalFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_total, 2, ',', '.');
    }

    /**
     * Accessor para status badge
     */
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'aberta' => '<span class="badge bg-warning">Aberta</span>',
            'finalizada' => '<span class="badge bg-success">Finalizada</span>',
            'cancelada' => '<span class="badge bg-danger">Cancelada</span>',
            'em_andamento' => '<span class="badge bg-info">Em Andamento</span>',
            'aguardando_pagamento' => '<span class="badge bg-secondary">Aguardando Pagamento</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-light">Indefinido</span>';
    }

    /**
     * Accessor para nome do cliente
     */
    public function getNomeClienteAttribute(): string
    {
        return $this->cliente ? $this->cliente->nome : 'Cliente não informado';
    }

    /**
     * Accessor para total de itens
     */
    public function getTotalItensAttribute(): int
    {
        return $this->itensAtivos->sum('quantidade');
    }

    // =========================================
    // MÉTODOS DE NEGÓCIO
    // =========================================

    /**
     * Calcula o total da venda baseado nos itens
     */
    public function calcularTotal(): void
    {
        $this->valor_bruto = $this->itensAtivos->sum('valor_total_item');
        $this->valor_total = $this->valor_bruto - $this->valor_desconto + $this->valor_acrescimo + $this->valor_frete + $this->valor_taxa_servico;
        $this->calcularComissao();
        $this->calcularValorLiquido();
    }

    /**
     * Calcula a comissão do marketplace
     */
    public function calcularComissao(): void
    {
        if ($this->aliquota_comissao > 0) {
            $this->valor_comissao_marketplace = ($this->valor_total * $this->aliquota_comissao) / 100;
        }
    }

    /**
     * Calcula o valor líquido para o vendedor
     */
    public function calcularValorLiquido(): void
    {
        $this->valor_liquido_vendedor = $this->valor_total - $this->valor_comissao_marketplace - $this->valor_impostos;
    }

    /**
     * Adiciona um item à venda
     */
    public function adicionarItem(array $dadosItem): VendaItem
    {
        $dadosItem['empresa_id'] = $this->empresa_id;
        $dadosItem['ordem_item'] = $this->itens()->max('ordem_item') + 1;
        
        $item = $this->itens()->create($dadosItem);
        $this->calcularTotal();
        $this->save();
        
        return $item;
    }

    /**
     * Remove um item da venda
     */
    public function removerItem(int $itemId): bool
    {
        $item = $this->itens()->find($itemId);
        if ($item) {
            $item->delete();
            $this->calcularTotal();
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Finaliza a venda
     */
    public function finalizar(): bool
    {
        if ($this->status !== 'aberta') {
            return false;
        }

        $this->status = 'finalizada';
        $this->data_finalizacao = now();
        $this->baixarEstoque();
        
        return $this->save();
    }

    /**
     * Cancela a venda
     */
    public function cancelar(string $motivo, int $usuarioId): bool
    {
        if ($this->status === 'cancelada') {
            return false;
        }

        $this->status = 'cancelada';
        $this->data_cancelamento = now();
        $this->motivo_cancelamento = $motivo;
        $this->cancelado_por = $usuarioId;
        
        // Reverter estoque se já foi baixado
        if ($this->data_finalizacao) {
            $this->reverterEstoque();
        }
        
        return $this->save();
    }

    /**
     * Baixa o estoque dos produtos vendidos
     */
    protected function baixarEstoque(): void
    {
        foreach ($this->itensAtivos as $item) {
            if ($item->produto && $item->controla_estoque) {
                $item->produto->baixarEstoque(
                    $item->quantidade,
                    'venda',
                    "Venda #{$this->numero_venda}"
                );
            }
        }
    }

    /**
     * Reverte o estoque dos produtos
     */
    protected function reverterEstoque(): void
    {
        foreach ($this->itensAtivos as $item) {
            if ($item->produto && $item->controla_estoque) {
                $item->produto->adicionarEstoque(
                    $item->quantidade,
                    'cancelamento',
                    "Cancelamento da venda #{$this->numero_venda}"
                );
            }
        }
    }

    /**
     * Gera o próximo número da venda para a empresa
     */
    public static function proximoNumero(int $empresaId): string
    {
        $ultimo = static::where('empresa_id', $empresaId)
            ->whereYear('data_venda', now()->year)
            ->max('numero_venda');

        if ($ultimo) {
            $numero = (int) substr($ultimo, -6) + 1;
        } else {
            $numero = 1;
        }

        return now()->year . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    // =========================================
    // EVENTS
    // =========================================

    /**
     * Boot method para eventos do modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($venda) {
            // Gerar UUID se não existir
            if (empty($venda->uuid)) {
                $venda->uuid = Str::uuid();
            }

            // Gerar número da venda se não existir
            if (empty($venda->numero_venda)) {
                $venda->numero_venda = static::proximoNumero($venda->empresa_id);
            }

            // Definir data da venda se não existir
            if (empty($venda->data_venda)) {
                $venda->data_venda = now();
            }

            // Marcar para sincronização
            $venda->sync_status = 'pending';
            $venda->sync_hash = md5(json_encode($venda->toArray()));
        });

        static::updating(function ($venda) {
            // Atualizar hash de sincronização
            $venda->sync_status = 'pending';
            $venda->sync_hash = md5(json_encode($venda->toArray()));
        });
    }
}
