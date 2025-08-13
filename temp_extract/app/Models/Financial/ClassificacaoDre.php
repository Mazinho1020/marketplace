<?php

namespace App\Models\Financial;

use App\Models\Core\BaseModel;
use App\Traits\HasSync;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class ClassificacaoDre extends BaseModel
{
    use HasSync, HasCompany;

    protected $table = 'classificacoes_dre';

    protected $fillable = [
        'codigo',
        'nivel',
        'classificacao_pai_id',
        'nome',
        'descricao',
        'tipo_id',
        'ativo',
        'ordem_exibicao',
        'empresa_id',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'nivel' => 'integer',
        'ordem_exibicao' => 'integer',
    ];

    // RELACIONAMENTOS
    public function tipo(): BelongsTo
    {
        return $this->belongsTo(Tipo::class);
    }

    public function classificacaoPai(): BelongsTo
    {
        return $this->belongsTo(ClassificacaoDre::class, 'classificacao_pai_id');
    }

    public function classificacoesFilhas(): HasMany
    {
        return $this->hasMany(ClassificacaoDre::class, 'classificacao_pai_id')
                    ->orderBy('ordem_exibicao');
    }

    public function contasGerenciais(): HasMany
    {
        return $this->hasMany(ContaGerencial::class, 'classificacao_dre_id');
    }

    // SCOPES
    public function scopeAtivas(Builder $query): Builder
    {
        return $query->where('ativo', true);
    }

    public function scopeRaizes(Builder $query): Builder
    {
        return $query->whereNull('classificacao_pai_id');
    }

    public function scopePorNivel(Builder $query, int $nivel): Builder
    {
        return $query->where('nivel', $nivel);
    }

    public function scopePorTipo(Builder $query, int $tipoId): Builder
    {
        return $query->where('tipo_id', $tipoId);
    }

    // ACCESSORS
    public function getCodigoCompletoAttribute(): string
    {
        if ($this->classificacao_pai_id) {
            return $this->classificacaoPai->codigo_completo . '.' . $this->codigo;
        }
        return $this->codigo ?? '';
    }

    public function getNomeCompletoAttribute(): string
    {
        $caminho = [];
        $classificacao = $this;
        
        while ($classificacao) {
            array_unshift($caminho, $classificacao->nome);
            $classificacao = $classificacao->classificacaoPai;
        }
        
        return implode(' > ', $caminho);
    }

    public function getTemFilhasAttribute(): bool
    {
        return $this->classificacoesFilhas()->exists();
    }

    // MÉTODOS DE NEGÓCIO
    public function adicionarFilha(array $dados): self
    {
        $dados['classificacao_pai_id'] = $this->id;
        $dados['nivel'] = $this->nivel + 1;
        $dados['empresa_id'] = $this->empresa_id;
        $dados['tipo_id'] = $dados['tipo_id'] ?? $this->tipo_id;
        
        return self::create($dados);
    }

    public function desativar(): bool
    {
        if ($this->tem_filhas) {
            throw new \InvalidArgumentException('Não é possível desativar uma classificação que possui filhas ativas');
        }

        if ($this->contasGerenciais()->exists()) {
            throw new \InvalidArgumentException('Não é possível desativar uma classificação que possui contas vinculadas');
        }

        return $this->update(['ativo' => false]);
    }

    public function calcularTotalLancamentos(?\DateTime $dataInicio = null, ?\DateTime $dataFim = null): float
    {
        // Implementar cálculo dos lançamentos desta classificação
        $query = $this->contasGerenciais()
                      ->with(['lancamentos' => function($q) use ($dataInicio, $dataFim) {
                          if ($dataInicio) $q->where('data', '>=', $dataInicio);
                          if ($dataFim) $q->where('data', '<=', $dataFim);
                      }]);

        return $query->get()
                    ->flatMap->lancamentos
                    ->sum('valor');
    }

    // BOOT
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($classificacao) {
            if (!$classificacao->codigo && $classificacao->classificacao_pai_id) {
                $classificacao->codigo = $classificacao->gerarProximoCodigo();
            }
        });
    }

    private function gerarProximoCodigo(): string
    {
        $ultimaFilha = $this->classificacaoPai
                           ->classificacoesFilhas()
                           ->orderBy('codigo', 'desc')
                           ->first();

        $proximoNumero = $ultimaFilha ? (int)$ultimaFilha->codigo + 1 : 1;
        return str_pad($proximoNumero, 2, '0', STR_PAD_LEFT);
    }
}