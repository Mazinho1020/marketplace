<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Financial\ContaGerencialController;
use App\Http\Controllers\Financial\CategoriaContaGerencialController;
use App\Http\Controllers\Financial\ContasPagarController;
use App\Http\Controllers\Financial\ContasReceberController;

/*
|--------------------------------------------------------------------------
| Rotas do Sistema Financeiro
|--------------------------------------------------------------------------
|
| Rotas do sistema financeiro integradas no contexto dos comerciantes.
| Cada empresa tem seu próprio conjunto de dados financeiros.
| 
| URLs seguem o padrão: /comerciantes/empresas/{empresa}/financeiro/*
|
*/

// Rotas do Sistema Financeiro dentro do contexto de cada empresa
Route::prefix('comerciantes/empresas/{empresa}/financeiro')->name('comerciantes.empresas.financeiro.')->group(function () {

    // Dashboard Financeiro
    Route::get('/', function ($empresa) {
        return view('comerciantes.financeiro.dashboard', compact('empresa'));
    })->name('dashboard');

    // Rotas das Categorias de Conta
    Route::prefix('categorias')->name('categorias.')->group(function () {
        Route::get('/', [CategoriaContaGerencialController::class, 'index'])->name('index');
        Route::get('/create', [CategoriaContaGerencialController::class, 'create'])->name('create');
        Route::post('/', [CategoriaContaGerencialController::class, 'store'])->name('store');
        Route::get('/{id}', [CategoriaContaGerencialController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [CategoriaContaGerencialController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CategoriaContaGerencialController::class, 'update'])->name('update');
        Route::delete('/{id}', [CategoriaContaGerencialController::class, 'destroy'])->name('destroy');

        // Rotas especiais
        Route::get('/tipo/{tipo}', [CategoriaContaGerencialController::class, 'byType'])->name('by-type');
        Route::get('/api/selecao', [CategoriaContaGerencialController::class, 'forSelection'])->name('for-selection');
        Route::post('/{id}/duplicar', [CategoriaContaGerencialController::class, 'duplicate'])->name('duplicate');
        Route::post('/importar-padrao', [CategoriaContaGerencialController::class, 'importDefault'])->name('import-default');
        Route::get('/api/estatisticas', [CategoriaContaGerencialController::class, 'statistics'])->name('statistics');
    });

    // Rotas das Contas Gerenciais
    Route::prefix('contas')->name('contas.')->group(function () {
        Route::get('/', [ContaGerencialController::class, 'index'])->name('index');
        Route::get('/create', [ContaGerencialController::class, 'create'])->name('create');
        Route::post('/', [ContaGerencialController::class, 'store'])->name('store');
        Route::get('/{id}', [ContaGerencialController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ContaGerencialController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ContaGerencialController::class, 'update'])->name('update');
        Route::delete('/{id}', [ContaGerencialController::class, 'destroy'])->name('destroy');

        // Rotas especiais
        Route::get('/api/hierarquia', [ContaGerencialController::class, 'hierarchy'])->name('hierarchy');
        Route::get('/api/para-lancamento', [ContaGerencialController::class, 'forLaunch'])->name('for-launch');
        Route::get('/categoria/{categoriaId}', [ContaGerencialController::class, 'byCategory'])->name('by-category');
        Route::get('/natureza/{natureza}', [ContaGerencialController::class, 'byNature'])->name('by-nature');
        Route::post('/importar-padrao', [ContaGerencialController::class, 'importDefault'])->name('import-default');
    });

    // Rotas para Contas a Pagar
    Route::prefix('contas-pagar')->name('contas-pagar.')->group(function () {
        Route::get('/', [ContasPagarController::class, 'index'])->name('index');
        Route::get('/create', [ContasPagarController::class, 'create'])->name('create');
        Route::post('/', [ContasPagarController::class, 'store'])->name('store');
        Route::get('/{id}', [ContasPagarController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ContasPagarController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ContasPagarController::class, 'update'])->name('update');
        Route::delete('/{id}', [ContasPagarController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/pagar', [ContasPagarController::class, 'pagar'])->name('pagar');

        // Rotas de pagamentos
        Route::prefix('{id}/pagamentos')->name('pagamentos.')->group(function () {
            Route::post('/', [\App\Http\Controllers\Comerciantes\Financial\PagamentoController::class, 'store'])->name('store');
            Route::get('/resumo', [\App\Http\Controllers\Comerciantes\Financial\PagamentoController::class, 'getSummary'])->name('summary');
            Route::get('/{pagamento}', [\App\Http\Controllers\Comerciantes\Financial\PagamentoController::class, 'show'])->name('show');
            Route::put('/{pagamento}', [\App\Http\Controllers\Comerciantes\Financial\PagamentoController::class, 'update'])->name('update');
            Route::delete('/{pagamento}', [\App\Http\Controllers\Comerciantes\Financial\PagamentoController::class, 'destroy'])->name('destroy');
        });
    });

    // Rotas para Contas a Receber
    Route::prefix('contas-receber')->name('contas-receber.')->group(function () {
        Route::get('/', [ContasReceberController::class, 'index'])->name('index');
        Route::get('/create', [ContasReceberController::class, 'create'])->name('create');
        Route::post('/', [ContasReceberController::class, 'store'])->name('store');
        Route::get('/{id}', [ContasReceberController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ContasReceberController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ContasReceberController::class, 'update'])->name('update');
        Route::delete('/{id}', [ContasReceberController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/gerar-boleto', [ContasReceberController::class, 'gerarBoleto'])->name('gerar-boleto');

        // Rotas para recebimentos (similar às rotas de pagamentos)
        Route::prefix('{id}/recebimentos')->name('recebimentos.')->group(function () {
            Route::get('/pagamento', [\App\Http\Controllers\Comerciantes\Financial\RecebimentoController::class, 'showPagamento'])->name('pagamento');
            Route::post('/', [\App\Http\Controllers\Comerciantes\Financial\RecebimentoController::class, 'store'])->name('store');
            Route::get('/resumo', [\App\Http\Controllers\Comerciantes\Financial\RecebimentoController::class, 'getSummary'])->name('summary');
            Route::get('/{recebimento}', [\App\Http\Controllers\Comerciantes\Financial\RecebimentoController::class, 'show'])->name('show');
            Route::put('/{recebimento}', [\App\Http\Controllers\Comerciantes\Financial\RecebimentoController::class, 'update'])->name('update');
            Route::delete('/{recebimento}', [\App\Http\Controllers\Comerciantes\Financial\RecebimentoController::class, 'destroy'])->name('destroy');
        });
    });

    // Rotas para APIs gerais do financeiro
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/resumo', function () {
            return response()->json(['message' => 'API de resumo financeiro']);
        })->name('resumo');

        Route::get('/relatorios', function () {
            return response()->json(['message' => 'API de relatórios financeiros']);
        })->name('relatorios');

        // Formas de pagamento para uso em formulários
        Route::get('/formas-pagamento', function ($empresa) {
            try {
                $empresaId = (int) $empresa; // $empresa é o ID, não um objeto

                $formasPagamento = DB::table('formas_pagamento')
                    ->where('ativo', true)
                    ->where('empresa_id', $empresaId)
                    ->where('tipo', 'recebimento') // Apenas formas para recebimento
                    ->whereIn('origem', ['sistema']) // Apenas origem sistema
                    ->where('is_gateway', 0) // Excluir formas que são apenas para gateway
                    ->orderBy('nome')
                    ->get(['id', 'nome', 'gateway_method', 'tipo', 'origem']);

                return response()->json($formasPagamento);
            } catch (\Exception $e) {
                Log::error('Erro na API formas-pagamento: ' . $e->getMessage());
                return response()->json(['error' => 'Erro ao carregar formas de pagamento: ' . $e->getMessage()], 500);
            }
        })->name('formas-pagamento');

        // API específica para formas de pagamento (contas a pagar)
        Route::get('/formas-pagamento-saida', function ($empresa) {
            try {
                $empresaId = (int) $empresa; // $empresa é o ID, não um objeto

                $formasPagamento = DB::table('formas_pagamento')
                    ->where('ativo', true)
                    ->where('empresa_id', $empresaId)
                    ->where('tipo', 'pagamento') // Para contas a pagar, apenas formas de pagamento
                    ->whereIn('origem', ['sistema']) // Apenas origem sistema para uso administrativo
                    ->where('is_gateway', 0) // Excluir formas que são apenas para gateway (uso online)
                    ->orderBy('nome')
                    ->get(['id', 'nome', 'gateway_method', 'tipo', 'origem']);

                return response()->json($formasPagamento);
            } catch (\Exception $e) {
                Log::error('Erro na API formas-pagamento-saida: ' . $e->getMessage());
                return response()->json(['error' => 'Erro ao carregar formas de pagamento: ' . $e->getMessage()], 500);
            }
        })->name('formas-pagamento-saida');        // Bandeiras de uma forma de pagamento específica
        Route::get('/formas-pagamento/{formaId}/bandeiras', function ($empresa, $formaId) {
            try {
                $empresaId = (int) $empresa; // $empresa é o ID, não um objeto

                $bandeiras = DB::table('forma_pag_bandeiras as fpb')
                    ->select(['fpb.id', 'fpb.nome', 'fpb.dias_para_receber', 'fpb.taxa'])
                    ->join('forma_pagamento_bandeiras as fpbr', 'fpb.id', '=', 'fpbr.forma_pag_bandeira_id')
                    ->where('fpbr.forma_pagamento_id', $formaId)
                    ->where('fpbr.empresa_id', $empresaId) // Filtrar por empresa também
                    ->where('fpb.ativo', true)
                    ->where('fpb.empresa_id', $empresaId) // Filtrar bandeiras da empresa
                    ->orderBy('fpb.nome')
                    ->get();

                return response()->json($bandeiras);
            } catch (\Exception $e) {
                Log::error('Erro na API bandeiras: ' . $e->getMessage());
                return response()->json(['error' => 'Erro ao carregar bandeiras: ' . $e->getMessage()], 500);
            }
        })->name('formas-pagamento.bandeiras');
    });
});
