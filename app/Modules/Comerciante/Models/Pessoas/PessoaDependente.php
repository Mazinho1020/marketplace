<?php

namespace App\Modules\Comerciante\Models\Pessoas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PessoaDependente extends Model
{
    protected $table = 'pessoas_dependentes';

    protected $fillable = [
        'pessoa_id',
        'empresa_id',
        'nome',
        'cpf',
        'data_nascimento',
        'parentesco',
        'parentesco_outros',
        'genero',
        'dependente_ir',
        'dependente_salario_familia',
        'dependente_plano_saude',
        'possui_deficiencia',
        'tipo_deficiencia',
        'certidao_nascimento',
        'cartao_sus',
        'telefone',
        'email',
        'data_inicio_dependencia',
        'data_fim_dependencia',
        'motivo_fim_dependencia',
        'ativo',
        'observacoes'
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'dependente_ir' => 'boolean',
        'dependente_salario_familia' => 'boolean',
        'dependente_plano_saude' => 'boolean',
        'possui_deficiencia' => 'boolean',
        'data_inicio_dependencia' => 'date',
        'data_fim_dependencia' => 'date',
        'ativo' => 'boolean'
    ];

    /**
     * Relacionamentos
     */
    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_id');
    }

    /**
     * Scopes
     */
    public function scopeEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeDependenteIr($query)
    {
        return $query->where('dependente_ir', true);
    }

    public function scopeDependenteSalarioFamilia($query)
    {
        return $query->where('dependente_salario_familia', true);
    }

    public function scopeDependentePlanoSaude($query)
    {
        return $query->where('dependente_plano_saude', true);
    }

    public function scopeParentesco($query, $parentesco)
    {
        return $query->where('parentesco', $parentesco);
    }

    public function scopeComDeficiencia($query)
    {
        return $query->where('possui_deficiencia', true);
    }

    /**
     * Métodos de negócio
     */
    public function getParentescoDescricao()
    {
        $parentescos = [
            'conjuge' => 'Cônjuge',
            'companheiro' => 'Companheiro(a)',
            'filho' => 'Filho',
            'filha' => 'Filha',
            'pai' => 'Pai',
            'mae' => 'Mãe',
            'irmao' => 'Irmão',
            'irma' => 'Irmã',
            'neto' => 'Neto',
            'neta' => 'Neta',
            'outros' => $this->parentesco_outros ?: 'Outros'
        ];

        return $parentescos[$this->parentesco] ?? $this->parentesco;
    }

    public function getCpfFormatado()
    {
        $cpf = preg_replace('/\D/', '', $this->cpf ?? '');

        if (strlen($cpf) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
        }

        return $this->cpf;
    }

    public function calcularIdade()
    {
        if (!$this->data_nascimento) {
            return null;
        }

        return $this->data_nascimento->diffInYears(now());
    }

    public function isAtivo()
    {
        return $this->ativo &&
            ($this->data_fim_dependencia === null || $this->data_fim_dependencia >= now());
    }

    public function isDependenteIr()
    {
        return $this->dependente_ir && $this->isAtivo();
    }

    public function isDependenteSalarioFamilia()
    {
        return $this->dependente_salario_familia && $this->isAtivo();
    }

    public function isDependentePlanoSaude()
    {
        return $this->dependente_plano_saude && $this->isAtivo();
    }

    public function temDeficiencia()
    {
        return $this->possui_deficiencia;
    }

    public function isElegivelSalarioFamilia()
    {
        // Regra: até 14 anos ou deficiente sem limite de idade
        $idade = $this->calcularIdade();

        return $this->isDependenteSalarioFamilia() &&
            ($idade <= 14 || $this->temDeficiencia());
    }

    public function isElegivelIr()
    {
        // Regras do IR para dependentes
        $idade = $this->calcularIdade();

        if (!$this->isDependenteIr()) {
            return false;
        }

        // Cônjuge: sempre elegível
        if (in_array($this->parentesco, ['conjuge', 'companheiro'])) {
            return true;
        }

        // Filhos: até 21 anos ou 24 se universitário ou deficiente sem limite
        if (in_array($this->parentesco, ['filho', 'filha'])) {
            return $idade <= 21 || $this->temDeficiencia();
        }

        // Outros parentes: conforme legislação
        return $this->temDeficiencia() || $idade <= 21;
    }

    public function getDependenciasAtivas()
    {
        $dependencias = [];

        if ($this->isDependenteIr()) {
            $dependencias[] = 'Imposto de Renda';
        }

        if ($this->isDependenteSalarioFamilia()) {
            $dependencias[] = 'Salário Família';
        }

        if ($this->isDependentePlanoSaude()) {
            $dependencias[] = 'Plano de Saúde';
        }

        return $dependencias;
    }

    public function finalizarDependencia($motivo = null)
    {
        $this->update([
            'data_fim_dependencia' => now()->toDateString(),
            'motivo_fim_dependencia' => $motivo,
            'ativo' => false
        ]);
    }
}
