<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresas';

    protected $fillable = [
        'uuid',
        'razao_social',
        'nome_fantasia',
        'trade_name',
        'document',
        'document_type',
        'cnpj',
        'inscricao_estadual',
        'inscricao_municipal',
        'data_abertura',
        'telefone',
        'celular',
        'email',
        'site',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
        'pais',
        'regime_tributario',
        'optante_simples',
        'incentivo_fiscal',
        'cnae_principal',
        'banco_nome',
        'banco_agencia',
        'banco_conta',
        'banco_tipo_conta',
        'banco_pix',
        'moeda_padrao',
        'fuso_horario',
        'idioma_padrao',
        'logo_url',
        'status',
        'subscription_plan',
        'trial_ends_at',
        'subscription_ends_at',
        'cor_principal',
        'ativo',
        'data_cadastro',
        'data_atualizacao',
        'sync_data',
        'sync_hash',
        'sync_status'
    ];

    protected $casts = [
        'data_abertura' => 'date',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'data_cadastro' => 'datetime',
        'data_atualizacao' => 'datetime',
        'optante_simples' => 'boolean',
        'incentivo_fiscal' => 'boolean',
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'ativo')->where('ativo', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inativo')->orWhere('ativo', false);
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspenso');
    }

    // Status badges
    public function getStatusBadgeClass()
    {
        if (!$this->ativo) return 'secondary';

        return match ($this->status) {
            'ativo' => 'success',
            'inativo' => 'secondary',
            'suspenso' => 'warning',
            'bloqueado' => 'danger',
            default => 'secondary'
        };
    }

    public function getPlanoBadgeClass()
    {
        return match ($this->subscription_plan) {
            'basico' => 'info',
            'pro' => 'primary',
            'premium' => 'warning',
            'enterprise' => 'success',
            default => 'secondary'
        };
    }

    // Verificações
    public function isActive()
    {
        return $this->status === 'ativo' && $this->ativo;
    }

    public function isVencido()
    {
        return $this->subscription_ends_at && $this->subscription_ends_at->isPast();
    }

    public function diasParaVencimento()
    {
        if (!$this->subscription_ends_at) return null;
        return now()->diffInDays($this->subscription_ends_at, false);
    }

    // Accessors para compatibilidade
    public function getEnderecoCompletoAttribute()
    {
        $endereco = collect([
            $this->logradouro,
            $this->numero,
            $this->complemento,
            $this->bairro,
            $this->cidade,
            $this->uf
        ])->filter()->implode(', ');

        return $endereco ?: 'Endereço não informado';
    }

    public function getPlanoAttribute()
    {
        return $this->subscription_plan;
    }

    public function getEstadoAttribute()
    {
        return $this->uf;
    }

    public function getEnderecoAttribute()
    {
        return $this->logradouro . ($this->numero ? ', ' . $this->numero : '');
    }

    // Relationships
    public function usuarios()
    {
        return $this->hasMany(\App\Models\User::class, 'empresa_id');
    }

    // Para estatísticas (se tiver tabelas relacionadas)
    public function getQuantidadeUsuariosAttribute()
    {
        return $this->usuarios()->count();
    }
}
