<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProdutoRelacionado extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'produto_relacionados';

    protected $fillable = [
        'empresa_id',
        'produto_id',
        'produto_relacionado_id',
        'tipo_relacao',
        'ordem',
        'ativo',
        'sync_status'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'ordem' => 'integer',
    ];

    // Tipos de relação disponíveis
    const TIPOS_RELACAO = [
        'similar' => 'Produto Similar',
        'complementar' => 'Produto Complementar',
        'acessorio' => 'Acessório',
        'substituto' => 'Produto Substituto',
        'kit' => 'Componente de Kit',
        'cross-sell' => 'Cross-sell',
        'up-sell' => 'Up-sell'
    ];

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function produtoRelacionado()
    {
        return $this->belongsTo(Produto::class, 'produto_relacionado_id');
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo_relacao', $tipo);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('ordem', 'asc');
    }

    // Acessadores
    public function getTipoRelacaoLabelAttribute()
    {
        return self::TIPOS_RELACAO[$this->tipo_relacao] ?? $this->tipo_relacao;
    }

    public function getStatusBadgeAttribute()
    {
        return $this->ativo ?
            '<span class="badge badge-success">Ativo</span>' :
            '<span class="badge badge-secondary">Inativo</span>';
    }

    // Métodos estáticos para facilitar consultas
    public static function getTiposRelacao()
    {
        return self::TIPOS_RELACAO;
    }

    public static function getProdutosSimilares($produtoId, $empresaId = null)
    {
        $query = self::with('produtoRelacionado')
            ->where('produto_id', $produtoId)
            ->where('tipo_relacao', 'similar')
            ->where('ativo', true)
            ->ordenados();

        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        return $query->get();
    }

    public static function getProdutosComplementares($produtoId, $empresaId = null)
    {
        $query = self::with('produtoRelacionado')
            ->where('produto_id', $produtoId)
            ->where('tipo_relacao', 'complementar')
            ->where('ativo', true)
            ->ordenados();

        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        return $query->get();
    }

    public static function getCrossSell($produtoId, $empresaId = null)
    {
        $query = self::with('produtoRelacionado')
            ->where('produto_id', $produtoId)
            ->where('tipo_relacao', 'cross-sell')
            ->where('ativo', true)
            ->ordenados();

        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        return $query->get();
    }

    public static function getUpSell($produtoId, $empresaId = null)
    {
        $query = self::with('produtoRelacionado')
            ->where('produto_id', $produtoId)
            ->where('tipo_relacao', 'up-sell')
            ->where('ativo', true)
            ->ordenados();

        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        return $query->get();
    }
}
