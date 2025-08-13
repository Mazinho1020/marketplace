<?php

namespace App\DTOs\Financial;

use App\Enums\NaturezaContaEnum;

class ContaGerencialDTO
{
    public function __construct(
        public readonly string $nome,
        public readonly ?string $descricao = null,
        public readonly ?string $codigo = null,
        public readonly ?int $contaPaiId = null,
        public readonly int $nivel = 1,
        public readonly bool $ativo = true,
        public readonly int $ordemExibicao = 0,
        public readonly bool $permiteLancamento = true,
        public readonly ?NaturezaContaEnum $naturezaConta = null,
        public readonly ?array $configuracoes = null,
        public readonly ?int $classificacaoDreId = null,
        public readonly ?int $tipoId = null,
        public readonly ?array $naturezas = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            nome: $data['nome'],
            descricao: $data['descricao'] ?? null,
            codigo: $data['codigo'] ?? null,
            contaPaiId: $data['conta_pai_id'] ?? null,
            nivel: $data['nivel'] ?? 1,
            ativo: $data['ativo'] ?? true,
            ordemExibicao: $data['ordem_exibicao'] ?? 0,
            permiteLancamento: $data['permite_lancamento'] ?? true,
            naturezaConta: isset($data['natureza_conta']) ? NaturezaContaEnum::from($data['natureza_conta']) : null,
            configuracoes: $data['configuracoes'] ?? null,
            classificacaoDreId: $data['classificacao_dre_id'] ?? null,
            tipoId: $data['tipo_id'] ?? null,
            naturezas: $data['naturezas'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'codigo' => $this->codigo,
            'conta_pai_id' => $this->contaPaiId,
            'nivel' => $this->nivel,
            'ativo' => $this->ativo,
            'ordem_exibicao' => $this->ordemExibicao,
            'permite_lancamento' => $this->permiteLancamento,
            'natureza_conta' => $this->naturezaConta?->value,
            'configuracoes' => $this->configuracoes,
            'classificacao_dre_id' => $this->classificacaoDreId,
            'tipo_id' => $this->tipoId,
        ];
    }
}