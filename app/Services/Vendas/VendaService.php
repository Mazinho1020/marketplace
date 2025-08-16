<?php

namespace App\Services\Vendas;

use App\Models\Vendas\Venda;
use App\Models\Fidelidade\FidelidadeCupom;
use App\Models\Fidelidade\FidelidadeCarteira;
use Illuminate\Support\Facades\DB;

/**
 * Service principal para gestão de vendas
 * 
 * Centraliza a lógica de negócio para vendas, incluindo:
 * - Integração com fidelidade
 * - Cálculos de cashback
 * - Aplicação de cupons
 * - Workflow de vendas
 */
class VendaService
{
    /**
     * Cria uma nova venda com todas as integrações
     */
    public function criarVenda(array $dadosVenda): Venda
    {
        DB::beginTransaction();

        try {
            // 1. Criar venda básica
            $venda = Venda::create([
                'empresa_id' => $dadosVenda['empresa_id'],
                'usuario_id' => $dadosVenda['usuario_id'],
                'pessoa_id' => $dadosVenda['pessoa_id'],
                'pessoa_tipo' => 'cliente',
                'valor_bruto' => $dadosVenda['valor_bruto'],
                'valor_desconto' => $dadosVenda['valor_desconto'] ?? 0,
                'canal_venda' => $dadosVenda['canal_venda'],
                'tipo_entrega' => $dadosVenda['tipo_entrega'],
                'data_entrega_prevista' => $dadosVenda['data_entrega_prevista'] ?? null,
                'descricao' => $dadosVenda['descricao'],
                'data_emissao' => now(),
                'data_competencia' => now()->format('Y-m-d'),
                'data_vencimento' => now()->addDays(30)->format('Y-m-d'),
                'situacao_financeira' => Venda::STATUS_RASCUNHO,
                'prioridade' => $dadosVenda['prioridade'] ?? 'normal',
                'observacoes' => $dadosVenda['observacoes'] ?? null,
            ]);

            // 2. Adicionar itens
            if (isset($dadosVenda['itens']) && is_array($dadosVenda['itens'])) {
                foreach ($dadosVenda['itens'] as $item) {
                    $venda->itens()->create([
                        'produto_id' => $item['produto_id'],
                        'quantidade' => $item['quantidade'],
                        'valor_unitario' => $item['valor_unitario'],
                        'valor_total' => $item['quantidade'] * $item['valor_unitario'],
                        'descricao' => $item['descricao'] ?? '',
                    ]);
                }
            }

            // 3. Aplicar cupom de fidelidade se fornecido
            if (isset($dadosVenda['cupom_codigo'])) {
                $this->aplicarCupomFidelidade($venda, $dadosVenda['cupom_codigo']);
            }

            // 4. Calcular cashback e pontos
            $this->calcularBeneficiosFidelidade($venda);

            DB::commit();

            return $venda->fresh(['cliente', 'itens', 'cupomFidelidade']);

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Confirma uma venda e aplica todas as integrações
     */
    public function confirmarVenda(Venda $venda, array $dadosConfirmacao = []): bool
    {
        DB::beginTransaction();

        try {
            // 1. Confirmar venda
            $sucesso = $venda->confirmar($dadosConfirmacao);

            if (!$sucesso) {
                throw new \Exception('Não foi possível confirmar a venda');
            }

            // 2. Baixar estoque (se aplicável)
            $this->baixarEstoque($venda);

            // 3. Aplicar pontos e cashback na carteira do cliente
            $this->aplicarBeneficiosFidelidade($venda);

            // 4. Enviar notificações
            $this->enviarNotificacoes($venda, 'venda_confirmada');

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Aplica cupom de fidelidade na venda
     */
    public function aplicarCupomFidelidade(Venda $venda, string $codigoCupom): bool
    {
        try {
            $cupom = FidelidadeCupom::where('codigo', $codigoCupom)
                ->where('status', 'ativo')
                ->where('empresa_id', $venda->empresa_id)
                ->first();

            if (!$cupom) {
                throw new \Exception('Cupom não encontrado ou inválido');
            }

            // Validar cupom (você pode expandir essas validações)
            $validacao = $this->validarCupom($cupom, $venda);
            
            if (!$validacao['valido']) {
                throw new \Exception($validacao['erro']);
            }

            // Aplicar desconto
            $desconto = $this->calcularDescontoCupom($cupom, $venda);
            
            $venda->update([
                'cupom_fidelidade_id' => $cupom->id,
                'valor_desconto' => $venda->valor_desconto + $desconto,
            ]);

            return true;

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Calcula benefícios de fidelidade (cashback e pontos)
     */
    private function calcularBeneficiosFidelidade(Venda $venda): void
    {
        // Calcular cashback (2% do valor líquido, por exemplo)
        $cashbackGerado = $venda->getValorLiquidoAttribute() * 0.02;
        
        // Calcular pontos (1 ponto por real, por exemplo)
        $pontosGerados = intval($venda->getValorLiquidoAttribute());

        $venda->update([
            'cashback_gerado' => $cashbackGerado,
            'pontos_gerados' => $pontosGerados,
        ]);
    }

    /**
     * Aplica benefícios na carteira do cliente após confirmação
     */
    private function aplicarBeneficiosFidelidade(Venda $venda): void
    {
        if ($venda->cashback_gerado > 0 || $venda->pontos_gerados > 0) {
            // Buscar ou criar carteira do cliente
            $carteira = FidelidadeCarteira::firstOrCreate(
                [
                    'empresa_id' => $venda->empresa_id,
                    'cliente_id' => $venda->pessoa_id,
                ],
                [
                    'saldo_pontos' => 0,
                    'saldo_cashback' => 0,
                    'total_pontos_ganhos' => 0,
                    'total_cashback_ganho' => 0,
                ]
            );

            // Adicionar benefícios
            $carteira->increment('saldo_pontos', $venda->pontos_gerados);
            $carteira->increment('saldo_cashback', $venda->cashback_gerado);
            $carteira->increment('total_pontos_ganhos', $venda->pontos_gerados);
            $carteira->increment('total_cashback_ganho', $venda->cashback_gerado);
        }
    }

    /**
     * Baixa estoque dos produtos da venda
     */
    private function baixarEstoque(Venda $venda): void
    {
        foreach ($venda->itens as $item) {
            // Verificar se o produto controla estoque
            $produto = $item->produto;
            
            if ($produto && $produto->controla_estoque) {
                // Baixar estoque (implementar conforme sua lógica de estoque)
                $produto->decrement('quantidade_estoque', $item->quantidade);
                
                // Criar movimentação de estoque
                $produto->movimentacoes()->create([
                    'tipo' => 'saida',
                    'quantidade' => $item->quantidade,
                    'motivo' => 'venda',
                    'referencia_id' => $venda->id,
                    'usuario_id' => $venda->usuario_id,
                ]);
            }
        }
    }

    /**
     * Envia notificações relacionadas à venda
     */
    private function enviarNotificacoes(Venda $venda, string $evento): void
    {
        // Integrar com sistema de notificações existente
        // Implementar conforme seu sistema de notificações
        
        /*
        $notificacaoService = app(\App\Services\NotificacaoService::class);
        
        $notificacaoService->sendEvent($evento, [
            'venda_id' => $venda->id,
            'numero_venda' => $venda->numero_venda,
            'cliente_nome' => $venda->cliente->nome,
            'valor_total' => $venda->getValorLiquidoAttribute(),
        ], [
            'empresa_id' => $venda->empresa_id,
            'cliente_id' => $venda->pessoa_id,
        ]);
        */
    }

    /**
     * Valida um cupom para uma venda
     */
    private function validarCupom(FidelidadeCupom $cupom, Venda $venda): array
    {
        // Verificar validade temporal
        if ($cupom->data_inicio && now() < $cupom->data_inicio) {
            return ['valido' => false, 'erro' => 'Cupom ainda não é válido'];
        }

        if ($cupom->data_fim && now() > $cupom->data_fim) {
            return ['valido' => false, 'erro' => 'Cupom expirado'];
        }

        // Verificar valor mínimo
        if ($cupom->valor_minimo_compra && $venda->valor_bruto < $cupom->valor_minimo_compra) {
            return ['valido' => false, 'erro' => "Valor mínimo da compra: R$ " . number_format($cupom->valor_minimo_compra, 2, ',', '.')];
        }

        // Verificar quantidade máxima
        if ($cupom->quantidade_maxima && $cupom->quantidade_utilizada >= $cupom->quantidade_maxima) {
            return ['valido' => false, 'erro' => 'Cupom esgotado'];
        }

        return ['valido' => true];
    }

    /**
     * Calcula desconto do cupom
     */
    private function calcularDescontoCupom(FidelidadeCupom $cupom, Venda $venda): float
    {
        if ($cupom->tipo_desconto === 'percentual') {
            return ($venda->valor_bruto * $cupom->valor_desconto) / 100;
        } else {
            return min($cupom->valor_desconto, $venda->valor_bruto);
        }
    }

    /**
     * Retorna estatísticas de vendas
     */
    public function obterEstatisticas(int $empresaId, string $periodo = 'mes'): array
    {
        $dataInicio = match($periodo) {
            'hoje' => now()->startOfDay(),
            'semana' => now()->startOfWeek(),
            'mes' => now()->startOfMonth(),
            'ano' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        $vendas = Venda::vendas()->empresa($empresaId)->where('created_at', '>=', $dataInicio);

        return [
            'total_vendas' => $vendas->count(),
            'valor_total' => $vendas->sum('valor_bruto'),
            'valor_liquido_total' => $vendas->get()->sum(function($venda) {
                return $venda->getValorLiquidoAttribute();
            }),
            'ticket_medio' => $vendas->avg('valor_bruto'),
            'cashback_gerado' => $vendas->sum('cashback_gerado'),
            'pontos_gerados' => $vendas->sum('pontos_gerados'),
            'vendas_por_canal' => $vendas->groupBy('canal_venda')
                                       ->selectRaw('canal_venda, COUNT(*) as total')
                                       ->pluck('total', 'canal_venda'),
            'vendas_por_status' => $vendas->groupBy('situacao_financeira')
                                         ->selectRaw('situacao_financeira, COUNT(*) as total')
                                         ->pluck('total', 'situacao_financeira'),
        ];
    }
}