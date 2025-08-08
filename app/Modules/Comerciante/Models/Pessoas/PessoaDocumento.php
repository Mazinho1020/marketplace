<?php

namespace App\Modules\Comerciante\Models\Pessoas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class PessoaDocumento extends Model
{
    protected $table = 'pessoas_documentos';

    protected $fillable = [
        'pessoa_id',
        'empresa_id',
        'tipo',
        'nome_documento',
        'numero',
        'orgao_emissor',
        'uf_emissao',
        'data_emissao',
        'data_validade',
        'arquivo_nome',
        'arquivo_url',
        'arquivo_tamanho',
        'arquivo_tipo',
        'verificado',
        'data_verificacao',
        'verificado_por',
        'observacoes',
        'ativo'
    ];

    protected $casts = [
        'data_emissao' => 'date',
        'data_validade' => 'date',
        'verificado' => 'boolean',
        'data_verificacao' => 'datetime',
        'ativo' => 'boolean',
        'arquivo_tamanho' => 'integer'
    ];

    /**
     * Relacionamentos
     */
    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_id');
    }

    public function verificador(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'verificado_por');
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

    public function scopeVerificados($query)
    {
        return $query->where('verificado', true);
    }

    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeVencidos($query)
    {
        return $query->where('data_validade', '<', now());
    }

    public function scopeVencendoEm($query, $dias = 30)
    {
        return $query->whereBetween('data_validade', [now(), now()->addDays($dias)]);
    }

    /**
     * Métodos de negócio
     */
    public function getTipoDescricao()
    {
        $tipos = [
            'cpf' => 'CPF',
            'rg' => 'RG',
            'cnh' => 'CNH',
            'ctps' => 'CTPS',
            'titulo_eleitor' => 'Título de Eleitor',
            'pis_pasep' => 'PIS/PASEP',
            'certificado_reservista' => 'Certificado de Reservista',
            'certidao_nascimento' => 'Certidão de Nascimento',
            'certidao_casamento' => 'Certidão de Casamento',
            'certidao_obito' => 'Certidão de Óbito',
            'comprovante_residencia' => 'Comprovante de Residência',
            'comprovante_renda' => 'Comprovante de Renda',
            'contrato_social' => 'Contrato Social',
            'alvara_funcionamento' => 'Alvará de Funcionamento',
            'outros' => 'Outros'
        ];

        return $tipos[$this->tipo] ?? ($this->nome_documento ?: $this->tipo);
    }

    public function isVerificado()
    {
        return $this->verificado;
    }

    public function isVencido()
    {
        return $this->data_validade && $this->data_validade < now();
    }

    public function isVencendoEm($dias = 30)
    {
        return $this->data_validade &&
            $this->data_validade >= now() &&
            $this->data_validade <= now()->addDays($dias);
    }

    public function temArquivo()
    {
        return !empty($this->arquivo_url);
    }

    public function getArquivoTamanhoFormatado()
    {
        if (!$this->arquivo_tamanho) {
            return null;
        }

        $bytes = $this->arquivo_tamanho;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getStatusDescricao()
    {
        if (!$this->ativo) {
            return 'Inativo';
        }

        if ($this->isVencido()) {
            return 'Vencido';
        }

        if ($this->isVencendoEm(30)) {
            return 'Vencendo em breve';
        }

        if ($this->isVerificado()) {
            return 'Verificado';
        }

        return 'Pendente verificação';
    }

    public function getStatusClass()
    {
        if (!$this->ativo) {
            return 'secondary';
        }

        if ($this->isVencido()) {
            return 'danger';
        }

        if ($this->isVencendoEm(30)) {
            return 'warning';
        }

        if ($this->isVerificado()) {
            return 'success';
        }

        return 'info';
    }

    public function diasParaVencimento()
    {
        if (!$this->data_validade) {
            return null;
        }

        return now()->diffInDays($this->data_validade, false);
    }

    public function marcarComoVerificado($verificadoPor = null)
    {
        $this->update([
            'verificado' => true,
            'data_verificacao' => now(),
            'verificado_por' => $verificadoPor ?? Auth::id()
        ]);
    }
}
