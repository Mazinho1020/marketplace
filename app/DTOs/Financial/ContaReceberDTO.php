<?php

namespace App\DTOs\Financial;

class ContaReceberDTO
{
    public function __construct(
        public readonly int $empresaId,
        public readonly int $pessoaId,
        public readonly string $pessoaTipo,
        public readonly int $contaGerencialId,
        public readonly string $descricao,
        public readonly float $valor,
        public readonly string $dataVencimento,
        public readonly ?string $numeroDocumento = null,
        public readonly ?string $observacoes = null,
        public readonly ?string $dataEmissao = null,
        public readonly ?string $dataCompetencia = null,
        public readonly ?array $configAlertas = null,
        public readonly ?array $jurosMultaConfig = null,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            empresaId: $data['empresa_id'],
            pessoaId: $data['pessoa_id'],
            pessoaTipo: $data['pessoa_tipo'],
            contaGerencialId: $data['conta_gerencial_id'],
            descricao: $data['descricao'],
            valor: $data['valor'],
            dataVencimento: $data['data_vencimento'],
            numeroDocumento: $data['numero_documento'] ?? null,
            observacoes: $data['observacoes'] ?? null,
            dataEmissao: $data['data_emissao'] ?? null,
            dataCompetencia: $data['data_competencia'] ?? null,
            configAlertas: $data['config_alertas'] ?? null,
            jurosMultaConfig: $data['juros_multa_config'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'empresa_id' => $this->empresaId,
            'pessoa_id' => $this->pessoaId,
            'pessoa_tipo' => $this->pessoaTipo,
            'conta_gerencial_id' => $this->contaGerencialId,
            'descricao' => $this->descricao,
            'valor' => $this->valor,
            'data_vencimento' => $this->dataVencimento,
            'numero_documento' => $this->numeroDocumento,
            'observacoes' => $this->observacoes,
            'data_emissao' => $this->dataEmissao,
            'data_competencia' => $this->dataCompetencia,
            'config_alertas' => $this->configAlertas,
            'juros_multa_config' => $this->jurosMultaConfig,
        ];
    }
}