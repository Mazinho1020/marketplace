<?php

namespace App\Services\Financial;

use App\Models\Financial\ContaGerencial;
use App\DTOs\Financial\ContaGerencialDTO;
use App\Enums\NaturezaContaEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ContaGerencialService
{
    public function __construct(
        private ContaGerencial $model
    ) {}

    /**
     * Lista todas as contas com paginação
     */
    public function index(array $filtros = []): LengthAwarePaginator
    {
        $query = $this->model->with(['contaPai', 'categoria', 'classificacaoDre', 'tipo'])
            ->ordenadoPorHierarquia();

        // Filtro por empresa (obrigatório)
        if (!empty($filtros['empresa_id'])) {
            $query->where('empresa_id', $filtros['empresa_id']);
        }

        // Filtros
        if (!empty($filtros['nome'])) {
            $query->buscar($filtros['nome'], ['nome', 'codigo', 'descricao']);
        }

        if (!empty($filtros['ativo'])) {
            $query->where('ativo', $filtros['ativo'] === 'true');
        }

        if (!empty($filtros['natureza'])) {
            $query->where('natureza', $filtros['natureza']);
        }

        if (!empty($filtros['categoria_id'])) {
            $query->where('categoria_id', $filtros['categoria_id']);
        }

        if (!empty($filtros['permite_lancamento'])) {
            $query->where('permite_lancamento', $filtros['permite_lancamento'] === 'true');
        }

        if (!empty($filtros['conta_pai_id'])) {
            $query->where('conta_pai_id', $filtros['conta_pai_id']);
        }

        return $query->paginate(15);
    }

    /**
     * Busca conta por ID
     */
    public function find(int $id): ?ContaGerencial
    {
        return $this->model->with(['contaPai', 'filhos', 'categoria', 'classificacaoDre', 'tipo'])
            ->find($id);
    }

    /**
     * Cria nova conta
     */
    public function create(ContaGerencialDTO $dto): ContaGerencial
    {
        return DB::transaction(function () use ($dto) {
            $data = $dto->toArrayWithoutNulls();

            // Define nível automaticamente se não informado
            if (!isset($data['nivel']) && isset($data['conta_pai_id'])) {
                $contaPai = $this->model->find($data['conta_pai_id']);
                $data['nivel'] = $contaPai ? $contaPai->nivel + 1 : 1;
            }

            // Gera código automaticamente se não informado
            if (!isset($data['codigo'])) {
                $data['codigo'] = $this->gerarProximoCodigo($data['conta_pai_id'] ?? null);
            }

            $conta = $this->model->create($data);
            $conta->load(['contaPai', 'categoria', 'classificacaoDre', 'tipo']);

            return $conta;
        });
    }

    /**
     * Atualiza conta existente
     */
    public function update(int $id, ContaGerencialDTO $dto): ContaGerencial
    {
        return DB::transaction(function () use ($id, $dto) {
            $conta = $this->find($id);

            if (!$conta) {
                throw new \Exception('Conta gerencial não encontrada');
            }

            $data = $dto->toArrayWithoutNulls();

            // Verifica se mudança de pai criaria loop
            if (isset($data['conta_pai_id']) && $data['conta_pai_id'] !== $conta->conta_pai_id) {
                $this->validarHierarquia($id, $data['conta_pai_id']);
            }

            $conta->update($data);
            $conta->load(['contaPai', 'categoria', 'classificacaoDre', 'tipo']);

            return $conta;
        });
    }

    /**
     * Remove conta
     */
    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $conta = $this->find($id);

            if (!$conta) {
                throw new \Exception('Conta gerencial não encontrada');
            }

            if (!$conta->podeSerExcluida()) {
                throw new \Exception('Conta não pode ser excluída pois possui filhos ou lançamentos');
            }

            return $conta->delete();
        });
    }

    /**
     * Lista contas em formato hierárquico
     */
    public function getHierarquia(bool $apenasAtivas = true, int $empresaId = null): Collection
    {
        $query = $this->model->with(['filhos.categoria', 'categoria'])
            ->raizes()
            ->ordenadoPorHierarquia();

        if ($apenasAtivas) {
            $query->ativos();
        }

        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        return $query->get();
    }

    /**
     * Lista contas que permitem lançamento
     */
    public function getContasParaLancamento(bool $apenasAtivas = true): Collection
    {
        $query = $this->model->with(['categoria'])
            ->permiteLancamento()
            ->ordenado();

        if ($apenasAtivas) {
            $query->ativos();
        }

        return $query->get();
    }

    /**
     * Busca contas por categoria
     */
    public function getPorCategoria(int $categoriaId, bool $apenasAtivas = true): Collection
    {
        $query = $this->model->with(['categoria'])
            ->where('categoria_id', $categoriaId)
            ->ordenado();

        if ($apenasAtivas) {
            $query->ativos();
        }

        return $query->get();
    }

    /**
     * Busca contas por natureza
     */
    public function getPorNatureza(NaturezaContaEnum $natureza, bool $apenasAtivas = true): Collection
    {
        $query = $this->model->with(['categoria'])
            ->porNatureza($natureza)
            ->ordenado();

        if ($apenasAtivas) {
            $query->ativos();
        }

        return $query->get();
    }

    /**
     * Gera próximo código automático
     */
    private function gerarProximoCodigo(?int $contaPaiId): string
    {
        $prefixo = '';
        $nivel = 1;

        if ($contaPaiId) {
            $contaPai = $this->model->find($contaPaiId);
            if ($contaPai) {
                $prefixo = $contaPai->codigo . '.';
                $nivel = $contaPai->nivel + 1;
            }
        }

        // Busca último código no mesmo nível
        $ultimoCodigo = $this->model
            ->where('conta_pai_id', $contaPaiId)
            ->where('codigo', 'like', $prefixo . '%')
            ->orderBy('codigo', 'desc')
            ->value('codigo');

        if ($ultimoCodigo) {
            $numeroAtual = intval(str_replace($prefixo, '', $ultimoCodigo));
            $proximoNumero = $numeroAtual + 1;
        } else {
            $proximoNumero = 1;
        }

        $digitos = max(2, strlen((string)$proximoNumero));

        return $prefixo . str_pad($proximoNumero, $digitos, '0', STR_PAD_LEFT);
    }

    /**
     * Valida hierarquia para evitar loops
     */
    private function validarHierarquia(int $contaId, ?int $novoContaPaiId): void
    {
        if (!$novoContaPaiId) {
            return;
        }

        // Verifica se o novo pai é o próprio registro
        if ($contaId === $novoContaPaiId) {
            throw new \Exception('Uma conta não pode ser pai de si mesma');
        }

        // Verifica se o novo pai é um descendente da conta atual
        $contaPai = $this->model->find($novoContaPaiId);

        while ($contaPai) {
            if ($contaPai->conta_pai_id === $contaId) {
                throw new \Exception('Uma conta não pode ter como pai um de seus descendentes');
            }

            $contaPai = $contaPai->contaPai;
        }
    }

    /**
     * Importa plano de contas padrão
     */
    public function importarPlanoContasPadrao(): array
    {
        $contasImportadas = [];

        DB::transaction(function () use (&$contasImportadas) {
            $planoContas = $this->getPlanoContasPadrao();

            foreach ($planoContas as $dados) {
                $dto = ContaGerencialDTO::fromArray($dados);
                $conta = $this->create($dto);
                $contasImportadas[] = $conta;
            }
        });

        return $contasImportadas;
    }

    /**
     * Retorna plano de contas padrão
     */
    private function getPlanoContasPadrao(): array
    {
        return [
            [
                'codigo' => '1',
                'nome' => 'ATIVO',
                'descricao' => 'Bens e direitos da empresa',
                'natureza' => NaturezaContaEnum::DEBITO,
                'nivel' => 1,
                'permite_lancamento' => false,
                'ativo' => true,
            ],
            [
                'codigo' => '2',
                'nome' => 'PASSIVO',
                'descricao' => 'Obrigações da empresa',
                'natureza' => NaturezaContaEnum::CREDITO,
                'nivel' => 1,
                'permite_lancamento' => false,
                'ativo' => true,
            ],
            [
                'codigo' => '3',
                'nome' => 'RECEITAS',
                'descricao' => 'Receitas da empresa',
                'natureza' => NaturezaContaEnum::CREDITO,
                'nivel' => 1,
                'permite_lancamento' => false,
                'ativo' => true,
            ],
            [
                'codigo' => '4',
                'nome' => 'DESPESAS',
                'descricao' => 'Despesas da empresa',
                'natureza' => NaturezaContaEnum::DEBITO,
                'nivel' => 1,
                'permite_lancamento' => false,
                'ativo' => true,
            ],
            // Subcontas de exemplo
            [
                'codigo' => '1.01',
                'nome' => 'Caixa',
                'descricao' => 'Dinheiro em espécie',
                'natureza' => NaturezaContaEnum::DEBITO,
                'nivel' => 2,
                'permite_lancamento' => true,
                'ativo' => true,
            ],
            [
                'codigo' => '1.02',
                'nome' => 'Bancos',
                'descricao' => 'Contas bancárias',
                'natureza' => NaturezaContaEnum::DEBITO,
                'nivel' => 2,
                'permite_lancamento' => true,
                'ativo' => true,
            ],
        ];
    }
}
