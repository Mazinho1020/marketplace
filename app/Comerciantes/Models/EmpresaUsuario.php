<?php

namespace App\Comerciantes\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;

/**
 * Model para a tabela empresa_usuarios (sua tabela existente)
 * Representa as pessoas físicas que podem ser:
 * - Proprietários de marcas
 * - Proprietários de empresas
 * - Colaboradores vinculados a empresas
 */
class EmpresaUsuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'empresa_usuarios';

    protected $fillable = [
        'uuid',
        'username',
        'nome',
        'email',
        'senha',           // Campo de senha na sua tabela
        'empresa_id',      // Campo existente (mantemos compatibilidade)
        'perfil_id',       // Campo existente (mantemos compatibilidade)
        'status',
        'telefone',
        'cargo',
        'avatar'
    ];

    protected $hidden = [
        'senha',
        'remember_token',
    ];

    protected $casts = [
        'data_cadastro' => 'datetime',
        'last_login' => 'datetime',
        'email_verified_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'locked_until' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'require_password_change' => 'boolean',
        'sync_data' => 'datetime',
    ];

    /**
     * Override para usar o campo 'senha' ao invés de 'password'
     * Necessário porque sua tabela usa 'senha'
     */
    public function getAuthPassword()
    {
        return $this->senha;
    }

    /**
     * RELACIONAMENTOS
     */

    /**
     * Marcas que este usuário é proprietário
     * Um usuário pode ter várias marcas
     */
    public function marcasProprietario(): HasMany
    {
        return $this->hasMany(Marca::class, 'pessoa_fisica_id');
    }

    /**
     * Alias para marcasProprietario() para compatibilidade
     */
    public function marcas(): HasMany
    {
        return $this->marcasProprietario();
    }

    /**
     * Empresas que este usuário é proprietário direto
     * Um usuário pode ser proprietário de várias empresas
     */
    public function empresasProprietario(): HasMany
    {
        return $this->hasMany(Empresa::class, 'proprietario_id');
    }

    /**
     * Empresas que este usuário está vinculado (como colaborador, gerente, etc)
     * Relacionamento many-to-many através da tabela empresa_user_vinculos
     */
    public function empresasVinculadas(): BelongsToMany
    {
        return $this->belongsToMany(Empresa::class, 'empresa_user_vinculos', 'user_id', 'empresa_id')
            ->withPivot(['perfil', 'status', 'permissoes', 'data_vinculo'])
            ->withTimestamps();
    }

    /**
     * Apenas empresas vinculadas ativas
     */
    public function empresasVinculadasAtivas(): BelongsToMany
    {
        return $this->empresasVinculadas()->wherePivot('status', 'ativo');
    }

    /**
     * SCOPES (filtros de query)
     */

    public function scopeAtivos($query)
    {
        return $query->where('status', 'ativo');
    }

    public function scopeByEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * MÉTODOS AUXILIARES
     */

    /**
     * Retorna todas as marcas ativas do usuário
     */
    public function getMarcasAtivasAttribute()
    {
        return $this->marcasProprietario()->where('status', 'ativa')->get();
    }

    /**
     * Retorna todas as empresas do usuário (proprietário + vinculadas)
     */
    public function getTodasEmpresasAttribute()
    {
        $empresasProprietario = $this->empresasProprietario;
        $empresasVinculadas = $this->empresasVinculadasAtivas;

        return $empresasProprietario->merge($empresasVinculadas)->unique('id');
    }

    /**
     * Verifica se o usuário tem permissão em uma empresa específica
     * @param int $empresaId
     * @param string|null $permissao
     * @return bool
     */
    public function temPermissaoEmpresa($empresaId, $permissao = null): bool
    {
        // 1. Verifica se é proprietário da empresa
        if ($this->empresasProprietario()->where('id', $empresaId)->exists()) {
            return true; // Proprietário tem todas as permissões
        }

        // 2. Verifica vínculo na tabela de relacionamento
        $vinculo = $this->empresasVinculadas()->where('empresas.id', $empresaId)->first();

        if (!$vinculo) {
            return false; // Não tem vínculo com a empresa
        }

        // 3. Se é proprietário via vínculo, tem todas as permissões
        if ($vinculo->pivot->perfil === 'proprietario') {
            return true;
        }

        // 4. Se não especificou permissão específica, apenas verifica se tem vínculo ativo
        if (!$permissao) {
            return $vinculo->pivot->status === 'ativo';
        }

        // 5. Verifica permissão específica no JSON de permissões
        $permissoes = json_decode($vinculo->pivot->permissoes, true) ?: [];
        return in_array($permissao, $permissoes);
    }

    /**
     * Retorna estatísticas para o dashboard
     */
    public function getEstatisticasDashboard(): array
    {
        $marcas = $this->marcasProprietario()->count();
        $empresas = $this->todas_empresas->count();
        $empresasAtivas = $this->todas_empresas->where('status', 'ativa')->count();

        // Aqui você pode adicionar mais estatísticas como:
        // - Vendas totais
        // - Pedidos pendentes
        // - Usuários vinculados, etc.

        return [
            'total_marcas' => $marcas,
            'total_empresas' => $empresas,
            'empresas_ativas' => $empresasAtivas,
            'usuarios_vinculados' => 0, // Implementar posteriormente
        ];
    }
}
