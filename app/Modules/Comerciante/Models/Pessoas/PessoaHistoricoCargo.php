<?php

namespace App\Modules\Comerciante\Models\Pessoas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class PessoaHistoricoCargo extends Model
{
    protected $table = 'pessoas_historico_cargos';

    protected $fillable = [
        'pessoa_id',
        'empresa_id',
        'departamento_id',
        'cargo_id',
        'superior_id',
        'salario_anterior',
        'salario_novo',
        'data_inicio',
        'data_fim',
        'motivo_alteracao',
        'motivo_detalhes',
        'aprovado_por',
        'data_aprovacao',
        'observacoes',
        'tipo_contratacao',
        'numero_registro'
    ];

    protected $casts = [
        'salario_anterior' => 'decimal:2',
        'salario_novo' => 'decimal:2',
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'data_aprovacao' => 'datetime'
    ];

    /**
     * Relacionamentos
     */
    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_id');
    }

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

    public function aprovador(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'aprovado_por');
    }

    /**
     * Scopes
     */
    public function scopeEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopePessoa($query, $pessoaId)
    {
        return $query->where('pessoa_id', $pessoaId);
    }

    public function scopeAtivos($query)
    {
        return $query->whereNull('data_fim')->orWhere('data_fim', '>=', now());
    }

    public function scopeFinalizados($query)
    {
        return $query->whereNotNull('data_fim')->where('data_fim', '<', now());
    }

    public function scopeMotivoAlteracao($query, $motivo)
    {
        return $query->where('motivo_alteracao', $motivo);
    }

    public function scopeAprovados($query)
    {
        return $query->whereNotNull('aprovado_por');
    }

    public function scopePendentesAprovacao($query)
    {
        return $query->whereNull('aprovado_por');
    }

    /**
     * Métodos de negócio
     */
    public function getMotivoAlteracaoDescricao()
    {
        $motivos = [
            'admissao' => 'Admissão',
            'promocao' => 'Promoção',
            'transferencia' => 'Transferência',
            'ajuste_salarial' => 'Ajuste Salarial',
            'rebaixamento' => 'Rebaixamento',
            'demissao' => 'Demissão',
            'outros' => 'Outros'
        ];

        return $motivos[$this->motivo_alteracao] ?? $this->motivo_alteracao;
    }

    public function getTipoContratacaoDescricao()
    {
        $tipos = [
            'CLT' => 'CLT',
            'PJ' => 'Pessoa Jurídica',
            'Diarista' => 'Diarista',
            'Terceirizado' => 'Terceirizado',
            'Estagiario' => 'Estagiário',
            'Entregador' => 'Entregador',
            'Freelancer' => 'Freelancer'
        ];

        return $tipos[$this->tipo_contratacao] ?? $this->tipo_contratacao;
    }

    public function calcularPercentualAumento()
    {
        if (!$this->salario_anterior || $this->salario_anterior == 0) {
            return null;
        }

        return (($this->salario_novo - $this->salario_anterior) / $this->salario_anterior) * 100;
    }

    public function getPercentualAumentoFormatado()
    {
        $percentual = $this->calcularPercentualAumento();

        if ($percentual === null) {
            return 'N/A';
        }

        $sinal = $percentual >= 0 ? '+' : '';
        return $sinal . number_format($percentual, 2, ',', '.') . '%';
    }

    public function calcularDiasPeriodo()
    {
        if (!$this->data_inicio) {
            return 0;
        }

        $dataFim = $this->data_fim ?: now();
        return $this->data_inicio->diffInDays($dataFim) + 1;
    }

    public function isAtivo()
    {
        return $this->data_fim === null || $this->data_fim >= now();
    }

    public function isAprovado()
    {
        return !empty($this->aprovado_por);
    }

    public function isPendente()
    {
        return !$this->isAprovado();
    }

    public function isPromocao()
    {
        return $this->motivo_alteracao === 'promocao';
    }

    public function isTransferencia()
    {
        return $this->motivo_alteracao === 'transferencia';
    }

    public function isAjusteSalarial()
    {
        return $this->motivo_alteracao === 'ajuste_salarial';
    }

    public function isDemissao()
    {
        return $this->motivo_alteracao === 'demissao';
    }

    public function isAdmissao()
    {
        return $this->motivo_alteracao === 'admissao';
    }

    public function getValorAumento()
    {
        if (!$this->salario_anterior) {
            return $this->salario_novo;
        }

        return $this->salario_novo - $this->salario_anterior;
    }

    public function getValorAumentoFormatado()
    {
        $valor = $this->getValorAumento();
        $sinal = $valor >= 0 ? '+' : '';

        return $sinal . 'R$ ' . number_format(abs($valor), 2, ',', '.');
    }

    public function aprovar($aprovadoPor = null, $observacoes = null)
    {
        $this->update([
            'aprovado_por' => $aprovadoPor ?? Auth::id(),
            'data_aprovacao' => now(),
            'observacoes' => $observacoes ?: $this->observacoes
        ]);
    }

    public function finalizar($dataFim = null, $motivo = null)
    {
        $this->update([
            'data_fim' => $dataFim ?: now()->toDateString(),
            'motivo_detalhes' => $motivo ?: $this->motivo_detalhes
        ]);
    }

    public function getResumoAlteracao()
    {
        return [
            'funcionario' => $this->pessoa->nome_completo,
            'departamento_anterior' => null, // Buscar do histórico anterior
            'departamento_novo' => $this->departamento->nome,
            'cargo_anterior' => null, // Buscar do histórico anterior
            'cargo_novo' => $this->cargo->nome,
            'salario_anterior' => $this->salario_anterior,
            'salario_novo' => $this->salario_novo,
            'percentual_aumento' => $this->getPercentualAumentoFormatado(),
            'valor_aumento' => $this->getValorAumentoFormatado(),
            'motivo' => $this->getMotivoAlteracaoDescricao(),
            'data_inicio' => $this->data_inicio->format('d/m/Y'),
            'aprovado' => $this->isAprovado(),
            'aprovador' => $this->aprovador->nome_completo ?? null
        ];
    }
}
