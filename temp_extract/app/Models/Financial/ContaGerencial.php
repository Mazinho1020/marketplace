<?php

namespace App\Models\Financial;

use App\Models\Core\BaseModel;
use App\Models\User;
use App\Enums\NaturezaContaEnum;
use App\Traits\HasSync;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class ContaGerencial extends BaseModel
{
    use HasSync, HasCompany;

    protected $table = 'conta_gerencial';

    protected $fillable = [
        'codigo',
        'conta_pai_id',
        'nivel',
        'nome',
        'descricao',
        'ativo',
        'ordem_exibicao',
        'permite_lancamento',
        'natureza_conta',
        'configuracoes',
        'usuario_id',
        'empresa_id',
        'classificacao_dre_id',
        'tipo_id',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'permite_lancamento' => 'boolean',
        'nivel' => 'integer',
        'ordem_exibicao' => 'integer',
        'natureza_conta' => NaturezaContaEnum::class,
        'configuracoes' => 'json',
    ];

    // RELACIONAMENTOS
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classificacaoDre(): BelongsTo
    {
        return $this->belongsTo(ClassificacaoDre::class);
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(Tipo::class);
    }

    public function contaPai(): BelongsTo
    {
        return $this->belongsTo(ContaGerencial::class, 'conta_pai_id');
    }

    public function contasFilhas(): HasMany
    {
        return $this->hasMany(ContaGerencial::class, 'conta_pai_id')
                    ->orderBy('ordem_exibicao');
    }

    public function naturezas(): BelongsToMany
    {
        return $this->belongsToMany(
            ContaGerencialNatureza::class,
            'conta_gerencial_naturezas',
            'conta_gerencial_id',
            'natureza_id'
        )->withPivot(['empresa_id'])
         ->withTimestamps();
    }

    public function lancamentos(): HasMany
    {
        return $this->hasMany(\App\Models\Financial\Lancamento::class, 'conta_gerencial_id');
    }

    // SCOPES
    public function scopeAtivas(Builder $query): Builder
    {
        return $query->where('ativo', true);
    }

    public function scopePermiteLancamento(Builder $query): Builder
    {
        return $query->where('permite_lancamento', true);
    }

    public function scopeRaizes(Builder $query): Builder
    {
        return $query->whereNull('conta_pai_id');
    }

    public function scopePorClassificacao(Builder $query, int $classificacaoId): Builder
    {
        return $query->where('classificacao_dre_id', $classificacaoId);
    }

    public function scopePorTipo(Builder $query, int $tipoId): Builder
    {
        return $query->where('tipo_id', $tipoId);
    }

    public function scopePorNatureza(Builder $query, NaturezaContaEnum $natureza): Builder
    {
        return $query->where('natureza_conta', $natureza);
    }

    // ACCESSORS
    public function getCodigoCompletoAttribute(): string
    {
        if ($this->conta_pai_id) {
            return $this->contaPai->codigo_completo . '.' . $this->codigo;
        }
        return $this->codigo ?? '';
    }

    public function getNomeCompletoAttribute(): string
    {
        $caminho = [];
        $conta = $this;
        
        while ($conta) {
            array_unshift($caminho, $conta->nome);
            $conta = $conta->contaPai;
        }
        
        return implode(' > ', $caminho);
    }

    public function getTemFilhasAttribute(): bool
    {
        return $this->contasFilhas()->exists();
    }

    public function getSaldoAtualAttribute(): float
    {
        return $this->calcularSaldo();
    }

    // MÉTODOS DE NEGÓCIO
    public function adicionarContaFilha(array $dados): self
    {
        $dados['conta_pai_id'] = $this->id;
        $dados['nivel'] = $this->nivel + 1;
        $dados['empresa_id'] = $this->empresa_id;
        $dados['classificacao_dre_id'] = $dados['classificacao_dre_id'] ?? $this->classificacao_dre_id;
        $dados['tipo_id'] = $dados['tipo_id'] ?? $this->tipo_id;
        
        return self::create($dados);
    }

    public function vincularNatureza(int $naturezaId): void
    {
        $this->naturezas()->syncWithoutDetaching([
            $naturezaId => ['empresa_id' => $this->empresa_id]
        ]);
    }

    public function desvincularNatureza(int $naturezaId): void
    {
        $this->naturezas()->detach($naturezaId);
    }

    public function calcularSaldo(?\DateTime $dataInicio = null, ?\DateTime $dataFim = null): float
    {
        $query = $this->lancamentos();
        
        if ($dataInicio) {
            $query->where('data', '>=', $dataInicio);
        }
        
        if ($dataFim) {
            $query->where('data', '<=', $dataFim);
        }

        $total = $query->sum('valor');
        
        // Aplicar natureza da conta
        if ($this->natureza_conta) {
            $total *= $this->natureza_conta->sinal();
        }

        return $total;
    }

    public function desativar(): bool
    {
        if ($this->tem_filhas) {
            throw new \InvalidArgumentException('Não é possível desativar uma conta que possui contas filhas ativas');
        }

        if ($this->lancamentos()->exists()) {
            throw new \InvalidArgumentException('Não é possível desativar uma conta que possui lançamentos');
        }

        return $this->update(['ativo' => false]);
    }

    public function moverPara(ContaGerencial $novoPai): bool
    {
        if ($this->isAncestralDe($novoPai)) {
            throw new \InvalidArgumentException('Não é possível mover uma conta para dentro de sua própria hierarquia');
        }

        $this->update([
            'conta_pai_id' => $novoPai->id,
            'nivel' => $novoPai->nivel + 1,
        ]);

        return true;
    }

    private function isAncestralDe(ContaGerencial $conta): bool
    {
        $ancestral = $conta->contaPai;
        
        while ($ancestral) {
            if ($ancestral->id === $this->id) {
                return true;
            }
            $ancestral = $ancestral->contaPai;
        }
        
        return false;
    }

    // BOOT
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($conta) {
            if (!$conta->codigo && $conta->conta_pai_id) {
                $conta->codigo = $conta->gerarProximoCodigo();
            }
        });
    }

    private function gerarProximoCodigo(): string
    {
        $ultimaFilha = $this->contaPai
                           ->contasFilhas()
                           ->orderBy('codigo', 'desc')
                           ->first();

        $proximoNumero = $ultimaFilha ? (int)$ultimaFilha->codigo + 1 : 1;
        return str_pad($proximoNumero, 3, '0', STR_PAD_LEFT);
    }
}