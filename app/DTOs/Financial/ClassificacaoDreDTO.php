<?php

namespace App\DTOs\Financial;

class ClassificacaoDreDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $codigo = null,
        public int $nivel = 1,
        public ?int $classificacao_pai_id = null,
        public string $nome = '',
        public ?string $descricao = null,
        public ?int $tipo_id = null,
        public bool $ativo = true,
        public int $ordem_exibicao = 0,
        public ?int $empresa_id = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            codigo: $data['codigo'] ?? null,
            nivel: $data['nivel'] ?? 1,
            classificacao_pai_id: $data['classificacao_pai_id'] ?? null,
            nome: $data['nome'] ?? '',
            descricao: $data['descricao'] ?? null,
            tipo_id: $data['tipo_id'] ?? null,
            ativo: $data['ativo'] ?? true,
            ordem_exibicao: $data['ordem_exibicao'] ?? 0,
            empresa_id: $data['empresa_id'] ?? null,
        );
    }

    public static function fromRequest($request): self
    {
        return self::fromArray($request->validated());
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nivel' => $this->nivel,
            'classificacao_pai_id' => $this->classificacao_pai_id,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'tipo_id' => $this->tipo_id,
            'ativo' => $this->ativo,
            'ordem_exibicao' => $this->ordem_exibicao,
            'empresa_id' => $this->empresa_id,
        ];
    }

    public function toArrayWithoutNulls(): array
    {
        return array_filter($this->toArray(), fn($value) => !is_null($value));
    }
}
