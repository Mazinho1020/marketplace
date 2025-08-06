<?php

namespace App\Comerciantes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * Model para a tabela empresas
 * Representa uma unidade/loja específica de uma marca
 * Exemplo: "Pizzaria Tradição Concórdia" é uma empresa da marca "Pizzaria Tradição"
 */
class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresas';

    protected $fillable = [
        'marca_id',
        'razao_social',
        'nome_fantasia',
        'cnpj',
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
        'status'
    ];

    protected $casts = [
        'configuracoes' => 'array',
        'horario_funcionamento' => 'array',
    ];

    /**
     * Boot do model - eventos automáticos
     */
    protected static function boot()
    {
        parent::boot();

        // Gera automaticamente o slug baseado no nome
        static::creating(function ($empresa) {
            if (empty($empresa->slug)) {
                $empresa->slug = Str::slug($empresa->nome_fantasia ?: $empresa->razao_social);
            }
        });
    }

    /**
     * RELACIONAMENTOS
     */

    /**
     * Marca à qual esta empresa pertence (se houver)
     */
    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class);
    }

    /**
     * Usuários vinculados a esta empresa (colaboradores, gerentes, etc)
     */
    public function usuariosVinculados(): BelongsToMany
    {
        return $this->belongsToMany(EmpresaUsuario::class, 'empresa_user_vinculos', 'empresa_id', 'user_id')
            ->withPivot(['perfil', 'status', 'permissoes', 'data_vinculo'])
            ->withTimestamps();
    }

    /**
     * Apenas usuários vinculados ativos
     */
    public function usuariosAtivos(): BelongsToMany
    {
        return $this->usuariosVinculados()->wherePivot('status', 'ativo');
    }

    /**
     * Proprietário da empresa (usuário com perfil proprietario)
     */
    public function proprietario(): BelongsToMany
    {
        return $this->usuariosVinculados()->wherePivot('perfil', 'proprietario');
    }

    /**
     * SCOPES (filtros de query)
     */

    public function scopeAtivas($query)
    {
        return $query->where('status', 'ativo');
    }

    /*
    public function scopeByMarca($query, $marcaId)
    {
        return $query->where('marca_id', $marcaId);
    }
    */

    /**
     * MÉTODOS AUXILIARES
     */

    /**
     * Retorna o endereço completo formatado
     */
    public function getEnderecoCompletoAttribute(): string
    {
        $endereco = [];

        if ($this->endereco_logradouro) {
            $endereco[] = $this->endereco_logradouro;
        }

        if ($this->endereco_numero) {
            $endereco[] = $this->endereco_numero;
        }

        if ($this->endereco_bairro) {
            $endereco[] = $this->endereco_bairro;
        }

        if ($this->endereco_cidade && $this->endereco_estado) {
            $endereco[] = $this->endereco_cidade . '/' . $this->endereco_estado;
        }

        return implode(', ', $endereco);
    }

    /**
     * Retorna todos os usuários da empresa (proprietário + vinculados)
     */
    public function getTodosUsuariosAttribute()
    {
        $proprietario = collect([$this->proprietario]);
        $vinculados = $this->usuariosAtivos;

        return $proprietario->merge($vinculados)->unique('id')->filter();
    }

    /**
     * Verifica se está funcionando agora (baseado no horário de funcionamento)
     */
    public function getEstaFuncionandoAttribute(): bool
    {
        if (!$this->horario_funcionamento) {
            return true; // Se não definiu horário, assume que está sempre funcionando
        }

        $agora = now();
        $diaSemana = strtolower($agora->format('l')); // monday, tuesday, etc

        // Mapeia dias em inglês para português
        $diasMap = [
            'monday' => 'segunda',
            'tuesday' => 'terca',
            'wednesday' => 'quarta',
            'thursday' => 'quinta',
            'friday' => 'sexta',
            'saturday' => 'sabado',
            'sunday' => 'domingo'
        ];

        $dia = $diasMap[$diaSemana] ?? $diaSemana;

        if (!isset($this->horario_funcionamento[$dia])) {
            return false; // Não funciona neste dia
        }

        $horario = $this->horario_funcionamento[$dia];

        // Se está marcado como fechado
        if ($horario === 'fechado' || !$horario) {
            return false;
        }

        // Verifica se tem horário definido (formato: "08:00-18:00")
        if (is_string($horario) && str_contains($horario, '-')) {
            [$abertura, $fechamento] = explode('-', $horario);
            $horaAtual = $agora->format('H:i');

            return $horaAtual >= $abertura && $horaAtual <= $fechamento;
        }

        return true;
    }
}
