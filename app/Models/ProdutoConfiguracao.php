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

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function itens()
    {
        return $this->hasMany(ProdutoConfiguracaoItem::class);
    }
}
