<?php

namespace App\Services\Financeiro;

use App\Models\Financeiro\Lancamento;
use App\Models\Financeiro\LancamentoItem;
use App\Models\Financeiro\LancamentoMovimentacao;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;

/**
 * Service para gestão completa de lançamentos financeiros
 * 
 * Centraliza toda a lógica de negócio dos lançamentos:
 * - Criação e edição de lançamentos
 * - Gestão de pagamentos e recebimentos
 * - Parcelamento automático
 * - Recorrência
 * - Workflow de aprovação
 */
class LancamentoService
{
    /**
     * Criar um novo lançamento
     */
    public function criarLancamento(array $dados): Lancamento
    {
        DB::beginTransaction();
        
        try {
            // Preparar dados do lançamento
            $dadosLancamento = $this->prepararDadosLancamento($dados);
            
            // Criar lançamento principal
            $lancamento = Lancamento::create($dadosLancamento);
            
            // Adicionar itens se fornecidos
            if (!empty($dados['itens'])) {
                $this->adicionarItens($lancamento, $dados['itens']);
            }
            
            // Criar parcelas se necessário
            if ($dados['total_parcelas'] > 1) {
                $this->criarParcelas($lancamento, $dados);
            }
            
            // Configurar recorrência se necessário
            if (!empty($dados['e_recorrente'])) {
                $this->configurarRecorrencia($lancamento, $dados);
            }
            
            DB::commit();
            
            return $lancamento->fresh(['itens', 'movimentacoes']);
            
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception("Erro ao criar lançamento: " . $e->getMessage());
        }
    }

    /**
     * Atualizar um lançamento existente
     */
    public function atualizarLancamento(Lancamento $lancamento, array $dados): Lancamento
    {
        DB::beginTransaction();
        
        try {
            // Verificar se pode ser editado
            if (!$this->podeSerEditado($lancamento)) {
                throw new Exception("Lançamento não pode ser editado");
            }
            
            // Preparar dados
            $dadosLancamento = $this->prepararDadosLancamento($dados);
            
            // Atualizar lançamento
            $lancamento->update($dadosLancamento);
            
            // Atualizar itens se fornecidos
            if (isset($dados['itens'])) {
                $this->atualizarItens($lancamento, $dados['itens']);
            }
            
            DB::commit();
            
            return $lancamento->fresh(['itens', 'movimentacoes']);
            
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception("Erro ao atualizar lançamento: " . $e->getMessage());
        }
    }

    /**
     * Registrar pagamento/recebimento
     */
    public function registrarPagamento(Lancamento $lancamento, array $dados): LancamentoMovimentacao
    {
        DB::beginTransaction();
        
        try {
            // Validar dados
            $this->validarDadosPagamento($lancamento, $dados);
            
            // Registrar movimentação
            $movimentacao = $lancamento->adicionarPagamento($dados['valor'], $dados);
            
            // Atualizar parcelas relacionadas se necessário
            if ($lancamento->isParcelado()) {
                $this->atualizarParcelasRelacionadas($lancamento);
            }
            
            DB::commit();
            
            return $movimentacao;
            
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception("Erro ao registrar pagamento: " . $e->getMessage());
        }
    }

    /**
     * Estornar pagamento
     */
    public function estornarPagamento(LancamentoMovimentacao $movimentacao, string $motivo): LancamentoMovimentacao
    {
        DB::beginTransaction();
        
        try {
            if (!$movimentacao->podeSerEstornado()) {
                throw new Exception("Esta movimentação não pode ser estornada");
            }
            
            $lancamento = $movimentacao->lancamento;
            $estorno = $lancamento->estornarPagamento($movimentacao, $motivo);
            
            DB::commit();
            
            return $estorno;
            
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception("Erro ao estornar pagamento: " . $e->getMessage());
        }
    }

    /**
     * Criar parcelas de um lançamento
     */
    public function criarParcelas(Lancamento $lancamentoOriginal, array $dados): Collection
    {
        DB::beginTransaction();
        
        try {
            $totalParcelas = $dados['total_parcelas'];
            $intervaloDias = $dados['intervalo_dias'] ?? 30;
            $grupoParcelas = (string) Str::uuid();
            
            $parcelas = collect();
            
            // Atualizar lançamento original como primeira parcela
            $lancamentoOriginal->update([
                'e_parcelado' => true,
                'parcela_atual' => 1,
                'total_parcelas' => $totalParcelas,
                'grupo_parcelas' => $grupoParcelas,
                'intervalo_dias' => $intervaloDias,
                'valor_bruto' => $dados['valor_bruto'] / $totalParcelas,
            ]);
            
            $lancamentoOriginal->calcularValorLiquido();
            $lancamentoOriginal->save();
            
            $parcelas->push($lancamentoOriginal);
            
            // Criar demais parcelas
            for ($i = 2; $i <= $totalParcelas; $i++) {
                $dataVencimento = Carbon::parse($lancamentoOriginal->data_vencimento)
                                       ->addDays(($i - 1) * $intervaloDias);
                
                $dadosParcela = $lancamentoOriginal->toArray();
                unset($dadosParcela['id'], $dadosParcela['created_at'], $dadosParcela['updated_at']);
                
                $dadosParcela['parcela_atual'] = $i;
                $dadosParcela['data_vencimento'] = $dataVencimento->format('Y-m-d');
                $dadosParcela['numero_documento'] = $lancamentoOriginal->numero_documento . "/{$i}";
                $dadosParcela['descricao'] = $lancamentoOriginal->descricao . " ({$i}/{$totalParcelas})";
                
                $parcela = Lancamento::create($dadosParcela);
                $parcelas->push($parcela);
            }
            
            DB::commit();
            
            return $parcelas;
            
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception("Erro ao criar parcelas: " . $e->getMessage());
        }
    }

    /**
     * Configurar recorrência
     */
    public function configurarRecorrencia(Lancamento $lancamento, array $dados): void
    {
        $proximaData = $this->calcularProximaRecorrencia(
            $lancamento->data_vencimento,
            $dados['frequencia_recorrencia']
        );
        
        $lancamento->update([
            'e_recorrente' => true,
            'frequencia_recorrencia' => $dados['frequencia_recorrencia'],
            'proxima_recorrencia' => $proximaData,
            'recorrencia_ativa' => true,
        ]);
    }

    /**
     * Processar recorrências pendentes
     */
    public function processarRecorrencias(): int
    {
        $lancamentosRecorrentes = Lancamento::where('e_recorrente', true)
                                           ->where('recorrencia_ativa', true)
                                           ->where('proxima_recorrencia', '<=', now()->format('Y-m-d'))
                                           ->get();
        
        $processados = 0;
        
        foreach ($lancamentosRecorrentes as $lancamento) {
            try {
                $this->criarLancamentoRecorrente($lancamento);
                $processados++;
            } catch (Exception $e) {
                // Log do erro, mas continua processando os demais
                logger()->error("Erro ao processar recorrência do lançamento {$lancamento->id}: " . $e->getMessage());
            }
        }
        
        return $processados;
    }

    /**
     * Obter relatório financeiro
     */
    public function obterRelatorioFinanceiro(int $empresaId, array $filtros = []): array
    {
        $query = Lancamento::empresa($empresaId)->ativo();
        
        // Aplicar filtros
        if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])) {
            $query->vencimentoEntre($filtros['data_inicio'], $filtros['data_fim']);
        }
        
        if (!empty($filtros['natureza'])) {
            $query->porNatureza($filtros['natureza']);
        }
        
        if (!empty($filtros['situacao'])) {
            $query->where('situacao_financeira', $filtros['situacao']);
        }
        
        $lancamentos = $query->get();
        
        return [
            'total_lancamentos' => $lancamentos->count(),
            'valor_total' => $lancamentos->sum('valor_liquido'),
            'valor_pago' => $lancamentos->sum('valor_pago'),
            'valor_pendente' => $lancamentos->where('situacao_financeira', Lancamento::SITUACAO_PENDENTE)->sum('valor_saldo'),
            'valor_vencido' => $lancamentos->where('situacao_financeira', Lancamento::SITUACAO_VENCIDO)->sum('valor_saldo'),
            'contas_receber' => $lancamentos->where('natureza_financeira', Lancamento::NATUREZA_ENTRADA)->sum('valor_liquido'),
            'contas_pagar' => $lancamentos->where('natureza_financeira', Lancamento::NATUREZA_SAIDA)->sum('valor_liquido'),
            'por_situacao' => $lancamentos->groupBy('situacao_financeira')->map(function ($group) {
                return [
                    'quantidade' => $group->count(),
                    'valor_total' => $group->sum('valor_liquido'),
                    'valor_saldo' => $group->sum('valor_saldo'),
                ];
            }),
            'por_categoria' => $lancamentos->groupBy('categoria')->map(function ($group) {
                return [
                    'quantidade' => $group->count(),
                    'valor_total' => $group->sum('valor_liquido'),
                ];
            }),
        ];
    }

    /**
     * Métodos privados
     */
    private function prepararDadosLancamento(array $dados): array
    {
        // Valores padrão
        $dadosLancamento = array_merge([
            'data_emissao' => now()->format('Y-m-d'),
            'data_competencia' => now()->format('Y-m-d'),
            'valor_desconto' => 0,
            'valor_acrescimo' => 0,
            'valor_juros' => 0,
            'valor_multa' => 0,
            'valor_pago' => 0,
            'total_parcelas' => 1,
            'intervalo_dias' => 30,
        ], $dados);
        
        // Calcular valor líquido
        $dadosLancamento['valor_liquido'] = $dadosLancamento['valor_bruto'] 
                                          - $dadosLancamento['valor_desconto'] 
                                          + $dadosLancamento['valor_acrescimo'] 
                                          + $dadosLancamento['valor_juros'] 
                                          + $dadosLancamento['valor_multa'];
        
        // Calcular saldo
        $dadosLancamento['valor_saldo'] = $dadosLancamento['valor_liquido'] - $dadosLancamento['valor_pago'];
        
        return $dadosLancamento;
    }

    private function adicionarItens(Lancamento $lancamento, array $itens): void
    {
        foreach ($itens as $item) {
            $item['lancamento_id'] = $lancamento->id;
            $item['empresa_id'] = $lancamento->empresa_id;
            LancamentoItem::create($item);
        }
    }

    private function atualizarItens(Lancamento $lancamento, array $itens): void
    {
        // Remove itens existentes
        $lancamento->itens()->delete();
        
        // Adiciona novos itens
        $this->adicionarItens($lancamento, $itens);
    }

    private function podeSerEditado(Lancamento $lancamento): bool
    {
        // Não pode editar se já foi pago
        if ($lancamento->isPago()) {
            return false;
        }
        
        // Não pode editar se foi cancelado ou estornado
        if (in_array($lancamento->situacao_financeira, [Lancamento::SITUACAO_CANCELADO, Lancamento::SITUACAO_ESTORNADO])) {
            return false;
        }
        
        // Não pode editar se tem pagamentos parciais
        if ($lancamento->valor_pago > 0) {
            return false;
        }
        
        return true;
    }

    private function validarDadosPagamento(Lancamento $lancamento, array $dados): void
    {
        if ($dados['valor'] <= 0) {
            throw new Exception("Valor do pagamento deve ser maior que zero");
        }
        
        if ($dados['valor'] > $lancamento->valor_saldo) {
            throw new Exception("Valor do pagamento não pode ser maior que o saldo devedor");
        }
        
        if ($lancamento->situacao_financeira === Lancamento::SITUACAO_CANCELADO) {
            throw new Exception("Não é possível registrar pagamento em lançamento cancelado");
        }
    }

    private function atualizarParcelasRelacionadas(Lancamento $lancamento): void
    {
        if (!$lancamento->grupo_parcelas) {
            return;
        }
        
        // Atualizar status das demais parcelas do grupo
        Lancamento::where('grupo_parcelas', $lancamento->grupo_parcelas)
                  ->where('id', '!=', $lancamento->id)
                  ->each(function ($parcela) {
                      $parcela->calcularSituacaoFinanceira();
                      $parcela->save();
                  });
    }

    private function calcularProximaRecorrencia(string $dataBase, string $frequencia): string
    {
        $data = Carbon::parse($dataBase);
        
        switch ($frequencia) {
            case 'diaria':
                return $data->addDay()->format('Y-m-d');
            case 'semanal':
                return $data->addWeek()->format('Y-m-d');
            case 'quinzenal':
                return $data->addDays(15)->format('Y-m-d');
            case 'mensal':
                return $data->addMonth()->format('Y-m-d');
            case 'bimestral':
                return $data->addMonths(2)->format('Y-m-d');
            case 'trimestral':
                return $data->addMonths(3)->format('Y-m-d');
            case 'semestral':
                return $data->addMonths(6)->format('Y-m-d');
            case 'anual':
                return $data->addYear()->format('Y-m-d');
            default:
                return $data->addMonth()->format('Y-m-d');
        }
    }

    private function criarLancamentoRecorrente(Lancamento $lancamentoOriginal): Lancamento
    {
        DB::beginTransaction();
        
        try {
            $dadosNovoLancamento = $lancamentoOriginal->toArray();
            unset($dadosNovoLancamento['id'], $dadosNovoLancamento['created_at'], $dadosNovoLancamento['updated_at']);
            
            // Atualizar datas
            $novaDataVencimento = Carbon::parse($lancamentoOriginal->proxima_recorrencia);
            $dadosNovoLancamento['data_vencimento'] = $novaDataVencimento->format('Y-m-d');
            $dadosNovoLancamento['data_emissao'] = now()->format('Y-m-d');
            $dadosNovoLancamento['data_competencia'] = $novaDataVencimento->format('Y-m-d');
            
            // Resetar valores de pagamento
            $dadosNovoLancamento['valor_pago'] = 0;
            $dadosNovoLancamento['situacao_financeira'] = Lancamento::SITUACAO_PENDENTE;
            $dadosNovoLancamento['data_pagamento'] = null;
            $dadosNovoLancamento['data_ultimo_pagamento'] = null;
            
            // Criar novo lançamento
            $novoLancamento = Lancamento::create($dadosNovoLancamento);
            
            // Copiar itens se existirem
            foreach ($lancamentoOriginal->itens as $item) {
                $dadosItem = $item->toArray();
                unset($dadosItem['id'], $dadosItem['created_at'], $dadosItem['updated_at']);
                $dadosItem['lancamento_id'] = $novoLancamento->id;
                LancamentoItem::create($dadosItem);
            }
            
            // Atualizar próxima recorrência do original
            $proximaRecorrencia = $this->calcularProximaRecorrencia(
                $novaDataVencimento->format('Y-m-d'),
                $lancamentoOriginal->frequencia_recorrencia
            );
            
            $lancamentoOriginal->update([
                'proxima_recorrencia' => $proximaRecorrencia
            ]);
            
            DB::commit();
            
            return $novoLancamento;
            
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
