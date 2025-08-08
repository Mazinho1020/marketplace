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

    public function configuracao()
    {
        return $this->belongsTo(ProdutoConfiguracao::class, 'produto_configuracao_id');
    }
}
