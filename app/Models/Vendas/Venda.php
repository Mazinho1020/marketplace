<?php

namespace App\Models\Vendas;

use App\Models\Financial\BaseFinancialModel;
use App\Models\Financeiro\Lancamento;
use App\Models\Financeiro\LancamentoItem;
use App\Models\Financeiro\Pagamento;
use App\Models\Produto;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Modelo de Vendas
 * 
 * Integra com o sistema financeiro existente:
 * - Cria lançamento na tabela 'lancamentos' 
 * - Adiciona itens na tabela 'lancamento_itens'
 * - Registra pagamentos na tabela 'pagamentos'
 * 
 * @property int $id
 * @property string $uuid
 * @property int $empresa_id
 * @property int $usuario_id
 * @property int $lancamento_id
 * @property int $cliente_id
 * @property string $numero_venda
 * @property string $tipo_venda
 * @property float $valor_total
 * @property float $valor_desconto
 * @property float $valor_liquido
 * @property string $status
 * @property Carbon $data_venda
 * @property string $observacoes
 * @property array $metadados
 */
class Venda extends BaseFinancialModel
{
    protected $table = 'vendas';

    protected $fillable = [
        'uuid',
        'empresa_id',
        'usuario_id',
        'lancamento_id',
        'cliente_id',
        'numero_venda',
        'tipo_venda',
        'valor_total',
        'valor_desconto',
        'valor_liquido',
        'status',
        'data_venda',
        'observacoes',
        'metadados'
    ];

    protected $casts = [
        'valor_total' => 'decimal:2',
        'valor_desconto' => 'decimal:2',
        'valor_liquido' => 'decimal:2',
        'data_venda' => 'datetime',
        'metadados' => 'array',
    ];

    // Status da venda
    const STATUS_PENDENTE = 'pendente';
    const STATUS_CONFIRMADA = 'confirmada';
    const STATUS_CANCELADA = 'cancelada';
    const STATUS_ENTREGUE = 'entregue';

    // Tipos de venda
    const TIPO_BALCAO = 'balcao';
    const TIPO_DELIVERY = 'delivery';
    const TIPO_ONLINE = 'online';
    const TIPO_TELEFONE = 'telefone';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($venda) {
            if (empty($venda->uuid)) {
                $venda->uuid = (string) Str::uuid();
            }

            if (empty($venda->numero_venda)) {
                $venda->numero_venda = $venda->gerarNumeroVenda();
            }

            if (empty($venda->data_venda)) {
                $venda->data_venda = now();
            }

            if (empty($venda->status)) {
                $venda->status = self::STATUS_PENDENTE;
            }
        });

        static::created(function ($venda) {
            // Criar lançamento financeiro automaticamente
            $venda->criarLancamentoFinanceiro();
        });
    }

    /**
     * Relacionamentos
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Comerciante\Models\Pessoas\Pessoa::class, 'cliente_id');
    }

    public function lancamento(): BelongsTo
    {
        return $this->belongsTo(Lancamento::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(VendaItem::class);
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class, 'lancamento_id', 'lancamento_id');
    }

    /**
     * Scopes
     */
    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopePorStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePorPeriodo($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('data_venda', [$dataInicio, $dataFim]);
    }

    public function scopeHoje($query)
    {
        return $query->whereDate('data_venda', today());
    }

    public function scopeEsteMes($query)
    {
        return $query->whereMonth('data_venda', now()->month)
                    ->whereYear('data_venda', now()->year);
    }

    /**
     * Métodos de negócio
     */
    public function gerarNumeroVenda(): string
    {
        $ano = date('Y');
        $ultimaVenda = static::where('empresa_id', $this->empresa_id)
            ->where('numero_venda', 'like', $ano . '%')
            ->orderBy('numero_venda', 'desc')
            ->first();

        if ($ultimaVenda) {
            $ultimoNumero = (int) substr($ultimaVenda->numero_venda, -6);
            $novoNumero = $ultimoNumero + 1;
        } else {
            $novoNumero = 1;
        }

        return $ano . str_pad($novoNumero, 6, '0', STR_PAD_LEFT);
    }

    public function criarLancamentoFinanceiro(): Lancamento
    {
        $lancamento = Lancamento::create([
            'uuid' => (string) Str::uuid(),
            'empresa_id' => $this->empresa_id,
            'usuario_id' => $this->usuario_id,
            'natureza_financeira' => Lancamento::NATUREZA_ENTRADA,
            'categoria' => Lancamento::CATEGORIA_VENDA,
            'origem' => Lancamento::ORIGEM_PDV,
            'valor_bruto' => $this->valor_total,
            'valor_liquido' => $this->valor_liquido,
            'situacao_financeira' => Lancamento::SITUACAO_PENDENTE,
            'descricao' => "Venda #{$this->numero_venda}",
            'data_vencimento' => $this->data_venda,
            'data_emissao' => $this->data_venda,
            'data_competencia' => $this->data_venda,
            'documento_origem' => $this->numero_venda,
            'documento_origem_tipo' => 'venda',
            'observacoes' => $this->observacoes,
        ]);

        // Atualizar a venda com o ID do lançamento
        $this->update(['lancamento_id' => $lancamento->id]);

        return $lancamento;
    }

    public function adicionarItem(Produto $produto, float $quantidade, float $precoUnitario, array $dados = []): VendaItem
    {
        $item = $this->itens()->create(array_merge($dados, [
            'produto_id' => $produto->id,
            'quantidade' => $quantidade,
            'valor_unitario' => $precoUnitario,
            'valor_total' => $quantidade * $precoUnitario,
            'empresa_id' => $this->empresa_id,
        ]));

        // Criar item no lançamento financeiro também
        if ($this->lancamento_id) {
            LancamentoItem::create([
                'lancamento_id' => $this->lancamento_id,
                'produto_id' => $produto->id,
                'quantidade' => $quantidade,
                'valor_unitario' => $precoUnitario,
                'empresa_id' => $this->empresa_id,
            ]);
        }

        // Atualizar estoque se o produto controla estoque
        if ($produto->controla_estoque) {
            $produto->decrement('estoque_atual', $quantidade);
        }

        // Recalcular totais da venda
        $this->recalcularTotais();

        return $item;
    }

    public function recalcularTotais(): void
    {
        $valorTotal = $this->itens()->sum('valor_total');
        $valorLiquido = $valorTotal - $this->valor_desconto;

        $this->update([
            'valor_total' => $valorTotal,
            'valor_liquido' => $valorLiquido,
        ]);

        // Atualizar também o lançamento financeiro
        if ($this->lancamento) {
            $this->lancamento->update([
                'valor_bruto' => $valorTotal,
                'valor_liquido' => $valorLiquido,
            ]);
        }
    }

    public function confirmar(): bool
    {
        $this->update(['status' => self::STATUS_CONFIRMADA]);

        // Confirmar lançamento financeiro
        if ($this->lancamento) {
            $this->lancamento->update([
                'situacao_financeira' => Lancamento::SITUACAO_CONFIRMADO
            ]);
        }

        return true;
    }

    public function cancelar(string $motivo = null): bool
    {
        $this->update([
            'status' => self::STATUS_CANCELADA,
            'observacoes' => ($this->observacoes ? $this->observacoes . "\n" : '') . "CANCELADA: " . $motivo
        ]);

        // Cancelar lançamento financeiro
        if ($this->lancamento) {
            $this->lancamento->update([
                'situacao_financeira' => Lancamento::SITUACAO_CANCELADO
            ]);
        }

        // Restaurar estoque
        foreach ($this->itens as $item) {
            if ($item->produto && $item->produto->controla_estoque) {
                $item->produto->increment('estoque_atual', $item->quantidade);
            }
        }

        return true;
    }

    /**
     * Formatters
     */
    public function getValorTotalFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_total, 2, ',', '.');
    }

    public function getValorLiquidoFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_liquido, 2, ',', '.');
    }

    public function getStatusFormatadoAttribute(): string
    {
        $status = [
            self::STATUS_PENDENTE => 'Pendente',
            self::STATUS_CONFIRMADA => 'Confirmada',
            self::STATUS_CANCELADA => 'Cancelada',
            self::STATUS_ENTREGUE => 'Entregue',
        ];

        return $status[$this->status] ?? $this->status;
    }

    public function getTipoVendaFormatadoAttribute(): string
    {
        $tipos = [
            self::TIPO_BALCAO => 'Balcão',
            self::TIPO_DELIVERY => 'Delivery',
            self::TIPO_ONLINE => 'Online',
            self::TIPO_TELEFONE => 'Telefone',
        ];

        return $tipos[$this->tipo_venda] ?? $this->tipo_venda;
    }
}