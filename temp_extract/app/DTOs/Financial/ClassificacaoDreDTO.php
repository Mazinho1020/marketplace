<?php

namespace App\DTOs\Financial;

class ClassificacaoDreDTO
{
    public function __construct(
        public readonly string $nome,
        public readonly ?string $descricao = null,
        public readonly ?string $codigo = null,
        public readonly ?int $classificacaoPaiId = null,
        public readonly int $nivel = 1,
        public readonly ?int $tipoId = null,
        public readonly bool $ativo = true,
        public readonly int $ordemExibicao = 0,
        public readonly int $empresaId = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            nome: $data['nome'],
            descricao: $data['descricao'] ?? null,
            codigo: $data['codigo'] ?? null,
            classificacaoPaiId: $data['classificacao_pai_id'] ?? null,
            nivel: $data['nivel'] ?? 1,
            tipoId: $data['tipo_id'] ?? null,
            ativo: $data['ativo'] ?? true,
            ordemExibicao: $data['ordem_exibicao'] ?? 0,
            empresaId: $data['empresa_id'] ?? auth()->user()->empresa_id ?? 0,
        );
    }

    public function toArray(): array
    {
        return [
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'codigo' => $this->codigo,
            'classificacao_pai_id' => $this->classificacaoPaiId,
            'nivel' => $this->nivel,
            'tipo_id' => $this->tipoId,
            'ativo' => $this->ativo,
            'ordem_exibicao' => $this->ordemExibicao,
            'empresa_id' => $this->empresaId,
        ];
    }
}