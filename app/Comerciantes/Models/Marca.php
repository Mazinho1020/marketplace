<?php

namespace App\Comerciantes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Model para a tabela marcas
 * Representa uma marca que pode ter várias empresas/unidades
 * Exemplo: "Pizzaria Tradição" é uma marca que tem as unidades:
 * - Pizzaria Tradição Concórdia
 * - Pizzaria Tradição Praça Central
 */
class Marca extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',                 // "Pizzaria Tradição"
        'slug',                 // "pizzaria-tradicao"
        'descricao',            // Descrição da marca
        'logo_url',             // URL do logo
        'identidade_visual',    // JSON com cores, fontes, etc
        'pessoa_fisica_id',     // ID do proprietário (empresa_usuarios.id)
        'status',               // ativa, inativa, suspensa
        'configuracoes'         // JSON com configurações da marca
    ];

    protected $casts = [
        'identidade_visual' => 'array',
        'configuracoes' => 'array',
    ];

    /**
     * Boot do model - eventos automáticos
     */
    protected static function boot()
    {
        parent::boot();

        // Gera automaticamente o slug baseado no nome
        static::creating(function ($marca) {
            if (empty($marca->slug)) {
                $marca->slug = Str::slug($marca->nome);
            }
        });
    }

    /**
     * RELACIONAMENTOS
     */

    /**
     * Proprietário da marca (pessoa física)
     */
    public function proprietario(): BelongsTo
    {
        return $this->belongsTo(EmpresaUsuario::class, 'pessoa_fisica_id');
    }

    /**
     * Todas as empresas desta marca
     */
    public function empresas(): HasMany
    {
        return $this->hasMany(Empresa::class);
    }

    /**
     * Apenas empresas ativas desta marca
     */
    public function empresasAtivas(): HasMany
    {
        return $this->empresas()->where('status', 'ativa');
    }

    /**
     * SCOPES (filtros de query)
     */

    public function scopeAtivas($query)
    {
        return $query->where('status', 'ativa');
    }

    public function scopeByProprietario($query, $userId)
    {
        return $query->where('pessoa_fisica_id', $userId);
    }

    /**
     * MÉTODOS AUXILIARES
     */

    /**
     * Conta total de empresas desta marca
     */
    public function getEmpresasCountAttribute(): int
    {
        return $this->empresas()->count();
    }

    /**
     * Conta empresas ativas desta marca
     */
    public function getEmpresasAtivasCountAttribute(): int
    {
        return $this->empresasAtivas()->count();
    }

    /**
     * Retorna a URL do logo ou uma imagem padrão
     */
    public function getLogoUrlCompletoAttribute(): string
    {
        if ($this->logo_url) {
            return asset('storage/' . $this->logo_url);
        }

        return asset('images/defaults/marca-default.png'); // Imagem padrão
    }

    /**
     * Retorna as cores da identidade visual
     */
    public function getCorPrimariaAttribute(): string
    {
        return $this->identidade_visual['cor_primaria'] ?? '#2ECC71';
    }

    public function getCorSecundariaAttribute(): string
    {
        return $this->identidade_visual['cor_secundaria'] ?? '#27AE60';
    }
}
