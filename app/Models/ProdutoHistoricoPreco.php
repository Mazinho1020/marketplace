<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoHistoricoPreco extends Model
{
    use HasFactory;

    protected $table = 'produto_historico_precos';

    protected $fillable = [
        'empresa_id',
        'produto_id',
        'variacao_id',
        'preco_compra_anterior',
        'preco_compra_novo',
        'preco_venda_anterior',
        'preco_venda_novo',
        'margem_anterior',
        'margem_nova',
        'motivo',
        'usuario_id',
        'data_alteracao',
        'sync_status'
    ];

    protected $casts = [
        'preco_compra_anterior' => 'decimal:2',
        'preco_compra_novo' => 'decimal:2',
        'preco_venda_anterior' => 'decimal:2',
        'preco_venda_novo' => 'decimal:2',
        'margem_anterior' => 'decimal:2',
        'margem_nova' => 'decimal:2',
        'data_alteracao' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopePorProduto($query, $produtoId)
    {
        return $query->where('produto_id', $produtoId);
    }

    public function scopePorPeriodo($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('data_alteracao', [$dataInicio, $dataFim]);
    }

    public function scopePorMotivo($query, $motivo)
    {
        return $query->where('motivo', $motivo);
    }

    public function scopeOrdenado($query, $ordem = 'desc')
    {
        return $query->orderBy('data_alteracao', $ordem);
    }

    // Métodos úteis
    public function getVariacaoPercentualAttribute()
    {
        if ($this->preco_venda_anterior == 0) {
            return 0;
        }

        return (($this->preco_venda_novo - $this->preco_venda_anterior) / $this->preco_venda_anterior) * 100;
    }

    public function getVariacaoMonetariaAttribute()
    {
        return $this->preco_venda_novo - $this->preco_venda_anterior;
    }

    public function getTipoVariacaoAttribute()
    {
        if ($this->preco_venda_novo > $this->preco_venda_anterior) {
            return 'aumento';
        } elseif ($this->preco_venda_novo < $this->preco_venda_anterior) {
            return 'reducao';
        } else {
            return 'sem_alteracao';
        }
    }

    public function getMotivoDescricaoAttribute()
    {
        $motivos = self::getMotivos();
        return $motivos[$this->motivo] ?? 'Não informado';
    }

    // Métodos estáticos
    public static function getMotivos()
    {
        return [
            'ajuste_comercial' => 'Ajuste Comercial',
            'promocao' => 'Promoção/Desconto',
            'aumento_fornecedor' => 'Aumento do Fornecedor',
            'reducao_custo' => 'Redução de Custo',
            'sazonalidade' => 'Sazonalidade',
            'concorrencia' => 'Concorrência',
            'estoque_baixo' => 'Estoque Baixo',
            'produto_novo' => 'Produto Novo',
            'descontinuado' => 'Produto Descontinuado',
            'erro_correcao' => 'Correção de Erro',
            'outros' => 'Outros'
        ];
    }

    public static function registrarAlteracao($empresaId, $produtoId, $usuarioId, $precoAnterior, $precoNovo, $motivo = 'ajuste_comercial', $observacoes = null)
    {
        return self::create([
            'empresa_id' => $empresaId,
            'produto_id' => $produtoId,
            'usuario_id' => $usuarioId,
            'preco_anterior' => $precoAnterior,
            'preco_novo' => $precoNovo,
            'motivo' => $motivo,
            'observacoes' => $observacoes,
            'data_alteracao' => now(),
            'sync_status' => 'pendente'
        ]);
    }

    // Análises estatísticas
    public static function getEstatisticasPorPeriodo($empresaId, $dataInicio, $dataFim)
    {
        $historicos = self::porEmpresa($empresaId)
            ->porPeriodo($dataInicio, $dataFim)
            ->get();

        $aumentos = $historicos->where('tipo_variacao', 'aumento');
        $reducoes = $historicos->where('tipo_variacao', 'reducao');

        return [
            'total_alteracoes' => $historicos->count(),
            'aumentos' => [
                'quantidade' => $aumentos->count(),
                'percentual' => $historicos->count() > 0 ? ($aumentos->count() / $historicos->count()) * 100 : 0,
                'valor_medio' => $aumentos->avg('variacao_monetaria') ?? 0
            ],
            'reducoes' => [
                'quantidade' => $reducoes->count(),
                'percentual' => $historicos->count() > 0 ? ($reducoes->count() / $historicos->count()) * 100 : 0,
                'valor_medio' => abs($reducoes->avg('variacao_monetaria') ?? 0)
            ],
            'maior_aumento' => $aumentos->max('variacao_monetaria') ?? 0,
            'maior_reducao' => abs($reducoes->min('variacao_monetaria') ?? 0),
            'produtos_afetados' => $historicos->unique('produto_id')->count()
        ];
    }

    public static function getProdutosMaisAlterados($empresaId, $limite = 10, $periodo = 30)
    {
        $dataInicio = now()->subDays($periodo);

        return self::porEmpresa($empresaId)
            ->where('data_alteracao', '>=', $dataInicio)
            ->with('produto')
            ->selectRaw('produto_id, COUNT(*) as total_alteracoes')
            ->groupBy('produto_id')
            ->orderBy('total_alteracoes', 'desc')
            ->limit($limite)
            ->get();
    }

    public static function getRelatorioVariacoes($empresaId, $dataInicio, $dataFim, $agrupamento = 'dia')
    {
        $formatoData = match ($agrupamento) {
            'hora' => '%Y-%m-%d %H:00:00',
            'dia' => '%Y-%m-%d',
            'semana' => '%Y-%u',
            'mes' => '%Y-%m',
            default => '%Y-%m-%d'
        };

        return self::porEmpresa($empresaId)
            ->porPeriodo($dataInicio, $dataFim)
            ->selectRaw("DATE_FORMAT(data_alteracao, '{$formatoData}') as periodo")
            ->selectRaw('COUNT(*) as total_alteracoes')
            ->selectRaw('SUM(CASE WHEN preco_novo > preco_anterior THEN 1 ELSE 0 END) as aumentos')
            ->selectRaw('SUM(CASE WHEN preco_novo < preco_anterior THEN 1 ELSE 0 END) as reducoes')
            ->selectRaw('AVG(preco_novo - preco_anterior) as variacao_media')
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->get();
    }

    // Método para limpar histórico antigo (manutenção)
    public static function limparHistoricoAntigo($empresaId, $diasManter = 365)
    {
        $dataLimite = now()->subDays($diasManter);

        return self::porEmpresa($empresaId)
            ->where('data_alteracao', '<', $dataLimite)
            ->delete();
    }

    // Método para exportar histórico
    public static function exportarHistorico($empresaId, $dataInicio, $dataFim, $formato = 'array')
    {
        $query = self::with(['produto', 'usuario'])
            ->porEmpresa($empresaId)
            ->porPeriodo($dataInicio, $dataFim)
            ->ordenado();

        $dados = $query->get()->map(function ($item) {
            return [
                'data' => $item->data_alteracao->format('d/m/Y H:i:s'),
                'produto' => $item->produto->nome,
                'sku' => $item->produto->sku,
                'preco_anterior' => 'R$ ' . number_format($item->preco_anterior, 2, ',', '.'),
                'preco_novo' => 'R$ ' . number_format($item->preco_novo, 2, ',', '.'),
                'variacao' => ($item->variacao_monetaria >= 0 ? '+' : '') . 'R$ ' . number_format($item->variacao_monetaria, 2, ',', '.'),
                'percentual' => ($item->variacao_percentual >= 0 ? '+' : '') . number_format($item->variacao_percentual, 2, ',', '.') . '%',
                'motivo' => $item->motivo_descricao,
                'usuario' => $item->usuario->name ?? 'Sistema',
                'observacoes' => $item->observacoes
            ];
        });

        return $formato === 'csv' ? $dados->toCsv() : $dados->toArray();
    }
}
