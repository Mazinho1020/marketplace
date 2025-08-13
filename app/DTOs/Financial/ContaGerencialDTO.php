<?php

namespace App\DTOs\Financial;

use App\Enums\NaturezaContaEnum;

class ContaGerencialDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $codigo = null,
        public ?int $conta_pai_id = null,
        public int $nivel = 1,
        public string $nome = '',
        public ?string $descricao = null,
        public bool $ativo = true,
        public int $ordem_exibicao = 0,
        public bool $permite_lancamento = true,
        public ?NaturezaContaEnum $natureza = null,
        public ?array $configuracoes = null,
        public ?int $usuario_id = null,
        public ?int $empresa_id = null,
        public ?int $classificacao_dre_id = null,
        public ?int $tipo_id = null,
        public ?int $categoria_id = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            codigo: $data['codigo'] ?? null,
            conta_pai_id: $data['conta_pai_id'] ?? null,
            nivel: $data['nivel'] ?? 1,
            nome: $data['nome'] ?? '',
            descricao: $data['descricao'] ?? null,
            ativo: $data['ativo'] ?? true,
            ordem_exibicao: $data['ordem_exibicao'] ?? 0,
            permite_lancamento: $data['permite_lancamento'] ?? true,
            natureza: isset($data['natureza'])
                ? NaturezaContaEnum::from($data['natureza'])
                : null,
            configuracoes: $data['configuracoes'] ?? null,
            usuario_id: $data['usuario_id'] ?? null,
            empresa_id: $data['empresa_id'] ?? null,
            classificacao_dre_id: $data['classificacao_dre_id'] ?? null,
            tipo_id: $data['tipo_id'] ?? null,
            categoria_id: $data['categoria_id'] ?? null,
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
            'conta_pai_id' => $this->conta_pai_id,
            'nivel' => $this->nivel,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'ativo' => $this->ativo,
            'ordem_exibicao' => $this->ordem_exibicao,
            'permite_lancamento' => $this->permite_lancamento,
            'natureza' => $this->natureza?->value,
            'configuracoes' => $this->configuracoes,
            'usuario_id' => $this->usuario_id,
            'empresa_id' => $this->empresa_id,
            'classificacao_dre_id' => $this->classificacao_dre_id,
            'tipo_id' => $this->tipo_id,
            'categoria_id' => $this->categoria_id,
        ];
    }

    public function toArrayWithoutNulls(): array
    {
        return array_filter($this->toArray(), fn($value) => !is_null($value));
    }
}
