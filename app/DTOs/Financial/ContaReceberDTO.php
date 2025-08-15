<?php

namespace App\DTOs\Financial;

use App\Enums\FrequenciaRecorrenciaEnum;
use Illuminate\Support\Carbon;

class ContaReceberDTO
{
    public function __construct(
        public readonly int $empresa_id,
        public readonly string $descricao,
        public readonly float $valor_total,
        public readonly Carbon|string $data_vencimento,
        public readonly Carbon|string|null $data_competencia = null,
        public readonly ?int $cliente_id = null,
        public readonly ?int $funcionario_id = null,
        public readonly ?int $conta_gerencial_id = null,
        public readonly ?int $categoria_id = null,
        public readonly ?string $observacoes = null,
        public readonly ?string $codigo_lancamento = null,
        public readonly ?string $documento_referencia = null,
        public readonly ?bool $cobranca_automatica = false,
        public readonly ?string $juros_multa_config = null,
        public readonly ?int $numero_parcelas = 1,
        public readonly ?int $parcela_atual = 1,
        public readonly ?float $valor_parcela = null,
        public readonly ?FrequenciaRecorrenciaEnum $frequencia_recorrencia = null,
    ) {}

    /**
     * Criar DTO a partir de array de dados
     */
    public static function fromArray(array $data): self
    {
        return new self(
            empresa_id: $data['empresa_id'],
            descricao: $data['descricao'],
            valor_total: (float) $data['valor_total'],
            data_vencimento: $data['data_vencimento'] instanceof Carbon 
                ? $data['data_vencimento'] 
                : Carbon::parse($data['data_vencimento']),
            data_competencia: isset($data['data_competencia']) 
                ? ($data['data_competencia'] instanceof Carbon 
                    ? $data['data_competencia'] 
                    : Carbon::parse($data['data_competencia']))
                : null,
            cliente_id: $data['cliente_id'] ?? null,
            funcionario_id: $data['funcionario_id'] ?? null,
            conta_gerencial_id: $data['conta_gerencial_id'] ?? null,
            categoria_id: $data['categoria_id'] ?? null,
            observacoes: $data['observacoes'] ?? null,
            codigo_lancamento: $data['codigo_lancamento'] ?? null,
            documento_referencia: $data['documento_referencia'] ?? null,
            cobranca_automatica: (bool) ($data['cobranca_automatica'] ?? false),
            juros_multa_config: $data['juros_multa_config'] ?? null,
            numero_parcelas: (int) ($data['numero_parcelas'] ?? 1),
            parcela_atual: (int) ($data['parcela_atual'] ?? 1),
            valor_parcela: isset($data['valor_parcela']) ? (float) $data['valor_parcela'] : null,
            frequencia_recorrencia: isset($data['frequencia_recorrencia']) 
                ? FrequenciaRecorrenciaEnum::from($data['frequencia_recorrencia'])
                : null,
        );
    }

    /**
     * Validar dados do DTO
     */
    public function validate(): array
    {
        $errors = [];

        if (empty($this->descricao)) {
            $errors['descricao'] = 'A descrição é obrigatória.';
        }

        if ($this->valor_total <= 0) {
            $errors['valor_total'] = 'O valor total deve ser maior que zero.';
        }

        if ($this->numero_parcelas && $this->numero_parcelas < 1) {
            $errors['numero_parcelas'] = 'O número de parcelas deve ser maior que zero.';
        }

        if ($this->parcela_atual && $this->numero_parcelas && $this->parcela_atual > $this->numero_parcelas) {
            $errors['parcela_atual'] = 'A parcela atual não pode ser maior que o número total de parcelas.';
        }

        if ($this->valor_parcela && $this->valor_parcela <= 0) {
            $errors['valor_parcela'] = 'O valor da parcela deve ser maior que zero.';
        }

        if ($this->numero_parcelas > 1 && !$this->frequencia_recorrencia) {
            $errors['frequencia_recorrencia'] = 'A frequência de recorrência é obrigatória para parcelamentos.';
        }

        return $errors;
    }

    /**
     * Converter para array
     */
    public function toArray(): array
    {
        return [
            'empresa_id' => $this->empresa_id,
            'descricao' => $this->descricao,
            'valor_total' => $this->valor_total,
            'data_vencimento' => $this->data_vencimento instanceof Carbon 
                ? $this->data_vencimento->toDateString()
                : $this->data_vencimento,
            'data_competencia' => $this->data_competencia instanceof Carbon 
                ? $this->data_competencia->toDateString()
                : $this->data_competencia,
            'cliente_id' => $this->cliente_id,
            'funcionario_id' => $this->funcionario_id,
            'conta_gerencial_id' => $this->conta_gerencial_id,
            'categoria_id' => $this->categoria_id,
            'observacoes' => $this->observacoes,
            'codigo_lancamento' => $this->codigo_lancamento,
            'documento_referencia' => $this->documento_referencia,
            'cobranca_automatica' => $this->cobranca_automatica,
            'juros_multa_config' => $this->juros_multa_config,
            'numero_parcelas' => $this->numero_parcelas,
            'parcela_atual' => $this->parcela_atual,
            'valor_parcela' => $this->valor_parcela,
            'frequencia_recorrencia' => $this->frequencia_recorrencia?->value,
        ];
    }

    /**
     * Obter dados para criação no banco
     */
    public function forDatabase(): array
    {
        $data = $this->toArray();
        
        // Converter datas para formato do banco
        if ($this->data_vencimento) {
            $data['data_vencimento'] = $this->data_vencimento instanceof Carbon 
                ? $this->data_vencimento->toDateString()
                : $this->data_vencimento;
        }
        
        if ($this->data_competencia) {
            $data['data_competencia'] = $this->data_competencia instanceof Carbon 
                ? $this->data_competencia->toDateString()
                : $this->data_competencia;
        }

        return array_filter($data, fn($value) => $value !== null);
    }
}
