<?php

namespace App\Models\Fidelidade;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FidelidadeCarteira extends Model
{
    use SoftDeletes;

    protected $table = 'fidelidade_carteiras';

    protected $fillable = [
        'cliente_id',
        'empresa_id',
        'saldo_cashback',
        'saldo_creditos',
        'saldo_bloqueado',
        'saldo_total_disponivel',
        'nivel_atual',
        'xp_total',
        'status',
        'sync_status',
        'sync_data'
    ];

    protected $casts = [
        'saldo_cashback' => 'decimal:2',
        'saldo_creditos' => 'decimal:2',
        'saldo_bloqueado' => 'decimal:2',
        'saldo_total_disponivel' => 'decimal:2',
        'xp_total' => 'integer',
        'sync_data' => 'datetime'
    ];

    const NIVEIS = [
        'bronze' => 'Bronze',
        'prata' => 'Prata',
        'ouro' => 'Ouro',
        'diamond' => 'Diamond'
    ];

    const STATUS = [
        'ativa' => 'Ativa',
        'bloqueada' => 'Bloqueada',
        'suspensa' => 'Suspensa'
    ];

    /**
     * Relacionamento com Cliente (User)
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Cliente::class, 'cliente_id');
    }

    /**
     * Relacionamento com Business
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Business\Business::class, 'empresa_id');
    }

    /**
     * Relacionamento com transações de cashback
     */
    public function transacoesCashback(): HasMany
    {
        return $this->hasMany(FidelidadeCashbackTransacao::class, 'cliente_id', 'cliente_id');
    }

    /**
     * Relacionamento com créditos
     */
    public function creditos(): HasMany
    {
        return $this->hasMany(FidelidadeCredito::class, 'cliente_id', 'cliente_id')
            ->where('empresa_id', $this->empresa_id);
    }

    /**
     * Relacionamento com conquistas
     */
    public function conquistas(): HasMany
    {
        return $this->hasMany(FidelidadeClienteConquista::class, 'cliente_id', 'cliente_id');
    }

    /**
     * Scope para carteiras ativas
     */
    public function scopeAtivas($query)
    {
        return $query->where('status', 'ativa');
    }

    /**
     * Scope para empresa específica
     */
    public function scopeForEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Scope por nível
     */
    public function scopeNivel($query, $nivel)
    {
        return $query->where('nivel_atual', $nivel);
    }

    /**
     * Atualizar saldo total
     */
    public function atualizarSaldoTotal()
    {
        $this->saldo_total_disponivel = $this->saldo_cashback + $this->saldo_creditos - $this->saldo_bloqueado;
        $this->save();
    }

    /**
     * Verificar se pode ser promovido de nível
     */
    public function verificarPromocaoNivel()
    {
        $novoNivel = $this->calcularNivelPorXP($this->xp_total);

        if ($novoNivel !== $this->nivel_atual) {
            $this->nivel_atual = $novoNivel;
            $this->save();
            return true;
        }

        return false;
    }

    /**
     * Calcular nível baseado no XP
     */
    private function calcularNivelPorXP($xp)
    {
        if ($xp >= 15000) return 'diamond';
        if ($xp >= 5000) return 'ouro';
        if ($xp >= 1000) return 'prata';
        return 'bronze';
    }

    /**
     * Adicionar XP
     */
    public function adicionarXP($xp)
    {
        $this->xp_total += $xp;
        $this->verificarPromocaoNivel();
        $this->save();
    }

    /**
     * Obter descrição do nível
     */
    public function getNivelDescricaoAttribute()
    {
        return self::NIVEIS[$this->nivel_atual] ?? $this->nivel_atual;
    }

    /**
     * Obter descrição do status
     */
    public function getStatusDescricaoAttribute()
    {
        return self::STATUS[$this->status] ?? $this->status;
    }
}
