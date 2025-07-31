<?php

namespace App\Models\Fidelidade;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CartaoFidelidade extends Model
{
    protected $table = 'cartoes_fidelidade';

    protected $fillable = [
        'programa_fidelidade_id',
        'user_id',
        'codigo',
        'saldo_pontos',
        'pontos_acumulados',
        'pontos_resgatados',
        'ativo',
        'data_ativacao',
        'data_ultima_transacao'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'data_ativacao' => 'datetime',
        'data_ultima_transacao' => 'datetime',
        'saldo_pontos' => 'integer',
        'pontos_acumulados' => 'integer',
        'pontos_resgatados' => 'integer'
    ];

    /**
     * Relacionamento com ProgramaFidelidade
     */
    public function programa(): BelongsTo
    {
        return $this->belongsTo(ProgramaFidelidade::class, 'programa_fidelidade_id');
    }

    /**
     * Relacionamento com Cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Cliente::class, 'cliente_id');
    }

    /**
     * Relacionamento com TransacoesPontos
     */
    public function transacoes(): HasMany
    {
        return $this->hasMany(TransacaoPontos::class);
    }

    /**
     * Scope para cartões ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Adicionar pontos ao cartão
     */
    public function adicionarPontos(int $pontos, string $descricao = null, array $metadata = []): TransacaoPontos
    {
        $transacao = $this->transacoes()->create([
            'programa_fidelidade_id' => $this->programa_fidelidade_id,
            'tipo' => 'acumulo',
            'pontos' => $pontos,
            'descricao' => $descricao,
            'metadata' => $metadata
        ]);

        $this->increment('saldo_pontos', $pontos);
        $this->increment('pontos_acumulados', $pontos);
        $this->update(['data_ultima_transacao' => now()]);

        return $transacao;
    }

    /**
     * Resgatar pontos do cartão
     */
    public function resgatarPontos(int $pontos, string $descricao = null, array $metadata = []): TransacaoPontos
    {
        if ($this->saldo_pontos < $pontos) {
            throw new \Exception('Saldo insuficiente de pontos.');
        }

        $transacao = $this->transacoes()->create([
            'programa_fidelidade_id' => $this->programa_fidelidade_id,
            'tipo' => 'resgate',
            'pontos' => $pontos,
            'descricao' => $descricao,
            'metadata' => $metadata
        ]);

        $this->decrement('saldo_pontos', $pontos);
        $this->increment('pontos_resgatados', $pontos);
        $this->update(['data_ultima_transacao' => now()]);

        return $transacao;
    }

    /**
     * Verificar se tem pontos suficientes
     */
    public function temPontosSuficientes(int $pontos): bool
    {
        return $this->saldo_pontos >= $pontos;
    }

    /**
     * Gerar código único do cartão
     */
    public static function gerarCodigo(): string
    {
        do {
            $codigo = 'FIDELIDADE' . str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        } while (self::where('codigo', $codigo)->exists());

        return $codigo;
    }
}
