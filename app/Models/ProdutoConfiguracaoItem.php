<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProdutoConfiguracaoItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'produto_configuracao_itens';

    protected $fillable = [
        'empresa_id',
        'produto_configuracao_id',
        'nome',
        'descricao',
        'valor_adicional',
        'imagem',
        'ordem',
        'disponivel',
        'padrao',
        'sync_status'
    ];

    protected $casts = [
        'valor_adicional' => 'decimal:2',
        'disponivel' => 'boolean',
        'padrao' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function configuracao()
    {
        return $this->belongsTo(ProdutoConfiguracao::class, 'produto_configuracao_id');
    }

    // Scopes
    public function scopeDisponiveis($query)
    {
        return $query->where('disponivel', true);
    }

    public function scopePadrao($query)
    {
        return $query->where('padrao', true);
    }

    public function scopeOrdenado($query)
    {
        return $query->orderBy('ordem')->orderBy('nome');
    }

    // Accessors
    public function getValorAdicionalFormatadoAttribute()
    {
        if ($this->valor_adicional == 0) {
            return 'Gratuito';
        }

        return ($this->valor_adicional > 0 ? '+' : '') . 'R$ ' . number_format($this->valor_adicional, 2, ',', '.');
    }

    public function getStatusBadgeAttribute()
    {
        return $this->disponivel
            ? '<span class="badge bg-success">Disponível</span>'
            : '<span class="badge bg-secondary">Indisponível</span>';
    }

    public function getPadraoTextAttribute()
    {
        return $this->padrao ? 'Sim' : 'Não';
    }

    public function getPadraoBadgeAttribute()
    {
        return $this->padrao
            ? '<span class="badge bg-warning">Padrão</span>'
            : '';
    }

    // Métodos auxiliares
    public function isPadrao()
    {
        return $this->padrao;
    }

    public function isDisponivel()
    {
        return $this->disponivel;
    }

    public function isGratuito()
    {
        return $this->valor_adicional == 0;
    }

    public function temDesconto()
    {
        return $this->valor_adicional < 0;
    }

    public function temAcrescimo()
    {
        return $this->valor_adicional > 0;
    }
}
