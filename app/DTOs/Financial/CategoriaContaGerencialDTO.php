<?php

namespace App\DTOs\Financial;

class CategoriaContaGerencialDTO
{
    public function __construct(
        public ?int $id = null,
        public string $nome = '',
        public string $nome_completo = '',
        public ?string $descricao = null,
        public string $cor = '#007bff',
        public ?string $icone = null,
        public bool $e_custo = false,
        public bool $e_despesa = false,
        public bool $e_receita = false,
        public bool $ativo = true,
        public ?int $empresa_id = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            nome: $data['nome'] ?? '',
            nome_completo: $data['nome_completo'] ?? '',
            descricao: $data['descricao'] ?? null,
            cor: $data['cor'] ?? '#007bff',
            icone: $data['icone'] ?? null,
            e_custo: $data['e_custo'] ?? false,
            e_despesa: $data['e_despesa'] ?? false,
            e_receita: $data['e_receita'] ?? false,
            ativo: $data['ativo'] ?? true,
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
            'nome' => $this->nome,
            'nome_completo' => $this->nome_completo,
            'descricao' => $this->descricao,
            'cor' => $this->cor,
            'icone' => $this->icone,
            'e_custo' => $this->e_custo,
            'e_despesa' => $this->e_despesa,
            'e_receita' => $this->e_receita,
            'ativo' => $this->ativo,
            'empresa_id' => $this->empresa_id,
        ];
    }

    public function toArrayWithoutNulls(): array
    {
        return array_filter($this->toArray(), fn($value) => !is_null($value));
    }
}
