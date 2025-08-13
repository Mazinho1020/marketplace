<?php

namespace App\Services\Financial;

use App\Models\Financial\CategoriaContaGerencial;
use App\DTOs\Financial\CategoriaContaGerencialDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoriaContaGerencialService
{
    public function __construct(
        private CategoriaContaGerencial $model
    ) {}

    /**
     * Lista todas as categorias com paginação
     */
    public function index(array $filtros = []): LengthAwarePaginator
    {
        $query = $this->model->with(['contasGerenciais'])
            ->ordenado();

        // Filtro por empresa (obrigatório)
        if (!empty($filtros['empresa_id'])) {
            $query->where('empresa_id', $filtros['empresa_id']);
        }

        // Filtros
        if (!empty($filtros['nome'])) {
            $query->buscar($filtros['nome'], ['nome', 'nome_completo', 'descricao']);
        }

        if (!empty($filtros['ativo'])) {
            $query->where('ativo', $filtros['ativo'] === 'true');
        }

        if (!empty($filtros['tipo'])) {
            match ($filtros['tipo']) {
                'custo' => $query->custos(),
                'despesa' => $query->despesas(),
                'receita' => $query->receitas(),
                default => null
            };
        }

        return $query->paginate(15);
    }

    /**
     * Busca categoria por ID
     */
    public function find(int $id): ?CategoriaContaGerencial
    {
        return $this->model->with(['contasGerenciais'])->find($id);
    }

    /**
     * Cria nova categoria
     */
    public function create(CategoriaContaGerencialDTO $dto): CategoriaContaGerencial
    {
        return DB::transaction(function () use ($dto) {
            $data = $dto->toArrayWithoutNulls();

            // Se nome_completo não foi informado, usar nome
            if (empty($data['nome_completo'])) {
                $data['nome_completo'] = $data['nome'];
            }

            $categoria = $this->model->create($data);
            $categoria->load(['contasGerenciais']);

            return $categoria;
        });
    }

    /**
     * Atualiza categoria existente
     */
    public function update(int $id, CategoriaContaGerencialDTO $dto): CategoriaContaGerencial
    {
        return DB::transaction(function () use ($id, $dto) {
            $categoria = $this->find($id);

            if (!$categoria) {
                throw new \Exception('Categoria não encontrada');
            }

            $data = $dto->toArrayWithoutNulls();

            // Se nome_completo não foi informado, usar nome
            if (empty($data['nome_completo']) && !empty($data['nome'])) {
                $data['nome_completo'] = $data['nome'];
            }

            $categoria->update($data);
            $categoria->load(['contasGerenciais']);

            return $categoria;
        });
    }

    /**
     * Remove categoria
     */
    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $categoria = $this->find($id);

            if (!$categoria) {
                throw new \Exception('Categoria não encontrada');
            }

            if ($categoria->contasGerenciais()->count() > 0) {
                throw new \Exception('Categoria não pode ser excluída pois possui contas vinculadas');
            }

            return $categoria->delete();
        });
    }

    /**
     * Lista categorias por tipo
     */
    public function getPorTipo(string $tipo, bool $apenasAtivas = true): Collection
    {
        $query = $this->model->ordenado();

        if ($apenasAtivas) {
            $query->ativos();
        }

        return match ($tipo) {
            'custo' => $query->custos()->get(),
            'despesa' => $query->despesas()->get(),
            'receita' => $query->receitas()->get(),
            default => $query->get()
        };
    }

    /**
     * Lista todas as categorias ativas para seleção
     */
    public function getParaSelecao(int $empresaId = null): Collection
    {
        $query = $this->model->ativos()->ordenado();

        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        return $query->select('id', 'nome', 'nome_completo', 'cor', 'icone', 'e_custo', 'e_despesa', 'e_receita')
            ->get();
    }

    /**
     * Duplica categoria existente
     */
    public function duplicar(int $id, string $novoNome): CategoriaContaGerencial
    {
        return DB::transaction(function () use ($id, $novoNome) {
            $categoriaOriginal = $this->find($id);

            if (!$categoriaOriginal) {
                throw new \Exception('Categoria não encontrada');
            }

            $dadosNova = $categoriaOriginal->toArray();
            unset($dadosNova['id'], $dadosNova['created_at'], $dadosNova['updated_at']);

            $dadosNova['nome'] = $novoNome;
            $dadosNova['nome_completo'] = $novoNome;

            $dto = CategoriaContaGerencialDTO::fromArray($dadosNova);

            return $this->create($dto);
        });
    }

    /**
     * Importa categorias padrão
     */
    public function importarCategoriasPadrao(): array
    {
        $categoriasImportadas = [];

        DB::transaction(function () use (&$categoriasImportadas) {
            $categoriasPadrao = $this->getCategoriasPadrao();

            foreach ($categoriasPadrao as $dados) {
                // Verifica se já existe categoria com o mesmo nome
                $existe = $this->model->where('nome', $dados['nome'])->exists();

                if (!$existe) {
                    $dto = CategoriaContaGerencialDTO::fromArray($dados);
                    $categoria = $this->create($dto);
                    $categoriasImportadas[] = $categoria;
                }
            }
        });

        return $categoriasImportadas;
    }

    /**
     * Retorna categorias padrão
     */
    private function getCategoriasPadrao(): array
    {
        return [
            // Receitas
            [
                'nome' => 'Vendas',
                'nome_completo' => 'Receita de Vendas',
                'descricao' => 'Receitas provenientes de vendas de produtos ou serviços',
                'cor' => '#28a745',
                'icone' => 'trending-up',
                'e_receita' => true,
                'ativo' => true,
            ],
            [
                'nome' => 'Outras Receitas',
                'nome_completo' => 'Outras Receitas Operacionais',
                'descricao' => 'Outras receitas operacionais da empresa',
                'cor' => '#20c997',
                'icone' => 'plus-circle',
                'e_receita' => true,
                'ativo' => true,
            ],

            // Despesas
            [
                'nome' => 'Despesas Fixas',
                'nome_completo' => 'Despesas Fixas Operacionais',
                'descricao' => 'Despesas que não variam com o volume de vendas',
                'cor' => '#dc3545',
                'icone' => 'minus-circle',
                'e_despesa' => true,
                'ativo' => true,
            ],
            [
                'nome' => 'Despesas Variáveis',
                'nome_completo' => 'Despesas Variáveis Operacionais',
                'descricao' => 'Despesas que variam conforme o volume de vendas',
                'cor' => '#fd7e14',
                'icone' => 'trending-down',
                'e_despesa' => true,
                'ativo' => true,
            ],
            [
                'nome' => 'Despesas Administrativas',
                'nome_completo' => 'Despesas Administrativas',
                'descricao' => 'Despesas relacionadas à administração da empresa',
                'cor' => '#6f42c1',
                'icone' => 'briefcase',
                'e_despesa' => true,
                'ativo' => true,
            ],

            // Custos
            [
                'nome' => 'Custos Fixos',
                'nome_completo' => 'Custos Fixos de Produção',
                'descricao' => 'Custos que não variam com a produção',
                'cor' => '#ffc107',
                'icone' => 'settings',
                'e_custo' => true,
                'ativo' => true,
            ],
            [
                'nome' => 'Custos Variáveis',
                'nome_completo' => 'Custos Variáveis de Produção',
                'descricao' => 'Custos que variam conforme a produção',
                'cor' => '#ff851b',
                'icone' => 'activity',
                'e_custo' => true,
                'ativo' => true,
            ],
            [
                'nome' => 'Matéria-Prima',
                'nome_completo' => 'Custos de Matéria-Prima',
                'descricao' => 'Custos relacionados à matéria-prima utilizada',
                'cor' => '#795548',
                'icone' => 'package',
                'e_custo' => true,
                'ativo' => true,
            ],
        ];
    }

    /**
     * Obtém estatísticas das categorias
     */
    public function getEstatisticas(): array
    {
        return [
            'total' => $this->model->count(),
            'ativas' => $this->model->ativos()->count(),
            'custos' => $this->model->custos()->count(),
            'despesas' => $this->model->despesas()->count(),
            'receitas' => $this->model->receitas()->count(),
        ];
    }
}
