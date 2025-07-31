<?php

namespace App\Repositories\Fidelidade;

use App\Models\Fidelidade\FidelidadeCarteira;
use App\Models\Fidelidade\FidelidadeCashbackRegra;
use App\Models\Fidelidade\FidelidadeCashbackTransacao;
use App\Models\Fidelidade\FidelidadeCupom;
use App\Models\Fidelidade\FidelidadeCupomUso;
use Carbon\Carbon;

class FidelidadeRepository
{
    /**
     * Obter carteira do cliente
     */
    public function obterCarteiraCliente($clienteId, $empresaId = null)
    {
        $query = FidelidadeCarteira::where('cliente_id', $clienteId);

        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        return $query->first();
    }

    /**
     * Obter ou criar carteira do cliente
     */
    public function obterOuCriarCarteira($clienteId, $empresaId = null)
    {
        $carteira = $this->obterCarteiraCliente($clienteId, $empresaId);

        if (!$carteira) {
            $carteira = FidelidadeCarteira::create([
                'cliente_id' => $clienteId,
                'empresa_id' => $empresaId,
                'saldo_cashback' => 0.00,
                'saldo_creditos' => 0.00,
                'saldo_bloqueado' => 0.00,
                'saldo_total_disponivel' => 0.00,
                'nivel_atual' => 'bronze',
                'xp_total' => 0,
                'status' => 'ativa',
                'sync_status' => 'sincronizado'
            ]);
        }

        return $carteira;
    }

    /**
     * Obter regras aplicáveis para um cliente e compra
     */
    public function obterRegrasAplicaveis($carteira, $valorCompra, $categoriaProduto = null)
    {
        $query = FidelidadeCashbackRegra::where('status', 'ativa')
            ->where('data_inicio', '<=', Carbon::now())
            ->where(function ($q) {
                $q->whereNull('data_fim')
                    ->orWhere('data_fim', '>=', Carbon::now());
            });

        // Filtrar por empresa se especificado
        if ($carteira->empresa_id) {
            $query->where(function ($q) use ($carteira) {
                $q->whereNull('empresa_id')
                    ->orWhere('empresa_id', $carteira->empresa_id);
            });
        }

        // Filtrar por categoria se especificado
        if ($categoriaProduto) {
            $query->where(function ($q) use ($categoriaProduto) {
                $q->whereNull('categoria_produto')
                    ->orWhere('categoria_produto', $categoriaProduto);
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Obter estatísticas do programa
     */
    public function obterEstatisticas($periodo = 30)
    {
        $dataInicio = Carbon::now()->subDays($periodo);

        return [
            'carteiras_ativas' => FidelidadeCarteira::where('status', 'ativa')->count(),
            'total_carteiras' => FidelidadeCarteira::count(),
            'total_cashback_disponivel' => FidelidadeCarteira::sum('saldo_cashback'),
            'total_creditos_disponivel' => FidelidadeCarteira::sum('saldo_creditos'),
            'transacoes_periodo' => FidelidadeCashbackTransacao::where('created_at', '>=', $dataInicio)->count(),
            'cashback_distribuido_periodo' => FidelidadeCashbackTransacao::where('tipo_transacao', 'ganho')
                ->where('created_at', '>=', $dataInicio)
                ->sum('valor'),
            'cashback_resgatado_periodo' => FidelidadeCashbackTransacao::where('tipo_transacao', 'resgate')
                ->where('created_at', '>=', $dataInicio)
                ->sum('valor'),
            'clientes_ativos_periodo' => FidelidadeCashbackTransacao::where('created_at', '>=', $dataInicio)
                ->distinct('carteira_id')
                ->count('carteira_id'),
            'ticket_medio' => FidelidadeCashbackTransacao::where('created_at', '>=', $dataInicio)
                ->where('valor_compra', '>', 0)
                ->avg('valor_compra'),
            'por_nivel' => FidelidadeCarteira::selectRaw('nivel_atual, COUNT(*) as total')
                ->groupBy('nivel_atual')
                ->get()
        ];
    }

    /**
     * Validar cupom
     */
    public function validarCupom($codigo, $clienteId, $valorCompra)
    {
        $cupom = FidelidadeCupom::where('codigo', $codigo)->first();

        if (!$cupom) {
            return ['valido' => false, 'erro' => 'Cupom não encontrado'];
        }

        // Verificações básicas
        if ($cupom->status !== 'ativo') {
            return ['valido' => false, 'erro' => 'Cupom inativo'];
        }

        if (Carbon::now()->lt($cupom->data_inicio)) {
            return ['valido' => false, 'erro' => 'Cupom ainda não está válido'];
        }

        if (Carbon::now()->gt($cupom->data_fim)) {
            return ['valido' => false, 'erro' => 'Cupom expirado'];
        }

        if ($cupom->quantidade_maxima && $cupom->quantidade_utilizada >= $cupom->quantidade_maxima) {
            return ['valido' => false, 'erro' => 'Cupom esgotado'];
        }

        if ($cupom->valor_minimo_compra && $valorCompra < $cupom->valor_minimo_compra) {
            return ['valido' => false, 'erro' => "Valor mínimo da compra: R$ {$cupom->valor_minimo_compra}"];
        }

        // Verificar limite por cliente
        if ($cupom->limite_uso_cliente) {
            $usosCliente = FidelidadeCupomUso::where('cupom_id', $cupom->id)
                ->where('cliente_id', $clienteId)
                ->where('status', 'utilizado')
                ->count();

            if ($usosCliente >= $cupom->limite_uso_cliente) {
                return ['valido' => false, 'erro' => 'Limite de uso por cliente excedido'];
            }
        }

        // Verificar nível mínimo
        if ($cupom->nivel_minimo_cliente) {
            $carteira = $this->obterCarteiraCliente($clienteId);
            if (!$carteira || $carteira->nivel_atual < $cupom->nivel_minimo_cliente) {
                return ['valido' => false, 'erro' => 'Nível mínimo não atingido'];
            }
        }

        return ['valido' => true, 'cupom' => $cupom];
    }

    /**
     * Obter transações de uma carteira
     */
    public function obterTransacoesCarteira($carteiraId, $limite = null)
    {
        $query = FidelidadeCashbackTransacao::where('carteira_id', $carteiraId)
            ->orderBy('created_at', 'desc');

        if ($limite) {
            $query->limit($limite);
        }

        return $query->get();
    }

    /**
     * Obter top clientes por saldo
     */
    public function obterTopClientesPorSaldo($limite = 10)
    {
        return FidelidadeCarteira::with('cliente')
            ->where('status', 'ativa')
            ->orderBy('saldo_total_disponivel', 'desc')
            ->limit($limite)
            ->get();
    }

    /**
     * Obter transações recentes
     */
    public function obterTransacoesRecentes($limite = 10)
    {
        return FidelidadeCashbackTransacao::with(['carteira.cliente', 'carteira.empresa'])
            ->orderBy('created_at', 'desc')
            ->limit($limite)
            ->get();
    }

    /**
     * Obter cupons ativos
     */
    public function obterCuponsAtivos()
    {
        return FidelidadeCupom::where('status', 'ativo')
            ->where('data_inicio', '<=', Carbon::now())
            ->where('data_fim', '>=', Carbon::now())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obter dados para gráficos
     */
    public function obterDadosGraficos($periodo = 7)
    {
        $dados = [];

        for ($i = $periodo - 1; $i >= 0; $i--) {
            $data = Carbon::now()->subDays($i);

            $dados[] = [
                'data' => $data->format('Y-m-d'),
                'transacoes' => FidelidadeCashbackTransacao::whereDate('created_at', $data)->count(),
                'valor_cashback' => FidelidadeCashbackTransacao::whereDate('created_at', $data)
                    ->where('tipo_transacao', 'ganho')
                    ->sum('valor'),
                'valor_resgates' => FidelidadeCashbackTransacao::whereDate('created_at', $data)
                    ->where('tipo_transacao', 'resgate')
                    ->sum('valor')
            ];
        }

        return $dados;
    }

    /**
     * Buscar carteiras com filtros
     */
    public function buscarCarteiras($filtros = [], $limite = 20)
    {
        $query = FidelidadeCarteira::with(['cliente', 'empresa']);

        if (isset($filtros['search'])) {
            $query->whereHas('cliente', function ($q) use ($filtros) {
                $q->where('name', 'like', "%{$filtros['search']}%")
                    ->orWhere('email', 'like', "%{$filtros['search']}%");
            });
        }

        if (isset($filtros['status'])) {
            $query->where('status', $filtros['status']);
        }

        if (isset($filtros['nivel'])) {
            $query->where('nivel_atual', $filtros['nivel']);
        }

        if (isset($filtros['empresa_id'])) {
            $query->where('empresa_id', $filtros['empresa_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($limite);
    }
}
