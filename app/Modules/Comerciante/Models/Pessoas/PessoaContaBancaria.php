<?php

namespace App\Modules\Comerciante\Models\Pessoas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PessoaContaBancaria extends Model
{
    protected $table = 'pessoas_contas_bancarias';

    protected $fillable = [
        'pessoa_id',
        'empresa_id',
        'nome_identificacao',
        'principal',
        'banco',
        'codigo_banco',
        'agencia',
        'agencia_dv',
        'conta',
        'conta_dv',
        'tipo_conta',
        'operacao',
        'titular',
        'cpf_cnpj_titular',
        'chave_pix',
        'tipo_chave_pix',
        'ativo',
        'observacoes'
    ];

    protected $casts = [
        'principal' => 'boolean',
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

    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePrincipal($query)
    {
        return $query->where('principal', true);
    }

    public function scopeBanco($query, $banco)
    {
        return $query->where('banco', 'LIKE', "%{$banco}%");
    }

    public function scopeTipoConta($query, $tipo)
    {
        return $query->where('tipo_conta', $tipo);
    }

    /**
     * Métodos de negócio
     */
    public function getDadosBancarios()
    {
        $dados = $this->banco;

        if ($this->codigo_banco) {
            $dados .= " ({$this->codigo_banco})";
        }

        $dados .= " - Ag: {$this->agencia}";

        if ($this->agencia_dv) {
            $dados .= "-{$this->agencia_dv}";
        }

        $dados .= " - CC: {$this->conta}";

        if ($this->conta_dv) {
            $dados .= "-{$this->conta_dv}";
        }

        if ($this->operacao) {
            $dados .= " - Op: {$this->operacao}";
        }

        return $dados;
    }

    public function getCpfCnpjTitularFormatado()
    {
        $cpfCnpj = preg_replace('/\D/', '', $this->cpf_cnpj_titular ?? '');

        if (strlen($cpfCnpj) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpfCnpj);
        } elseif (strlen($cpfCnpj) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cpfCnpj);
        }

        return $this->cpf_cnpj_titular;
    }

    public function isPrincipal()
    {
        return $this->principal;
    }

    public function isAtiva()
    {
        return $this->ativo;
    }

    public function temPix()
    {
        return !empty($this->chave_pix);
    }

    public function getChavePixFormatada()
    {
        if (!$this->chave_pix) {
            return null;
        }

        switch ($this->tipo_chave_pix) {
            case 'cpf':
            case 'cnpj':
                return $this->getCpfCnpjFormatado($this->chave_pix);
            case 'telefone':
                return $this->getTelefoneFormatado($this->chave_pix);
            default:
                return $this->chave_pix;
        }
    }

    private function getCpfCnpjFormatado($valor)
    {
        $numero = preg_replace('/\D/', '', $valor);

        if (strlen($numero) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $numero);
        } elseif (strlen($numero) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $numero);
        }

        return $valor;
    }

    private function getTelefoneFormatado($valor)
    {
        $numero = preg_replace('/\D/', '', $valor);

        if (strlen($numero) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $numero);
        } elseif (strlen($numero) === 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $numero);
        }

        return $valor;
    }

    public function getTipoContaDescricao()
    {
        $tipos = [
            'corrente' => 'Conta Corrente',
            'poupanca' => 'Poupança',
            'salario' => 'Conta Salário',
            'investimento' => 'Conta Investimento'
        ];

        return $tipos[$this->tipo_conta] ?? $this->tipo_conta;
    }
}
