<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Financeiro\LancamentoController;

/*
|--------------------------------------------------------------------------
| Rotas da API - Sistema de Lançamentos Financeiros
|--------------------------------------------------------------------------
|
| Rotas completas para gestão de lançamentos financeiros:
| - CRUD de lançamentos
| - Gestão de pagamentos/recebimentos
| - Relatórios e dashboard
| - Workflow de aprovação
|
*/

Route::prefix('financeiro')->name('financeiro.')->group(function () {
    
    // Rotas principais de lançamentos
    Route::apiResource('lancamentos', LancamentoController::class);
    
    // Rotas específicas para lançamentos
    Route::prefix('lancamentos')->name('lancamentos.')->group(function () {
        
        // Pagamentos e recebimentos
        Route::post('{lancamento}/pagamento', [LancamentoController::class, 'registrarPagamento'])
             ->name('registrar-pagamento');
        
        // Estorno de movimentações
        Route::post('movimentacoes/{movimentacao}/estorno', [LancamentoController::class, 'estornarPagamento'])
             ->name('estornar-pagamento');
        
        // Parcelamento
        Route::post('{lancamento}/parcelas', [LancamentoController::class, 'criarParcelas'])
             ->name('criar-parcelas');
        
        // Workflow de aprovação
        Route::post('{lancamento}/aprovar', [LancamentoController::class, 'aprovar'])
             ->name('aprovar');
        
        Route::post('{lancamento}/rejeitar', [LancamentoController::class, 'rejeitar'])
             ->name('rejeitar');
        
        Route::post('{lancamento}/cancelar', [LancamentoController::class, 'cancelar'])
             ->name('cancelar');
        
        // Consultas específicas
        Route::get('vencidos', [LancamentoController::class, 'vencidos'])
             ->name('vencidos');
        
        // Relatórios
        Route::get('relatorio-financeiro', [LancamentoController::class, 'relatorioFinanceiro'])
             ->name('relatorio-financeiro');
        
        // Dashboard
        Route::get('dashboard', [LancamentoController::class, 'dashboard'])
             ->name('dashboard');
    });
});

/*
|--------------------------------------------------------------------------
| Exemplos de uso das rotas
|--------------------------------------------------------------------------
|
| GET /api/financeiro/lancamentos
| - Lista todos os lançamentos com filtros
| - Parâmetros: empresa_id, natureza, situacao, data_inicio, data_fim, etc.
|
| POST /api/financeiro/lancamentos
| - Cria um novo lançamento
| - Body: dados do lançamento conforme LancamentoRequest
|
| GET /api/financeiro/lancamentos/{id}
| - Obtém um lançamento específico com itens e movimentações
|
| PUT /api/financeiro/lancamentos/{id}
| - Atualiza um lançamento existente
|
| DELETE /api/financeiro/lancamentos/{id}
| - Exclui um lançamento (soft delete)
|
| POST /api/financeiro/lancamentos/{id}/pagamento
| - Registra um pagamento/recebimento
| - Body: { valor, data_movimentacao, forma_pagamento_id, observacoes }
|
| POST /api/financeiro/lancamentos/movimentacoes/{id}/estorno
| - Estorna uma movimentação
| - Body: { motivo }
|
| POST /api/financeiro/lancamentos/{id}/parcelas
| - Cria parcelas de um lançamento
| - Body: { total_parcelas, intervalo_dias }
|
| POST /api/financeiro/lancamentos/{id}/aprovar
| - Aprova um lançamento
| - Body: { observacoes }
|
| POST /api/financeiro/lancamentos/{id}/rejeitar
| - Rejeita um lançamento
| - Body: { motivo }
|
| POST /api/financeiro/lancamentos/{id}/cancelar
| - Cancela um lançamento
| - Body: { motivo }
|
| GET /api/financeiro/lancamentos/vencidos
| - Lista lançamentos vencidos
| - Parâmetros: empresa_id, dias (em atraso)
|
| GET /api/financeiro/lancamentos/relatorio-financeiro
| - Gera relatório financeiro
| - Parâmetros: empresa_id, data_inicio, data_fim, natureza, situacao
|
| GET /api/financeiro/lancamentos/dashboard
| - Dashboard financeiro com totais e estatísticas
| - Parâmetros: empresa_id
|
*/
