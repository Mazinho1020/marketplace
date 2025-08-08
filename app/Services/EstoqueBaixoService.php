<?php

namespace App\Services;

use App\Models\Produto;
use App\Models\ComercianteNotificacao;
use Illuminate\Support\Facades\DB;

class EstoqueBaixoService
{
    /**
     * Verifica produtos com estoque baixo e cria notificações
     */
    public function verificarEstoqueBaixo($empresaId = null)
    {
        $query = Produto::with(['categoria', 'empresa'])
            ->where('controla_estoque', true)
            ->where('ativo', true)
            ->whereRaw('estoque_atual <= estoque_minimo')
            ->where('estoque_minimo', '>', 0);

        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        $produtosEstoqueBaixo = $query->with(['categoria', 'marca'])->get();

        foreach ($produtosEstoqueBaixo as $produto) {
            $this->criarNotificacaoEstoqueBaixo($produto);
        }

        return $produtosEstoqueBaixo;
    }

    /**
     * Cria notificação de estoque baixo para um produto
     */
    public function criarNotificacaoEstoqueBaixo(Produto $produto)
    {
        // Verificar se já existe notificação recente (últimas 24h)
        $notificacaoExistente = ComercianteNotificacao::where('empresa_id', $produto->empresa_id)
            ->where('tipo', 'estoque_baixo')
            ->where('referencia_tipo', 'produto')
            ->where('referencia_id', $produto->id)
            ->where('created_at', '>=', now()->subDay())
            ->first();

        if ($notificacaoExistente) {
            return $notificacaoExistente;
        }

        $dados = [
            'produto_id' => $produto->id,
            'produto_nome' => $produto->nome,
            'produto_sku' => $produto->sku,
            'quantidade_atual' => $produto->estoque_atual,
            'estoque_minimo' => $produto->estoque_minimo,
            'categoria' => $produto->categoria?->nome,
            'marca' => $produto->marca?->nome
        ];

        return ComercianteNotificacao::create([
            'empresa_id' => $produto->empresa_id,
            'tipo' => 'estoque_baixo',
            'titulo' => 'Estoque Baixo: ' . $produto->nome,
            'mensagem' => $this->montarMensagemEstoqueBaixo($produto),
            'dados' => json_encode($dados),
            'referencia_tipo' => 'produto',
            'referencia_id' => $produto->id,
            'prioridade' => 'alta',
            'lida' => false
        ]);
    }

    /**
     * Monta mensagem de notificação de estoque baixo
     */
    private function montarMensagemEstoqueBaixo(Produto $produto)
    {
        $categoria = $produto->categoria ? " ({$produto->categoria->nome})" : '';

        return "O produto \"{$produto->nome}\"{$categoria} está com estoque baixo. " .
            "Quantidade atual: {$produto->estoque_atual} | " .
            "Estoque mínimo: {$produto->estoque_minimo}. " .
            "Recomendamos reabastecer o estoque.";
    }

    /**
     * Verifica produtos com estoque zerado
     */
    public function verificarEstoqueZerado($empresaId = null)
    {
        $query = Produto::where('controla_estoque', true)
            ->where('ativo', true)
            ->where('estoque_atual', '<=', 0);

        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        $produtosEstoqueZerado = $query->with(['categoria', 'marca'])->get();

        foreach ($produtosEstoqueZerado as $produto) {
            $this->criarNotificacaoEstoqueZerado($produto);
        }

        return $produtosEstoqueZerado;
    }

    /**
     * Cria notificação de estoque zerado
     */
    public function criarNotificacaoEstoqueZerado(Produto $produto)
    {
        // Verificar se já existe notificação recente (últimas 12h)
        $notificacaoExistente = ComercianteNotificacao::where('empresa_id', $produto->empresa_id)
            ->where('tipo', 'estoque_zerado')
            ->where('referencia_tipo', 'produto')
            ->where('referencia_id', $produto->id)
            ->where('created_at', '>=', now()->subHours(12))
            ->first();

        if ($notificacaoExistente) {
            return $notificacaoExistente;
        }

        $dados = [
            'produto_id' => $produto->id,
            'produto_nome' => $produto->nome,
            'produto_sku' => $produto->sku,
            'quantidade_atual' => $produto->estoque_atual,
            'categoria' => $produto->categoria?->nome,
            'marca' => $produto->marca?->nome
        ];

        return ComercianteNotificacao::create([
            'empresa_id' => $produto->empresa_id,
            'tipo' => 'estoque_zerado',
            'titulo' => 'Estoque Esgotado: ' . $produto->nome,
            'mensagem' => $this->montarMensagemEstoqueZerado($produto),
            'dados' => json_encode($dados),
            'referencia_tipo' => 'produto',
            'referencia_id' => $produto->id,
            'prioridade' => 'critica',
            'lida' => false
        ]);
    }

    /**
     * Monta mensagem de notificação de estoque zerado
     */
    private function montarMensagemEstoqueZerado(Produto $produto)
    {
        $categoria = $produto->categoria ? " ({$produto->categoria->nome})" : '';

        return "O produto \"{$produto->nome}\"{$categoria} está com estoque ESGOTADO! " .
            "O produto não está mais disponível para venda. " .
            "É necessário reabastecer urgentemente.";
    }

    /**
     * Obtém relatório de produtos com problemas de estoque
     */
    public function relatorioProblemasEstoque($empresaId = null)
    {
        $query = Produto::where('controla_estoque', true)
            ->where('ativo', true);

        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        $produtos = $query->with(['categoria', 'marca'])->get();

        $relatorio = [
            'estoque_zerado' => $produtos->where('estoque_atual', '<=', 0)->values(),
            'estoque_baixo' => $produtos->filter(function ($produto) {
                return $produto->estoque_atual > 0 &&
                    $produto->estoque_minimo > 0 &&
                    $produto->estoque_atual <= $produto->estoque_minimo;
            })->values(),
            'estoque_critico' => $produtos->filter(function ($produto) {
                return $produto->estoque_atual > 0 &&
                    $produto->estoque_minimo > 0 &&
                    $produto->estoque_atual <= ($produto->estoque_minimo * 0.5);
            })->values(),
            'estoque_normal' => $produtos->filter(function ($produto) {
                return !$produto->estoque_minimo ||
                    $produto->estoque_atual > $produto->estoque_minimo;
            })->values()
        ];

        $relatorio['resumo'] = [
            'total_produtos' => $produtos->count(),
            'total_zerado' => $relatorio['estoque_zerado']->count(),
            'total_baixo' => $relatorio['estoque_baixo']->count(),
            'total_critico' => $relatorio['estoque_critico']->count(),
            'total_normal' => $relatorio['estoque_normal']->count(),
            'percentual_problemas' => $produtos->count() > 0 ?
                round((($relatorio['estoque_zerado']->count() + $relatorio['estoque_baixo']->count()) / $produtos->count()) * 100, 2) : 0
        ];

        return $relatorio;
    }

    /**
     * Executa verificação completa de estoque com criação de notificações
     */
    public function executarVerificacaoCompleta($empresaId = null)
    {
        $produtosBaixo = $this->verificarEstoqueBaixo($empresaId);
        $produtosZerado = $this->verificarEstoqueZerado($empresaId);

        $notificacoesCriadas = 0;

        // Criar notificações para produtos com estoque baixo
        foreach ($produtosBaixo as $produto) {
            if ($this->criarNotificacaoEstoqueBaixo($produto)) {
                $notificacoesCriadas++;
            }
        }

        // Criar notificações para produtos esgotados
        foreach ($produtosZerado as $produto) {
            if ($this->criarNotificacaoEstoqueEsgotado($produto)) {
                $notificacoesCriadas++;
            }
        }

        $resultados = [
            'produtos_estoque_baixo' => $produtosBaixo,
            'produtos_estoque_zerado' => $produtosZerado,
            'total_notificacoes_criadas' => $notificacoesCriadas,
            'relatorio' => $this->relatorioProblemasEstoque($empresaId)
        ];

        return $resultados;
    }

    /**
     * Cria notificação para produto com estoque esgotado
     */
    private function criarNotificacaoEstoqueEsgotado(Produto $produto): bool
    {
        // Verifica se já existe notificação recente para este produto
        $notificacaoExistente = ComercianteNotificacao::where('empresa_id', $produto->empresa_id)
            ->where('tipo', 'estoque_zerado')
            ->where('dados->produto_id', $produto->id)
            ->where('created_at', '>=', now()->subHours(24))
            ->exists();

        if ($notificacaoExistente) {
            return false;
        }

        ComercianteNotificacao::create([
            'empresa_id' => $produto->empresa_id,
            'tipo' => 'estoque_zerado',
            'titulo' => 'Produto Esgotado',
            'mensagem' => "O produto '{$produto->nome}' está com estoque esgotado.",
            'dados' => [
                'produto_id' => $produto->id,
                'produto_nome' => $produto->nome,
                'produto_sku' => $produto->sku,
                'quantidade_atual' => $produto->estoque_atual,
                'categoria' => $produto->categoria->nome ?? 'N/A'
            ],
            'prioridade' => 'alta',
            'lida' => false
        ]);

        return true;
    }

    /**
     * Marca notificações de estoque como lidas
     */
    public function marcarNotificacoesComoLidas($empresaId, array $tiposNotificacao = ['estoque_baixo', 'estoque_zerado'])
    {
        return ComercianteNotificacao::where('empresa_id', $empresaId)
            ->whereIn('tipo', $tiposNotificacao)
            ->where('lida', false)
            ->update(['lida' => true, 'lida_em' => now()]);
    }

    /**
     * Remove notificações antigas de estoque baixo
     */
    public function limparNotificacoesAntigas(int $diasAtras = 7): int
    {
        return ComercianteNotificacao::where('tipo', 'estoque_baixo')
            ->where('created_at', '<', now()->subDays($diasAtras))
            ->delete();
    }
}
