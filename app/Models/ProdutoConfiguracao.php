<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProdutoConfiguracao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'produto_configuracoes';

    protected $fillable = [
        'empresa_id',
        'produto_id',
        'nome',
        'descricao',
        'tipo_configuracao',
        'obrigatorio',
        'permite_multiplos',
        'qtd_minima',
        'qtd_maxima',
        'tipo_calculo',
        'ordem',
        'ativo',
        'sync_status'
    ];

    protected $casts = [
        'obrigatorio' => 'boolean',
        'permite_multiplos' => 'boolean',
        'ativo' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function itens()
    {
        return $this->hasMany(ProdutoConfiguracaoItem::class);
    }

    public function itensAtivos()
    {
        return $this->hasMany(ProdutoConfiguracaoItem::class)->where('disponivel', true);
    }

    public function itensPadrao()
    {
        return $this->hasMany(ProdutoConfiguracaoItem::class)->where('padrao', true);
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeObrigatorios($query)
    {
        return $query->where('obrigatorio', true);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_configuracao', $tipo);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeOrdenado($query)
    {
        return $query->orderBy('ordem')->orderBy('nome');
    }

    // Accessors
    public function getTipoDescricaoAttribute()
    {
        $tipos = [
            'tamanho' => 'Tamanho',
            'sabor' => 'Sabor',
            'ingrediente' => 'Ingrediente',
            'complemento' => 'Complemento',
            'personalizado' => 'Personalizado'
        ];

        return $tipos[$this->tipo_configuracao] ?? $this->tipo_configuracao;
    }

    public function getTipoCalculoDescricaoAttribute()
    {
        $tipos = [
            'soma' => 'Somar valores',
            'media' => 'Média dos valores',
            'maximo' => 'Valor máximo',
            'substituicao' => 'Substituir valor base'
        ];

        return $tipos[$this->tipo_calculo] ?? $this->tipo_calculo;
    }

    public function getObrigatorioTextAttribute()
    {
        return $this->obrigatorio ? 'Sim' : 'Não';
    }

    public function getObrigatorioBadgeAttribute()
    {
        return $this->obrigatorio
            ? '<span class="badge bg-danger">Obrigatório</span>'
            : '<span class="badge bg-secondary">Opcional</span>';
    }

    public function getStatusBadgeAttribute()
    {
        return $this->ativo
            ? '<span class="badge bg-success">Ativo</span>'
            : '<span class="badge bg-secondary">Inativo</span>';
    }

    public function getPermiteMultiplosTextAttribute()
    {
        return $this->permite_multiplos ? 'Sim' : 'Não';
    }

    public function getQuantidadeItensAttribute()
    {
        return $this->itens()->count();
    }

    public function getQuantidadeItensAtivosAttribute()
    {
        return $this->itensAtivos()->count();
    }

    // Métodos auxiliares
    public function isObrigatorio()
    {
        return $this->obrigatorio;
    }

    public function isAtivo()
    {
        return $this->ativo;
    }

    public function permiteMultiplos()
    {
        return $this->permite_multiplos;
    }

    public function temLimiteQuantidade()
    {
        return $this->qtd_minima || $this->qtd_maxima;
    }

    public function validarQuantidade($quantidade)
    {
        if ($this->qtd_minima && $quantidade < $this->qtd_minima) {
            return false;
        }

        if ($this->qtd_maxima && $quantidade > $this->qtd_maxima) {
            return false;
        }

        return true;
    }

    public function calcularPreco($itens_selecionados)
    {
        $precos = [];

        foreach ($itens_selecionados as $item_id) {
            $item = $this->itens()->find($item_id);
            if ($item && $item->disponivel) {
                $precos[] = $item->valor_adicional;
            }
        }

        if (empty($precos)) {
            return 0;
        }

        switch ($this->tipo_calculo) {
            case 'soma':
                return array_sum($precos);
            case 'media':
                return array_sum($precos) / count($precos);
            case 'maximo':
                return max($precos);
            case 'substituicao':
                return end($precos); // Último valor
            default:
                return array_sum($precos);
        }
    }
}
