<?php

namespace App\Modules\Comerciante\Models\Pessoas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Comerciante\Traits\HasConfiguracoes;

class Pessoa extends Model
{
    use SoftDeletes, HasConfiguracoes;

    protected $table = 'pessoas';

    protected $fillable = [
        'empresa_id',
        'codigo',
        'tipo',
        'nome',
        'sobrenome',
        'nome_social',
        'data_nascimento',
        'cpf_cnpj',
        'rg',
        'orgao_emissor',
        'estado_civil',
        'genero',
        'nacionalidade',
        'telefone',
        'whatsapp',
        'email',
        'email_secundario',
        'status',
        'observacoes',
        'foto_url',

        // Funcionários
        'departamento_id',
        'cargo_id',
        'superior_id',
        'data_admissao',
        'numero_registro',
        'salario_atual',
        'dia_vencimento_salario',
        'tipo_contratacao',
        'data_demissao',
        'motivo_demissao',
        'situacao_trabalhista',

        // Clientes/Fornecedores
        'pessoa_juridica',
        'inscricao_estadual',
        'inscricao_municipal',
        'nome_fantasia',
        'website',
        'limite_credito',
        'limite_fiado',
        'prazo_pagamento_padrao',
        'rating',
        'categoria_id',

        // Bancário
        'conta_bancaria_principal_id',
        'chave_pix',
        'tipo_chave_pix',

        // Afiliados
        'afiliado_codigo',
        'afiliado_nivel',
        'afiliado_taxa_comissao',
        'afiliado_total_vendas',
        'afiliado_total_comissoes',
        'afiliado_total_pago',

        // Planos
        'plano_atual_id',
        'plano_status',
        'plano_inicio',
        'plano_vencimento',
        'plano_trial_expira',
        'chave_licenca',
        'chave_api',

        'endereco_principal_id'
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'data_admissao' => 'date',
        'data_demissao' => 'date',
        'pessoa_juridica' => 'boolean',
        'limite_credito' => 'decimal:2',
        'limite_fiado' => 'decimal:2',
        'salario_atual' => 'decimal:2',
        'afiliado_taxa_comissao' => 'decimal:2',
        'afiliado_total_vendas' => 'decimal:2',
        'afiliado_total_comissoes' => 'decimal:2',
        'afiliado_total_pago' => 'decimal:2',
        'plano_inicio' => 'datetime',
        'plano_vencimento' => 'datetime',
        'plano_trial_expira' => 'datetime'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Relacionamentos
     */
    public function departamento(): BelongsTo
    {
        return $this->belongsTo(PessoaDepartamento::class, 'departamento_id');
    }

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(PessoaCargo::class, 'cargo_id');
    }

    public function superior(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'superior_id');
    }

    public function subordinados(): HasMany
    {
        return $this->hasMany(Pessoa::class, 'superior_id');
    }

    public function enderecos(): HasMany
    {
        return $this->hasMany(PessoaEndereco::class);
    }

    public function enderecoPrincipal(): BelongsTo
    {
        return $this->belongsTo(PessoaEndereco::class, 'endereco_principal_id');
    }

    public function contasBancarias(): HasMany
    {
        return $this->hasMany(PessoaContaBancaria::class);
    }

    public function contaBancariaPrincipal(): BelongsTo
    {
        return $this->belongsTo(PessoaContaBancaria::class, 'conta_bancaria_principal_id');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(PessoaDocumento::class);
    }

    public function dependentes(): HasMany
    {
        return $this->hasMany(PessoaDependente::class);
    }

    public function historicoCargos(): HasMany
    {
        return $this->hasMany(PessoaHistoricoCargo::class)->orderBy('data_inicio', 'desc');
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
        return $query->where('status', 'ativo');
    }

    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', 'LIKE', "%{$tipo}%");
    }

    public function scopeClientes($query)
    {
        return $query->tipo('cliente');
    }

    public function scopeFuncionarios($query)
    {
        return $query->tipo('funcionario');
    }

    public function scopeFornecedores($query)
    {
        return $query->tipo('fornecedor');
    }

    public function scopeEntregadores($query)
    {
        return $query->tipo('entregador');
    }

    /**
     * Mutators e Accessors
     */
    public function setTipoAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['tipo'] = implode(',', $value);
        } else {
            $this->attributes['tipo'] = $value;
        }
    }

    public function getTipoAttribute($value)
    {
        return explode(',', $value);
    }

    public function getNomeCompletoAttribute()
    {
        return trim($this->nome . ' ' . $this->sobrenome);
    }

    public function getCpfCnpjFormatadoAttribute()
    {
        $cpfCnpj = preg_replace('/\D/', '', $this->cpf_cnpj ?? '');

        if (strlen($cpfCnpj) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpfCnpj);
        } elseif (strlen($cpfCnpj) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cpfCnpj);
        }

        return $this->cpf_cnpj;
    }

    /**
     * Métodos de negócio
     */
    public function temTipo($tipo)
    {
        return in_array($tipo, $this->tipo);
    }

    public function isCliente()
    {
        return $this->temTipo('cliente');
    }

    public function isFuncionario()
    {
        return $this->temTipo('funcionario');
    }

    public function isFornecedor()
    {
        return $this->temTipo('fornecedor');
    }

    public function isEntregador()
    {
        return $this->temTipo('entregador');
    }

    public function isAtivo()
    {
        return $this->status === 'ativo';
    }

    public function podeVender()
    {
        if (!$this->isCliente() || !$this->isAtivo()) {
            return false;
        }

        // Verificar configurações de bloqueio
        $config = $this->getConfig();

        if ($config->verificarLimiteCredito() && $this->limite_credito <= 0) {
            return false;
        }

        if ($config->bloquearClienteInadimplente() && $this->isInadimplente()) {
            return false;
        }

        return true;
    }

    public function isInadimplente()
    {
        // Implementar lógica para verificar inadimplência
        return false;
    }

    public function calcularIdade()
    {
        if (!$this->data_nascimento) {
            return null;
        }

        return $this->data_nascimento->diffInYears(now());
    }

    public function tempoEmpresa()
    {
        if (!$this->data_admissao) {
            return null;
        }

        return $this->data_admissao->diffForHumans();
    }

    /**
     * Validações
     */
    public function validarDados()
    {
        $config = $this->getConfig();
        $errors = [];

        if ($config->cpfObrigatorio() && empty($this->cpf_cnpj)) {
            $errors[] = 'CPF/CNPJ é obrigatório';
        }

        if ($config->emailObrigatorio() && empty($this->email)) {
            $errors[] = 'Email é obrigatório';
        }

        if ($config->telefoneObrigatorio() && empty($this->telefone)) {
            $errors[] = 'Telefone é obrigatório';
        }

        return $errors;
    }

    /**
     * Aplicar configurações padrão
     */
    public function aplicarPadroes()
    {
        $config = $this->getConfig();

        if ($this->isCliente()) {
            $this->limite_credito = $this->limite_credito ?? $config->limiteCreditoPadrao();
            $this->limite_fiado = $this->limite_fiado ?? $config->limiteFiadoPadrao();
            $this->prazo_pagamento_padrao = $this->prazo_pagamento_padrao ?? $config->prazoPagamentoCliente();
        }
    }
}
